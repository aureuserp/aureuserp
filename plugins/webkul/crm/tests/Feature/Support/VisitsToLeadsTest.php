<?php

use Illuminate\Support\Str;
use Livewire\Livewire;
use Webkul\Crm\Enums\LeadType;
use Webkul\Crm\Enums\OpportunityPriority;
use Webkul\Crm\Enums\RevealRuleContactCount;
use Webkul\Crm\Enums\RevealRuleContactFilter;
use Webkul\Crm\Enums\RevealRuleLeadFor;
use Webkul\Crm\Enums\RevealViewState;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\RevealRuleResource;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\RevealRuleResource\Pages\CreateRevealRule;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\RevealRuleResource\Pages\ManageRevealRuleLeads;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\RevealRuleResource\Pages\ViewRevealRule;
use Webkul\Crm\Listeners\CreateLeadFromVisitor;
use Webkul\Crm\Models\Lead;
use Webkul\Crm\Models\RevealRule;
use Webkul\Crm\Models\RevealView;
use Webkul\Crm\Models\Tag;
use Webkul\Crm\Services\VisitsToLeads;
use Webkul\Crm\Settings\CRMSettings;
use Webkul\Partner\Models\Partner;
use Webkul\Support\Models\Country;
use Webkul\Website\Events\VisitorTracked;
use Webkul\Website\Models\Visitor;

require_once __DIR__.'/../../Helpers/CRMHelper.php';

beforeEach(function () {
    CRMHelper::bootstrap();
    TestBootstrapHelper::ensurePluginInstalled('website');

    RevealRule::query()->delete();

    visitsToLeadsSetting(true);
});

function visitsToLeadsSetting(bool $enabled, bool $useLeads = true): void
{
    $settings = app(CRMSettings::class);
    $settings->visits_to_leads = $enabled;
    $settings->use_leads = $useLeads;
    $settings->save();
}

function websiteVisitor(array $attributes = []): Visitor
{
    return Visitor::query()->create([
        'access_token'             => Str::lower(Str::random(32)),
        'visit_count'              => 1,
        'lang'                     => 'en',
        'last_connection_datetime' => now(),
        ...$attributes,
    ]);
}

function revealRule(array $attributes = [], array $countryCodes = []): RevealRule
{
    $rule = RevealRule::query()->create(['name' => 'Rule '.uniqid(), 'lead_type' => LeadType::LEAD, ...$attributes]);

    if ($countryCodes) {
        $rule->countries()->attach(Country::query()->whereIn('code', $countryCodes)->pluck('id'));
    }

    return $rule;
}

function siteUrl(string $path = ''): string
{
    return url('/').$path;
}

function leadOfVisitor(Visitor $visitor): ?Lead
{
    return Lead::query()->withoutGlobalScopes()->whereHas('visitors', fn ($query) => $query->whereKey($visitor->id))->first();
}

it('creates no lead when no rule exists', function () {
    $visitor = websiteVisitor();

    expect(app(VisitsToLeads::class)->queue($visitor, '203.0.113.7', siteUrl('/pages/pricing')))->toBeEmpty()
        ->and(app(VisitsToLeads::class)->processVisitor($visitor->id))->toBeNull();
});

it('does nothing when the setting is off', function () {
    visitsToLeadsSetting(false);
    revealRule();

    $visitor = websiteVisitor();

    app(CreateLeadFromVisitor::class)->handle(new VisitorTracked($visitor, '203.0.113.7', siteUrl('/')));

    expect(RevealView::query()->where('visitor_id', $visitor->id)->exists())->toBeFalse()
        ->and(app(VisitsToLeads::class)->generateForExistingVisitors())->toBe(0);
});

it('queues only the visits that match a rule URL expression', function () {
    $rule = revealRule(['regex_url' => '/pricing|/shop']);
    $visitor = websiteVisitor();
    $listener = app(CreateLeadFromVisitor::class);

    $listener->handle(new VisitorTracked($visitor, '203.0.113.7', siteUrl('/pages/about-us')));

    expect(RevealView::query()->where('visitor_id', $visitor->id)->exists())->toBeFalse();

    $listener->handle(new VisitorTracked($visitor, '203.0.113.7', siteUrl('/pages/pricing')));

    expect(RevealView::query()->where('visitor_id', $visitor->id)->first())
        ->reveal_rule_id->toBe($rule->id)
        ->reveal_ip->toBe('203.0.113.7')
        ->reveal_state->toBe(RevealViewState::TO_PROCESS);
});

it('matches rules on the country of the visitor', function () {
    revealRule([], ['IN']);
    $service = app(VisitsToLeads::class);

    $indian = websiteVisitor(['country_id' => Country::query()->where('code', 'IN')->value('id')]);
    $french = websiteVisitor(['country_id' => Country::query()->where('code', 'FR')->value('id')]);

    expect($service->queue($indian, null, siteUrl('/')))->toHaveCount(1)
        ->and($service->queue($french, null, siteUrl('/')))->toBeEmpty();
});

it('creates the lead from the first matching rule and clears the queue', function () {
    $tag = Tag::query()->create(['name' => 'Visit tag '.uniqid()]);
    $first = revealRule(['sort' => 1, 'suffix' => 'Pricing', 'priority' => OpportunityPriority::HIGH->value]);
    $first->tags()->attach($tag->id);
    revealRule(['sort' => 2, 'suffix' => 'Second']);

    $country = Country::query()->where('code', 'IN')->firstOrFail();
    $visitor = websiteVisitor(['country_id' => $country->id, 'timezone' => 'Asia/Kolkata']);
    $service = app(VisitsToLeads::class);

    $service->queue($visitor, '203.0.113.7', siteUrl('/pages/pricing'));
    $lead = $service->processVisitor($visitor->id);

    expect($lead->type)->toBe(LeadType::LEAD)
        ->and($lead->name)->toBe("Website visitor #{$visitor->id} (India) - Pricing")
        ->and($lead->reveal_rule_id)->toBe($first->id)
        ->and($lead->priority)->toBe(OpportunityPriority::HIGH)
        ->and($lead->tags->pluck('id')->all())->toBe([$tag->id])
        ->and($lead->country_id)->toBe($country->id)
        ->and($lead->reveal_ip)->toBe('203.0.113.7')
        ->and($lead->user_id)->toBeNull()
        ->and($lead->description)->toContain('Asia/Kolkata')->toContain($first->name)
        ->and(RevealView::query()->where('visitor_id', $visitor->id)->exists())->toBeFalse();
});

it('creates an opportunity when leads are turned off', function () {
    visitsToLeadsSetting(true, useLeads: false);

    $lead = app(VisitsToLeads::class)->leadForVisitor(websiteVisitor(), revealRule(['lead_type' => LeadType::LEAD]), null, null);

    expect($lead->type)->toBe(LeadType::OPPORTUNITY);
});

it('creates only one lead per visitor', function () {
    $rule = revealRule();
    $visitor = websiteVisitor();
    $service = app(VisitsToLeads::class);

    $first = $service->leadForVisitor($visitor, $rule, '203.0.113.7', null);

    expect($service->leadForVisitor($visitor, $rule, '203.0.113.7', null)->id)->toBe($first->id)
        ->and($service->queue($visitor, '203.0.113.7', siteUrl('/')))->toBeEmpty();
});

it('fills the contact details of a logged-in visitor', function () {
    $partner = Partner::factory()->create(['name' => 'Visiting Customer', 'email' => 'visiting@example.com']);

    $lead = app(VisitsToLeads::class)->leadForVisitor(websiteVisitor(['partner_id' => $partner->id]), revealRule(), null, null);

    expect($lead->name)->toBe('Website visitor: Visiting Customer')
        ->and($lead->partner_id)->toBe($partner->id)
        ->and($lead->email)->toBe('visiting@example.com');
});

it('applies the rules to the pages already visited by existing visitors', function () {
    revealRule(['regex_url' => '/pricing']);

    $matching = websiteVisitor();
    $matching->tracks()->create(['url' => siteUrl('/pages/pricing'), 'visit_datetime' => now()]);
    $other = websiteVisitor();
    $other->tracks()->create(['url' => siteUrl('/pages/about-us'), 'visit_datetime' => now()]);

    app(VisitsToLeads::class)->generateForExistingVisitors();

    expect(leadOfVisitor($matching))->not->toBeNull()
        ->and(leadOfVisitor($other))->toBeNull();
});

it('matches URL expressions on the path relative to the website', function () {
    expect(revealRule(['regex_url' => '/'])->matchesUrl(siteUrl('/')))->toBeTrue()
        ->and(revealRule(['regex_url' => '/'])->matchesUrl(siteUrl('/pages/about-us')))->toBeFalse()
        ->and(revealRule()->matchesUrl(siteUrl('/anything')))->toBeTrue()
        ->and(RevealRule::isValidPattern('(['))->toBeFalse();
});

it('creates a rule with company and contact details from the form', function () {
    CRMHelper::actingAsCRMUser(['reveal::rule']);

    Livewire::test(CreateRevealRule::class)
        ->fillForm([
            'name'                => 'Form rule',
            'regex_url'           => '/pricing',
            'lead_type'           => LeadType::OPPORTUNITY,
            'lead_for'            => RevealRuleLeadFor::PEOPLE,
            'company_size_min'    => 10,
            'company_size_max'    => 500,
            'contact_filter_type' => RevealRuleContactFilter::ROLE,
            'extra_contacts'      => RevealRuleContactCount::THREE,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    expect(RevealRule::query()->where('name', 'Form rule')->first())
        ->lead_for->toBe(RevealRuleLeadFor::PEOPLE)
        ->company_size_max->toBe(500)
        ->extra_contacts->toBe(RevealRuleContactCount::THREE);
});

it('rejects an invalid URL expression and a company size range that ends before it starts', function () {
    CRMHelper::actingAsCRMUser(['reveal::rule']);

    Livewire::test(CreateRevealRule::class)
        ->fillForm(['name' => 'Bad rule', 'regex_url' => '([', 'company_size_min' => 500, 'company_size_max' => 10])
        ->call('create')
        ->assertHasFormErrors(['regex_url', 'company_size_max']);
});

it('hides the rules screen while Visitors to Leads is off', function () {
    CRMHelper::actingAsCRMUser(['reveal::rule']);

    expect(RevealRuleResource::canViewAny())->toBeTrue();

    visitsToLeadsSetting(false);

    expect(RevealRuleResource::canViewAny())->toBeFalse();
});

it('shows a rule on its view page with view, edit and generated leads tabs', function () {
    CRMHelper::actingAsCRMUser(['reveal::rule', 'lead', 'opportunity']);

    $rule = revealRule(['name' => 'Tabbed rule'], ['IN']);
    $generated = CRMHelper::lead(['name' => 'Generated by rule']);
    $generated->forceFill(['reveal_rule_id' => $rule->id])->save();

    $component = Livewire::test(ViewRevealRule::class, ['record' => $rule->getRouteKey()])
        ->assertOk()
        ->assertSee('Tabbed rule');

    $tabs = collect($component->instance()->getRecordNavigationTabs())
        ->mapWithKeys(fn ($tab): array => [$tab->getLabel() => $tab->getBadge()]);

    expect($tabs->keys()->all())->toHaveCount(3)
        ->and($tabs->get(ManageRevealRuleLeads::getNavigationLabel()))->toBe(1);
});

it('lists only the leads a rule generated on its generated leads tab', function () {
    CRMHelper::actingAsCRMUser(['reveal::rule', 'lead', 'opportunity']);

    $rule = revealRule(['name' => 'Listing rule']);
    $generated = CRMHelper::lead(['name' => 'From this rule']);
    $generated->forceFill(['reveal_rule_id' => $rule->id])->save();
    $other = CRMHelper::lead(['name' => 'From nowhere']);

    Livewire::test(ManageRevealRuleLeads::class, ['record' => $rule->getRouteKey()])
        ->assertOk()
        ->assertCanSeeTableRecords([$generated])
        ->assertCanNotSeeTableRecords([$other]);
});
