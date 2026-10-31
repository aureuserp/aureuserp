<?php

namespace Webkul\Crm\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use Webkul\Crm\Enums\LeadType;
use Webkul\Crm\Models\ActivityLog;
use Webkul\Crm\Models\Lead;
use Webkul\Crm\Models\LeadMerge;

class LeadMerger
{
    protected const FILLABLE_FIELDS = [
        'partner_id', 'contact_name', 'company_name', 'job_title', 'email', 'phone', 'website',
        'street1', 'street2', 'city', 'zip', 'state_id', 'country_id',
        'user_id', 'team_id', 'expected_closing_date', 'referred_by',
        'utm_campaign_id', 'utm_medium_id', 'utm_source_id',
    ];

    public function sortByConfidence(Collection $records): Collection
    {
        return $records->sort(function (Lead $a, Lead $b): int {
            return [
                $a->type === LeadType::OPPORTUNITY ? 0 : 1,
                $a->trashed() ? 1 : 0,
                -1 * (int) ($a->stage?->sort ?? 0),
                -1 * (float) $a->probability,
                $a->getKey(),
            ] <=> [
                $b->type === LeadType::OPPORTUNITY ? 0 : 1,
                $b->trashed() ? 1 : 0,
                -1 * (int) ($b->stage?->sort ?? 0),
                -1 * (float) $b->probability,
                $b->getKey(),
            ];
        })->values();
    }

    public function pickHead(Collection $records): Lead
    {
        return $this->sortByConfidence($records)->first();
    }

    public function distinctValues(Collection $records, string $field): array
    {
        return $records->pluck($field)->filter()->map(fn (mixed $id): int => (int) $id)->unique()->values()->all();
    }

    public function merge(Collection $records, ?Lead $head = null, array $assignments = []): Lead
    {
        $records = $records->unique(fn (Lead $lead): int => $lead->getKey())->values();

        if ($records->count() < 2) {
            throw new InvalidArgumentException('At least two records are needed to merge.');
        }

        if ($records->pluck('company_id')->unique()->count() > 1) {
            throw new InvalidArgumentException('Only records of the same company can be merged.');
        }

        $head ??= $this->pickHead($records);

        $others = $this->sortByConfidence($records->reject(fn (Lead $lead): bool => $lead->is($head)));

        foreach (['user_id', 'team_id'] as $field) {
            $chosen = $assignments[$field] ?? null;

            if (blank($chosen)) {
                continue;
            }

            if (! in_array((int) $chosen, $this->distinctValues($records, $field), true)) {
                throw new InvalidArgumentException("The chosen {$field} must belong to one of the merged records.");
            }

            $head->setAttribute($field, (int) $chosen);
        }

        return DB::transaction(function () use ($head, $others): Lead {
            $this->mergeFields($head, $others);

            $this->mergeTags($head, $others);

            $this->moveChatter($head, $others);

            ActivityLog::query()->whereIn('lead_id', $others->modelKeys())->update(['lead_id' => $head->getKey()]);

            LeadMerge::query()->create([
                'lead_id'        => $head->getKey(),
                'merged_records' => $others->map(fn (Lead $lead): array => [
                    'id'          => $lead->getKey(),
                    'name'        => $lead->name,
                    'type'        => $lead->type,
                    'email'       => $lead->email,
                    'phone'       => $lead->phone,
                    'salesperson' => $lead->user?->name,
                    'was_lost'    => $lead->trashed(),
                ])->all(),
                'merged_by'      => Auth::id(),
                'company_id'     => $head->company_id,
            ]);

            $head->addMessage([
                'type' => 'comment',
                'body' => __('crm::filament/similar-leads.merge.chatter-note', [
                    'records' => $others->map(fn (Lead $lead): string => e($lead->name).' (#'.$lead->getKey().')')->implode(', '),
                ]),
            ]);

            $others->each(fn (Lead $lead) => $lead->forceDelete());

            return $head->refresh();
        });
    }

    protected function mergeFields(Lead $head, Collection $others): void
    {
        foreach (self::FILLABLE_FIELDS as $field) {
            if (filled($head->getAttribute($field))) {
                continue;
            }

            $value = $others->map(fn (Lead $lead): mixed => $lead->getAttribute($field))->first(fn (mixed $value): bool => filled($value));

            if ($value !== null) {
                $head->setAttribute($field, $value);
            }
        }

        foreach (['expected_revenue', 'probability'] as $field) {
            if ((float) $head->getAttribute($field) === 0.0) {
                $head->setAttribute($field, $others->max(fn (Lead $lead): float => (float) $lead->getAttribute($field)));
            }
        }

        $head->priority = collect([$head, ...$others])->max(fn (Lead $lead): int => (int) ($lead->priority?->value ?? 0));

        $descriptions = collect([$head, ...$others])
            ->filter(fn (Lead $lead): bool => filled(trim(strip_tags((string) $lead->description))))
            ->map(fn (Lead $lead): string => $lead->is($head)
                ? (string) $lead->description
                : '<p><strong>'.e($lead->name).'</strong></p>'.$lead->description);

        if ($descriptions->count() > 1) {
            $head->description = $descriptions->implode('<hr>');
        }

        if ($others->contains(fn (Lead $lead): bool => $lead->type === LeadType::OPPORTUNITY)) {
            $head->type = LeadType::OPPORTUNITY;
            $head->converted_at ??= now();
        }

        if ($head->trashed()) {
            $head->restore();
        }

        $head->save();
    }

    protected function mergeTags(Lead $head, Collection $others): void
    {
        $tagIds = $others->flatMap(fn (Lead $lead) => $lead->tags()->pluck('crm_tags.id'))->all();

        $head->tags()->syncWithoutDetaching($tagIds);
    }

    protected function moveChatter(Lead $head, Collection $others): void
    {
        $morphType = $head->getMorphClass();

        $otherIds = $others->modelKeys();

        foreach (['chatter_messages', 'chatter_attachments'] as $table) {
            DB::table($table)
                ->where('messageable_type', $morphType)
                ->whereIn('messageable_id', $otherIds)
                ->update(['messageable_id' => $head->getKey()]);
        }

        $headFollowerPartnerIds = DB::table('chatter_followers')
            ->where('followable_type', $morphType)
            ->where('followable_id', $head->getKey())
            ->pluck('partner_id');

        DB::table('chatter_followers')
            ->where('followable_type', $morphType)
            ->whereIn('followable_id', $otherIds)
            ->whereIn('partner_id', $headFollowerPartnerIds)
            ->delete();

        DB::table('chatter_followers')
            ->where('followable_type', $morphType)
            ->whereIn('followable_id', $otherIds)
            ->get(['id', 'partner_id'])
            ->unique('partner_id')
            ->each(fn (object $follower) => DB::table('chatter_followers')->where('id', $follower->id)->update(['followable_id' => $head->getKey()]));

        DB::table('chatter_followers')
            ->where('followable_type', $morphType)
            ->whereIn('followable_id', $otherIds)
            ->delete();
    }
}
