<?php

use Livewire\Livewire;
use Webkul\Crm\Enums\LeadConversionAction;
use Webkul\Crm\Enums\LeadConversionCustomer;
use Webkul\Crm\Enums\LeadType;
use Webkul\Crm\Filament\Clusters\Leads\Resources\LeadResource\Actions\ConvertToOpportunityAction;
use Webkul\Crm\Filament\Clusters\Leads\Resources\LeadResource\Pages\ListLeads;
use Webkul\Crm\Filament\Clusters\Leads\Resources\LeadResource\Pages\ViewLead;
use Webkul\Crm\Models\Lead;
use Webkul\Crm\Models\LeadConversion;
use Webkul\Crm\Models\LeadMassConversion;
use Webkul\Crm\Services\LeadConverter;
use Webkul\Security\Models\User;

require_once __DIR__.'/../../Helpers/CRMHelper.php';

beforeEach(fn () => CRMHelper::bootstrap());

function conversionPermissions(): array
{
    return [
        ...CRMHelper::permissionsFor(['lead']),
        ...CRMHelper::permissionsFor(['opportunity']),
    ];
}

it('records a plain conversion', function () {
    $user = CRMHelper::actingAs(conversionPermissions());

    $lead = CRMHelper::lead(['contact_name' => 'Plain Buyer', 'email' => 'plain.buyer@customer.test']);

    $conversion = app(LeadConverter::class)->convert($lead, ['action' => LeadConversionAction::CONVERT, 'customer' => LeadConversionCustomer::CREATE, 'user_id' => $user->id]);

    expect($conversion->action)->toBe(LeadConversionAction::CONVERT)
        ->and($conversion->customer)->toBe(LeadConversionCustomer::CREATE)
        ->and($conversion->lead_id)->toBe($lead->id)
        ->and($conversion->opportunity_id)->toBe($lead->id)
        ->and($conversion->partner_id)->not->toBeNull()
        ->and($conversion->creator_id)->toBe($user->id)
        ->and($conversion->duplicates)->toHaveCount(0)
        ->and($lead->fresh()->type)->toBe(LeadType::OPPORTUNITY);
});

it('merges with existing opportunities and keeps the merged records in the conversion', function () {
    CRMHelper::actingAs(conversionPermissions());

    $existing = CRMHelper::opportunity(['email' => 'same@customer.test', 'name' => 'Existing deal']);
    $lead = CRMHelper::lead(['email' => 'SAME@customer.test', 'name' => 'New enquiry']);

    $duplicates = ConvertToOpportunityAction::mergeableDuplicates($lead->fresh());

    expect($duplicates->modelKeys())->toBe([$existing->id]);

    $conversion = app(LeadConverter::class)->convert($lead->fresh(), ['action' => LeadConversionAction::MERGE, 'customer' => LeadConversionCustomer::NONE], $duplicates);

    expect($conversion->action)->toBe(LeadConversionAction::MERGE)
        ->and($conversion->opportunity_id)->toBe($existing->id)
        ->and($conversion->lead_id)->toBeNull()
        ->and($conversion->original_lead_id)->toBe($lead->id)
        ->and($conversion->lead_name)->toBe('New enquiry')
        ->and($conversion->duplicates->pluck('original_lead_id')->all())->toBe([$existing->id])
        ->and(Lead::withTrashed()->find($lead->id))->toBeNull();
});

it('offers the merge choice in the convert dialog only when there are similar records', function () {
    CRMHelper::actingAs(conversionPermissions());

    $alone = CRMHelper::lead(['email' => 'alone@customer.test']);

    Livewire::test(ViewLead::class, ['record' => $alone->getRouteKey()])
        ->callAction('convertToOpportunity', data: ['customer' => 'none'])
        ->assertHasNoActionErrors();

    expect(LeadConversion::query()->where('original_lead_id', $alone->id)->value('action'))->toBe(LeadConversionAction::CONVERT);
});

it('converts several leads, shares salespeople in turn and records the mass conversion', function () {
    $user = CRMHelper::actingAs(conversionPermissions());

    $other = User::withoutEvents(fn () => User::factory()->create(['default_company_id' => CRMHelper::companyId(), 'is_active' => true]));

    $first = CRMHelper::lead(['email' => 'first@customer.test']);
    $second = CRMHelper::lead(['email' => 'second@customer.test']);
    $kept = CRMHelper::lead(['email' => 'kept@customer.test', 'user_id' => $user->id]);
    Lead::query()->whereKey([$first->id, $second->id])->update(['user_id' => null]);

    $mass = app(LeadConverter::class)->massConvert(
        collect([$first, $second, $kept])->map->fresh(),
        ['customer' => LeadConversionCustomer::NONE, 'deduplicate' => false, 'salespeople' => [$user->id, $other->id], 'force_assignment' => false],
    );

    expect($mass)->toBeInstanceOf(LeadMassConversion::class)
        ->and($mass->converted_count)->toBe(3)
        ->and($mass->salespeople->pluck('id')->sort()->values()->all())->toBe(collect([$user->id, $other->id])->sort()->values()->all())
        ->and($mass->leads->pluck('original_lead_id')->all())->toBe([$first->id, $second->id, $kept->id])
        ->and([$first->fresh()->user_id, $second->fresh()->user_id])->toBe([$user->id, $other->id])
        ->and($kept->fresh()->user_id)->toBe($user->id)
        ->and(Lead::query()->whereKey([$first->id, $second->id, $kept->id])->pluck('type')->unique()->all())->toBe([LeadType::OPPORTUNITY]);
});

it('merges each lead with its similar records when deduplicating', function () {
    CRMHelper::actingAs(conversionPermissions());

    $existing = CRMHelper::opportunity(['email' => 'dedup@customer.test']);
    $lead = CRMHelper::lead(['email' => 'dedup@customer.test']);

    $mass = app(LeadConverter::class)->massConvert(
        collect([$lead->fresh()]),
        ['customer' => LeadConversionCustomer::NONE, 'deduplicate' => true],
        fn (Lead $lead) => ConvertToOpportunityAction::mergeableDuplicates($lead),
    );

    expect($mass->merged_count)->toBe(1)
        ->and($mass->leads->first()->opportunity_id)->toBe($existing->id)
        ->and($mass->leads->first()->lead_id)->toBeNull();
});

it('runs the mass conversion from the leads list', function () {
    CRMHelper::actingAs(conversionPermissions());

    $leads = collect([CRMHelper::lead(), CRMHelper::lead()]);

    Livewire::test(ListLeads::class)
        ->callTableBulkAction('massConvertToOpportunities', $leads, data: ['customer' => 'none', 'deduplicate' => false])
        ->assertHasNoTableBulkActionErrors();

    expect(Lead::query()->whereKey($leads->modelKeys())->pluck('type')->unique()->all())->toBe([LeadType::OPPORTUNITY])
        ->and(LeadMassConversion::query()->latest('id')->value('converted_count'))->toBe(2);
});
