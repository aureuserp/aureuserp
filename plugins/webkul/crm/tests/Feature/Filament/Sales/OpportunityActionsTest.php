<?php

use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;
use Webkul\Crm\Filament\Clusters\Leads\Resources\LeadResource\Pages\EditLead;
use Webkul\Crm\Filament\Clusters\Leads\Resources\LeadResource\Pages\ViewLead;
use Webkul\Crm\Filament\Clusters\Sales\Resources\OpportunityResource;
use Webkul\Crm\Filament\Clusters\Sales\Resources\OpportunityResource\Pages\EditOpportunity;
use Webkul\Crm\Filament\Clusters\Sales\Resources\OpportunityResource\Pages\ListOpportunities;
use Webkul\Crm\Filament\Clusters\Sales\Resources\OpportunityResource\Pages\ViewOpportunity;
use Webkul\Crm\Mail\LeadMessageMail;
use Webkul\Crm\Models\Lead;
use Webkul\Crm\Models\LostReason;

it('offers the lead bulk actions on the pipeline list', function () {
    CRMHelper::actingAsCRMUser(['opportunity']);

    $component = Livewire::withQueryParams(['layout' => 'list'])->test(ListOpportunities::class);

    foreach (['export', 'delete', 'restore', 'forceDelete', 'markLeadsAsLost', 'mergeLeads', 'sendLeadsEmail', 'sendLeadsSMS'] as $name) {
        $component->assertTableBulkActionExists($name);
    }

    $component->assertTableBulkActionDoesNotExist('archiveLeads')
        ->assertTableBulkActionDoesNotExist('duplicateLeads');
});

it('soft-deletes and marks opportunities as lost in bulk', function () {
    CRMHelper::actingAsCRMUser(['opportunity']);

    $deleted = CRMHelper::opportunity();
    $lost = CRMHelper::opportunity();
    $reason = LostReason::factory()->create();

    Livewire::withQueryParams(['layout' => 'list'])->test(ListOpportunities::class)
        ->callTableBulkAction('delete', [$deleted])
        ->callTableBulkAction('markLeadsAsLost', [$lost], ['lost_reason_id' => $reason->id]);

    expect(Lead::withTrashed()->find($deleted->id)->trashed())->toBeTrue()
        ->and(Lead::withTrashed()->find($lost->id)->lost_reason_id)->toBe($reason->id);
});

it('emails the selected opportunities', function () {
    Mail::fake();

    CRMHelper::actingAsCRMUser(['opportunity']);

    $opportunity = CRMHelper::opportunity(['email' => 'pipeline@acme.test']);

    Livewire::withQueryParams(['layout' => 'list'])->test(ListOpportunities::class)
        ->callTableBulkAction('sendLeadsEmail', [$opportunity], ['subject' => 'Proposal', 'body' => '<p>Attached</p>']);

    Mail::assertSent(LeadMessageMail::class, fn (LeadMessageMail $mail): bool => $mail->hasTo('pipeline@acme.test'));
});

it('shows chatter first and email, SMS and delete as buttons without an actions menu', function (string $page, string $type) {
    CRMHelper::actingAsCRMUser([$type]);

    $attributes = ['email' => 'view@acme.test', 'phone' => '+91 98765 43210'];
    $record = $type === 'lead' ? CRMHelper::lead($attributes) : CRMHelper::opportunity($attributes);

    $component = Livewire::test($page, ['record' => $record->getKey()]);

    foreach (['sendEmail', 'sendSMS', 'delete'] as $name) {
        $component->assertActionVisible($name);
    }

    foreach (['duplicate', 'archive', 'forceDelete'] as $name) {
        $component->assertActionDoesNotExist($name);
    }

    $component->assertActionHidden('restore');

    expect(array_key_first($component->instance()->getCachedHeaderActions()))->toBe('chatter.action');
})->with([
    'lead'        => [ViewLead::class, 'lead'],
    'opportunity' => [ViewOpportunity::class, 'opportunity'],
]);

it('soft-deletes an opportunity from its page and can restore it', function () {
    CRMHelper::actingAsCRMUser(['opportunity']);

    $opportunity = CRMHelper::opportunity();

    Livewire::test(ViewOpportunity::class, ['record' => $opportunity->getKey()])
        ->callAction('delete')
        ->assertRedirect(OpportunityResource::getUrl('index'));

    expect(Lead::withTrashed()->find($opportunity->id)?->trashed())->toBeTrue();

    Livewire::test(ViewOpportunity::class, ['record' => $opportunity->getKey()])
        ->assertActionHidden('delete')
        ->callAction('restore');

    expect(Lead::query()->find($opportunity->id))->not->toBeNull();
});

it('puts chatter first on the lead and opportunity edit pages', function (string $page, string $type) {
    CRMHelper::actingAsCRMUser([$type]);

    $record = $type === 'lead' ? CRMHelper::lead() : CRMHelper::opportunity();

    expect(array_key_first(Livewire::test($page, ['record' => $record->getKey()])->instance()->getCachedHeaderActions()))->toBe('chatter.action');
})->with([
    'lead'        => [EditLead::class, 'lead'],
    'opportunity' => [EditOpportunity::class, 'opportunity'],
]);
