<?php

namespace Webkul\Crm\Services;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Throwable;
use Webkul\Crm\Models\Lead;
use Webkul\Crm\Models\RevealRule;
use Webkul\Crm\Models\RevealView;
use Webkul\Crm\Settings\CrmSettings;
use Webkul\PluginManager\Package;
use Webkul\Support\Models\Company;
use Webkul\Support\Models\Country;

class VisitsToLeads
{
    public const VISITOR_MODEL = 'Webkul\\Website\\Models\\Visitor';

    public const GEOLOCATION_SERVICE = 'Webkul\\Website\\Services\\IpGeolocation';

    protected ?Collection $activeRules = null;

    public static function websiteAvailable(): bool
    {
        return class_exists(self::VISITOR_MODEL) && Package::isPluginInstalled('website');
    }

    public static function enabled(): bool
    {
        return static::websiteAvailable() && (bool) app(CrmSettings::class)->visits_to_leads;
    }

    public function activeRules(): Collection
    {
        return $this->activeRules ??= RevealRule::query()->active()->with(['countries', 'states'])->get();
    }

    public function matchingRules(Model $visitor, ?string $ip, ?string $url): Collection
    {
        $countryId = $visitor->country_id ?? $this->countryFromIp($ip)?->getKey();
        $stateId = $visitor->partner?->state_id;

        return $this->activeRules()
            ->filter(fn (RevealRule $rule): bool => $rule->matchesUrl($url) && $rule->matchesLocation($countryId, $stateId))
            ->values();
    }

    public function hasLead(Model $visitor): bool
    {
        return DB::table('crm_lead_visitor_rel')->where('visitor_id', $visitor->getKey())->exists();
    }

    public function queue(Model $visitor, ?string $ip, ?string $url): Collection
    {
        if (! static::enabled() || $this->activeRules()->isEmpty() || $this->hasLead($visitor)) {
            return new Collection;
        }

        return $this->matchingRules($visitor, $ip, $url)
            ->map(fn (RevealRule $rule): RevealView => RevealView::query()->firstOrCreate(
                ['visitor_id' => $visitor->getKey(), 'reveal_rule_id' => $rule->getKey()],
                ['reveal_ip' => $ip, 'url' => filled($url) ? mb_substr($url, 0, 2000) : null, 'reveal_state' => RevealView::STATE_TO_PROCESS],
            ));
    }

    public function processVisitor(int $visitorId): ?Lead
    {
        $views = RevealView::query()
            ->where('visitor_id', $visitorId)
            ->where('reveal_state', RevealView::STATE_TO_PROCESS)
            ->whereHas('rule', fn (Builder $query): Builder => $query->where('is_active', true))
            ->with('rule')
            ->get()
            ->sortBy(fn (RevealView $view): array => [$view->rule->sort, $view->rule->getKey()])
            ->values();

        $visitor = static::websiteAvailable() ? (self::VISITOR_MODEL)::query()->with(['partner', 'country'])->find($visitorId) : null;

        $lead = $visitor && ($view = $views->first())
            ? $this->leadForVisitor($visitor, $view->rule, $view->reveal_ip, $view->url)
            : null;

        RevealView::query()->where('visitor_id', $visitorId)->delete();

        return $lead;
    }

    public function processPending(): int
    {
        if (! static::enabled()) {
            return 0;
        }

        $count = 0;

        RevealView::query()
            ->where('reveal_state', RevealView::STATE_TO_PROCESS)
            ->distinct()
            ->pluck('visitor_id')
            ->each(function (int $visitorId) use (&$count): void {
                try {
                    if ($this->processVisitor($visitorId)) {
                        $count++;
                    }
                } catch (Throwable $exception) {
                    report($exception);
                }
            });

        return $count;
    }

    public function visitorsWithoutLeadQuery(): ?Builder
    {
        if (! static::websiteAvailable()) {
            return null;
        }

        return (self::VISITOR_MODEL)::query()
            ->whereNotIn('id', DB::table('crm_lead_visitor_rel')->select('visitor_id'));
    }

    public function generateForExistingVisitors(): int
    {
        if (! static::enabled() || $this->activeRules()->isEmpty()) {
            return 0;
        }

        $count = 0;

        $this->visitorsWithoutLeadQuery()
            ->with(['partner', 'country'])
            ->chunkById(200, function ($visitors) use (&$count): void {
                foreach ($visitors as $visitor) {
                    $urls = $visitor->tracks()->orderBy('visit_datetime')->pluck('url');

                    foreach ($urls as $url) {
                        if ($rule = $this->matchingRules($visitor, null, $url)->first()) {
                            $this->leadForVisitor($visitor, $rule, null, $url);

                            $count++;

                            break;
                        }
                    }
                }
            });

        return $count;
    }

    public function leadForVisitor(Model $visitor, RevealRule $rule, ?string $ip, ?string $url): Lead
    {
        $existing = Lead::query()
            ->withoutGlobalScopes()
            ->withTrashed()
            ->whereHas('visitors', fn (Builder $query): Builder => $query->whereKey($visitor->getKey()))
            ->first();

        if ($existing) {
            return $existing;
        }

        return DB::transaction(function () use ($visitor, $rule, $ip, $url): Lead {
            $country = $visitor->country ?? $this->countryFromIp($ip);
            $timezone = $visitor->timezone ?? $this->timezoneForCountry($country?->code);
            $partner = $visitor->partner;

            $lead = Lead::query()->create([
                'type'           => app(CrmSettings::class)->use_leads && $rule->lead_type === Lead::TYPE_LEAD ? Lead::TYPE_LEAD : Lead::TYPE_OPPORTUNITY,
                'name'           => collect([$this->leadName($visitor, $country), $rule->suffix])->filter()->implode(' - '),
                'partner_id'     => $partner?->getKey(),
                'contact_name'   => $partner?->name,
                'email'          => $partner?->email,
                'phone'          => $partner?->phone,
                'street1'        => $partner?->street1,
                'city'           => $partner?->city,
                'zip'            => $partner?->zip,
                'country_id'     => $country?->getKey(),
                'state_id'       => $partner?->state_id,
                'description'    => $this->description($visitor, $rule, $ip, $url, $country, $timezone),
                'referred_by'    => __('crm::visits-to-leads.referred-by'),
                'reveal_ip'      => $ip,
                'reveal_rule_id' => $rule->getKey(),
                'priority'       => $rule->priority,
                'team_id'        => $rule->team_id,
                'user_id'        => $rule->user_id,
                'company_id'     => $rule->company_id ?? $partner?->company_id ?? Company::query()->where('is_active', true)->orderBy('id')->value('id'),
                'creator_id'     => null,
            ]);

            $lead->tags()->syncWithoutDetaching($rule->tags()->pluck('crm_tags.id')->all());
            $lead->visitors()->syncWithoutDetaching([$visitor->getKey()]);

            return $lead;
        });
    }

    protected function leadName(Model $visitor, ?Country $country): string
    {
        if ($visitor->partner) {
            return __('crm::visits-to-leads.lead-name-partner', ['name' => $visitor->partner->name]);
        }

        return $country
            ? __('crm::visits-to-leads.lead-name-country', ['id' => $visitor->getKey(), 'country' => $country->name])
            : __('crm::visits-to-leads.lead-name', ['id' => $visitor->getKey()]);
    }

    protected function description(Model $visitor, RevealRule $rule, ?string $ip, ?string $url, ?Country $country, ?string $timezone): string
    {
        $rows = collect([
            'rule'         => $rule->name,
            'ip'           => $ip,
            'country'      => $country?->name,
            'timezone'     => $timezone,
            'language'     => $visitor->lang,
            'landing-page' => $url,
            'first-visit'  => $visitor->created_at?->toDateTimeString(),
        ])->filter(fn (?string $value): bool => filled($value));

        $items = $rows->map(fn (string $value, string $key): string => '<li><strong>'.e(__('crm::visits-to-leads.description.'.$key)).':</strong> '.e($value).'</li>')->implode('');

        return '<p>'.e(__('crm::visits-to-leads.description.intro')).'</p><ul>'.$items.'</ul>';
    }

    protected function countryFromIp(?string $ip): ?Country
    {
        if (blank($ip) || ! class_exists(self::GEOLOCATION_SERVICE)) {
            return null;
        }

        $code = app(self::GEOLOCATION_SERVICE)->countryCode($ip);

        return $code ? Country::query()->where('code', $code)->first() : null;
    }

    protected function timezoneForCountry(?string $code): ?string
    {
        return filled($code) && class_exists(self::GEOLOCATION_SERVICE)
            ? app(self::GEOLOCATION_SERVICE)->timezoneForCountry($code)
            : null;
    }
}
