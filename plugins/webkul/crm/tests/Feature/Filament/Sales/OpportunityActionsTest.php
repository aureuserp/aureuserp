<?php

use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;
use Webkul\Crm\Filament\Clusters\Leads\Resources\LeadResource\Pages\ViewLead;
use Webkul\Crm\Filament\Clusters\Sales\Resources\OpportunityResource;
use Webkul\Crm\Filament\Clusters\Sales\Resources\OpportunityResource\Pages\ListOpportunities;
use Webkul\Crm\Filament\Clusters\Sales\Resources\OpportunityResource\Pages\ViewOpportunity;
use Webkul\Crm\Mail\LeadMessageMail;
use Webkul\Crm\Models\Lead;
use Webkul\Crm\Models\LostReason;

it('offers the lead bulk actions on the pipeline list', function () {
    CRMHelper::actingAsCRMUser(['opportunity']);

    $component = Livewire::test(ListOpportunities::class);

    foreach (['export', 'archiveLeads', 'restore', 'duplicateLeads', 'forceDelete', 'markLeadsAsLost', 'mergeLeads', 'sendLeadsEmail', 'sendLeadsSMS'] as $name) {
        $component->assertTableBulkActionExists($name);
    }
});

it('archives, duplicates and marks opportunities as lost in bulk', function () {
    CRMHelper::actingAsCRMUser(['opportunity']);

    $archived = CRMHelper::opportunity();
    $copied = CRMHelper::opportunity(['name' => 'Big deal']);
    $lost = CRMHelper::opportunity();
    $reason = LostReason::factory()->create();

    Livewire::test(ListOpportunities::class)
        ->callTableBulkAction('archiveLeads', [$archived])
        ->callTableBulkAction('duplicateLeads', [$copied])
        ->callTableBulkAction('markLeadsAsLost', [$lost], ['lost_reason_id' => $reason->id]);

    $copy = Lead::query()->where('name', __('crm::filament/lead-actions.duplicate.copy', ['name' => 'Big deal']))->first();

    expect(Lead::withTrashed()->find($archived->id)->trashed())->toBeTrue()
        ->and($copy?->isLead())->toBeFalse()
        ->and(Lead::withTrashed()->find($lost->id)->lost_reason_id)->toBe($reason->id);
});

it('emails the selected opportunities', function () {
    Mail::fake();

    CRMHelper::actingAsCRMUser(['opportunity']);

    $opportunity = CRMHelper::opportunity(['email' => 'pipeline@acme.test']);

    Livewire::test(ListOpportunities::class)
        ->callTableBulkAction('sendLeadsEmail', [$opportunity], ['subject' => 'Proposal', 'body' => '<p>Attached</p>']);

    Mail::assertSent(LeadMessageMail::class, fn (LeadMessageMail $mail): bool => $mail->hasTo('pipeline@acme.test'));
});

it('groups email, SMS, duplicate, archive and delete on the record pages', function (string $page, string $type) {
    CRMHelper::actingAsCRMUser(['lead', 'opportunity']);

    $record = $type === 'lead' ? CRMHelper::lead(['email' => 'view@acme.test']) : CRMHelper::opportunity(['email' => 'view@acme.test']);

    $component = Livewire::test($page, ['record' => $record->getKey()]);

    foreach (['sendEmail', 'sendSMS', 'duplicate', 'archive', 'forceDelete'] as $name) {
        $component->assertActionVisible($name);
    }

    $component->assertActionHidden('restore');
})->with([
    'lead'        => [ViewLead::class, 'lead'],
    'opportunity' => [ViewOpportunity::class, 'opportunity'],
]);

it('duplicates an opportunity from its page and opens the copy', function () {
    CRMHelper::actingAsCRMUser(['opportunity']);

    $opportunity = CRMHelper::opportunity(['name' => 'Renewal']);

    $component = Livewire::test(ViewOpportunity::class, ['record' => $opportunity->getKey()])
        ->callAction('duplicate');

    $copy = Lead::query()->where('name', __('crm::filament/lead-actions.duplicate.copy', ['name' => 'Renewal']))->firstOrFail();

    $component->assertRedirect(OpportunityResource::getUrl('view', ['record' => $copy]));
});

it('archives an opportunity from its page and then offers unarchive', function () {
    CRMHelper::actingAsCRMUser(['opportunity']);

    $opportunity = CRMHelper::opportunity();

    Livewire::test(ViewOpportunity::class, ['record' => $opportunity->getKey()])
        ->callAction('archive')
        ->assertActionVisible('restore')
        ->assertActionHidden('archive');

    expect(Lead::withTrashed()->find($opportunity->id)->trashed())->toBeTrue();
});

it('permanently deletes an opportunity from its page', function () {
    CRMHelper::actingAsCRMUser(['opportunity']);

    $opportunity = CRMHelper::opportunity();

    Livewire::test(ViewOpportunity::class, ['record' => $opportunity->getKey()])
        ->callAction('forceDelete')
        ->assertRedirect(OpportunityResource::getUrl('index'));

    expect(Lead::withTrashed()->find($opportunity->id))->toBeNull();
});
