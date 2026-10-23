<?php

namespace Webkul\Crm\Services;

use Illuminate\Support\Facades\DB;
use Webkul\Crm\Models\Lead;
use Webkul\Crm\Models\Stage;
use Webkul\Crm\Models\Tag;
use Webkul\Crm\Settings\CrmSettings;
use Webkul\Sale\Models\Team;
use Webkul\Support\Models\Country;
use Webkul\Support\Models\State;
use Webkul\Support\Models\UTMSource;

class LeadScoring
{
    public const VARIABLES = ['country_id', 'state_id', 'phone', 'email', 'utm_source_id', 'tags'];

    public const TOTAL = '__total';

    protected const SMOOTHING = 0.1;

    protected array $frequencies = [];

    protected static bool $refreshScheduled = false;

    public function enabledVariables(): array
    {
        return array_values(array_intersect(self::VARIABLES, (array) app(CrmSettings::class)->scoring_variables));
    }

    public function rebuild(): void
    {
        $startDate = app(CrmSettings::class)->scoring_start_date;
        $variables = $this->enabledVariables();
        $stages = Stage::query()->orderBy('sort')->get(['id', 'sort']);
        $counts = [];

        $add = function (int $teamId, string $variable, string $value, bool $won) use (&$counts): void {
            $key = $teamId.'|'.$variable.'|'.$value;
            $counts[$key] ??= [$teamId, $variable, $value, 0, 0];
            $counts[$key][$won ? 3 : 4]++;
        };

        Lead::query()
            ->withoutGlobalScopes()
            ->withTrashed()
            ->when(filled($startDate), fn ($query) => $query->whereDate('created_at', '>=', $startDate))
            ->where(fn ($query) => $query
                ->whereNotNull('deleted_at')
                ->orWhereHas('stage', fn ($query) => $query->where('is_won', true)))
            ->with(['stage', 'tags'])
            ->chunkById(500, function ($leads) use ($add, $stages, $variables): void {
                foreach ($leads as $lead) {
                    $won = ! $lead->trashed() && (bool) $lead->stage?->is_won;
                    $teamId = (int) $lead->team_id;

                    $add($teamId, self::TOTAL, '', $won);

                    foreach ($stages as $stage) {
                        if ($won || ($lead->stage && $stage->sort <= $lead->stage->sort)) {
                            $add($teamId, 'stage', (string) $stage->id, $won);
                        }
                    }

                    foreach ($this->leadValues($lead, $variables) as [$variable, $value]) {
                        $add($teamId, $variable, $value, $won);
                    }
                }
            });

        DB::transaction(function () use ($counts): void {
            DB::table('crm_lead_scoring_frequencies')->delete();

            foreach (array_chunk(array_values($counts), 500) as $chunk) {
                DB::table('crm_lead_scoring_frequencies')->insert(array_map(fn (array $row): array => [
                    'team_id'    => $row[0] ?: null,
                    'variable'   => $row[1],
                    'value'      => $row[2],
                    'won_count'  => $row[3],
                    'lost_count' => $row[4],
                ], $chunk));
            }
        });

        $this->frequencies = [];
    }

    public function score(Lead $lead): array
    {
        $frequencies = $this->frequenciesFor($lead->team_id);

        [$totalWon, $totalLost] = $frequencies[self::TOTAL][''] ?? [0, 0];

        if ($totalWon + $totalLost <= 0) {
            return ['probability' => null, 'factors' => []];
        }

        $won = ($totalWon + 1) / ($totalWon + $totalLost + 2);
        $lost = 1 - $won;
        $factors = [];

        $values = [['stage', (string) $lead->stage_id], ...$this->leadValues($lead, $this->enabledVariables())];

        foreach ($values as [$variable, $value]) {
            if ($value === '') {
                continue;
            }

            [$wonCount, $lostCount] = $frequencies[$variable][$value] ?? [0, 0];

            $wonShare = ($wonCount + self::SMOOTHING) / ($totalWon + self::SMOOTHING);
            $lostShare = ($lostCount + self::SMOOTHING) / ($totalLost + self::SMOOTHING);

            $won *= $wonShare;
            $lost *= $lostShare;

            $factors[] = ['variable' => $variable, 'value' => $value, 'ratio' => $wonShare / $lostShare];
        }

        $probability = $won + $lost > 0 ? round(100 * $won / ($won + $lost), 2) : 0.0;

        $probability = min($probability, 99.99);

        return ['probability' => $probability, 'factors' => $factors];
    }

    public function explain(Lead $lead): array
    {
        $score = $this->score($lead);

        $factors = collect($score['factors']);

        return [
            'probability' => $score['probability'],
            'team'        => $lead->team_id ? Team::query()->withTrashed()->whereKey($lead->team_id)->value('name') : null,
            'positives'   => $factors->where('ratio', '>', 1.05)->sortByDesc('ratio')->take(3)->map(fn (array $factor): string => $this->factorLabel($factor))->values()->all(),
            'negatives'   => $factors->where('ratio', '<', 0.95)->sortBy('ratio')->take(3)->map(fn (array $factor): string => $this->factorLabel($factor))->values()->all(),
        ];
    }

    public function refresh(Lead $lead): void
    {
        if ($lead->trashed() || $lead->stage?->is_won) {
            return;
        }

        $automated = $this->score($lead)['probability'];

        $updates = ['automated_probability' => $automated];

        if ($lead->is_automated_probability && $automated !== null) {
            $updates['probability'] = $automated;
        }

        Lead::query()->withoutGlobalScopes()->whereKey($lead->getKey())->update($updates);

        $lead->forceFill($updates)->syncOriginalAttributes(array_keys($updates));
    }

    public function rescoreOpenLeads(?int $teamId = null): int
    {
        $count = 0;

        Lead::query()
            ->withoutGlobalScopes()
            ->whereNull('deleted_at')
            ->whereDoesntHave('stage', fn ($query) => $query->where('is_won', true))
            ->when($teamId, fn ($query) => $query->where('team_id', $teamId))
            ->with(['stage', 'tags'])
            ->chunkById(500, function ($leads) use (&$count): void {
                foreach ($leads as $lead) {
                    $this->refresh($lead);
                    $count++;
                }
            });

        return $count;
    }

    public function rebuildAndRescore(): int
    {
        $this->rebuild();

        return $this->rescoreOpenLeads();
    }

    public static function scheduleRefresh(): void
    {
        if (static::$refreshScheduled) {
            return;
        }

        static::$refreshScheduled = true;

        DB::afterCommit(function (): void {
            static::$refreshScheduled = false;

            app(static::class)->rebuildAndRescore();
        });
    }

    protected function leadValues(Lead $lead, array $variables): array
    {
        $values = [];

        foreach ($variables as $variable) {
            match ($variable) {
                'phone' => $values[] = ['phone', strlen((string) Lead::sanitizePhone($lead->phone)) >= 7 ? 'valid' : 'invalid'],
                'email' => $values[] = ['email', filter_var((string) $lead->email, FILTER_VALIDATE_EMAIL) ? 'valid' : 'invalid'],
                'tags'  => array_push($values, ...$lead->tags->map(fn (Tag $tag): array => ['tags', (string) $tag->getKey()])->all()),
                default => filled($lead->{$variable}) ? $values[] = [$variable, (string) $lead->{$variable}] : null,
            };
        }

        return $values;
    }

    protected function frequenciesFor(?int $teamId): array
    {
        $key = (string) ($teamId ?? 0);

        if (isset($this->frequencies[$key])) {
            return $this->frequencies[$key];
        }

        $rows = DB::table('crm_lead_scoring_frequencies')
            ->when($teamId, fn ($query) => $query->where('team_id', $teamId), fn ($query) => $query->whereNull('team_id'))
            ->get();

        if ($rows->where('variable', self::TOTAL)->sum(fn ($row): float => $row->won_count + $row->lost_count) <= 0) {
            $rows = DB::table('crm_lead_scoring_frequencies')
                ->select('variable', 'value', DB::raw('SUM(won_count) as won_count'), DB::raw('SUM(lost_count) as lost_count'))
                ->groupBy('variable', 'value')
                ->get();
        }

        $frequencies = [];

        foreach ($rows as $row) {
            $frequencies[$row->variable][$row->value] = [(float) $row->won_count, (float) $row->lost_count];
        }

        return $this->frequencies[$key] = $frequencies;
    }

    protected function factorLabel(array $factor): string
    {
        $label = fn (string $key, array $replace = []): string => __('crm::filament/lead-scoring.factors.'.$key, $replace);

        return match ($factor['variable']) {
            'stage'         => $label('stage', ['value' => Stage::query()->whereKey($factor['value'])->value('name') ?? '?']),
            'phone'         => $label('phone-'.$factor['value']),
            'email'         => $label('email-'.$factor['value']),
            'country_id'    => $label('country', ['value' => Country::query()->whereKey($factor['value'])->value('name') ?? '?']),
            'state_id'      => $label('state', ['value' => State::query()->whereKey($factor['value'])->value('name') ?? '?']),
            'utm_source_id' => $label('source', ['value' => UTMSource::query()->whereKey($factor['value'])->value('name') ?? '?']),
            'tags'          => $label('tag', ['value' => Tag::query()->whereKey($factor['value'])->value('name') ?? '?']),
            default         => $factor['variable'],
        };
    }
}
