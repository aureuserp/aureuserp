<?php

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;
use Webkul\Crm\Filament\Clusters\Leads\Resources\LeadResource\Pages\ListLeads;
use Webkul\Crm\Mail\LeadMessageMail;
use Webkul\Crm\Models\Lead;
use Webkul\Crm\Models\LostReason;

it('soft-deletes and restores the selected leads with the core bulk actions', function () {
    CRMHelper::actingAsCRMUser(['lead']);

    $leads = collect([CRMHelper::lead(), CRMHelper::lead()]);

    Livewire::test(ListLeads::class)
        ->assertTableBulkActionDoesNotExist('archiveLeads')
        ->assertTableBulkActionDoesNotExist('duplicateLeads')
        ->callTableBulkAction('delete', $leads);

    expect(Lead::onlyTrashed()->whereKey($leads->modelKeys())->count())->toBe(2);

    Livewire::test(ListLeads::class)
        ->call('loadView', 'lost')
        ->callTableBulkAction('restore', Lead::onlyTrashed()->whereKey($leads->modelKeys())->get());

    expect(Lead::query()->whereKey($leads->modelKeys())->count())->toBe(2);
});

it('marks the selected leads as lost with a reason', function () {
    CRMHelper::actingAsCRMUser(['lead']);

    $lead = CRMHelper::lead();
    $reason = LostReason::factory()->create();

    Livewire::test(ListLeads::class)
        ->callTableBulkAction('markLeadsAsLost', [$lead], ['lost_reason_id' => $reason->id, 'lost_feedback' => 'Too expensive']);

    $lost = Lead::withTrashed()->find($lead->id);

    expect($lost->trashed())->toBeTrue()
        ->and($lost->lost_reason_id)->toBe($reason->id);
});

it('emails the selected leads that have an email address and logs it on each lead', function () {
    Mail::fake();

    CRMHelper::actingAsCRMUser(['lead']);

    $withEmail = CRMHelper::lead(['email' => 'bulk-mail@acme.test']);
    $withoutEmail = CRMHelper::lead(['email' => null]);

    Livewire::test(ListLeads::class)
        ->callTableBulkAction('sendLeadsEmail', [$withEmail, $withoutEmail], ['subject' => 'Hello', 'body' => '<p>Our offer</p>']);

    Mail::assertSent(LeadMessageMail::class, fn (LeadMessageMail $mail): bool => $mail->hasTo('bulk-mail@acme.test')
        && str_contains($mail->mailSubject, '[Lead #'.$withEmail->id.']'));
    Mail::assertSentCount(1);

    expect($withEmail->messages()->where('type', 'comment')->where('subject', 'like', 'Hello%')->exists())->toBeTrue();
});

it('texts the selected leads that have a valid number and logs it on each lead', function () {
    Http::fake(['api.msg91.com/*' => Http::response('ok')]);

    CRMHelper::configureSMSGateway();
    CRMHelper::actingAsCRMUser(['lead']);

    $withNumber = CRMHelper::lead(['phone' => '+91 98765 43210']);
    $withoutNumber = CRMHelper::lead(['phone' => null, 'partner_id' => null]);

    Livewire::test(ListLeads::class)
        ->callTableBulkAction('sendLeadsSMS', [$withNumber, $withoutNumber], ['message' => 'Offer ends today']);

    Http::assertSentCount(1);
    Http::assertSent(fn (Request $request): bool => $request['mobiles'] === '919876543210');

    expect($withNumber->messages()->where('type', 'comment')->where('body', 'Offer ends today')->exists())->toBeTrue();
});

it('merges only the records left in the merge list', function () {
    CRMHelper::actingAsCRMUser(['lead']);

    $head = CRMHelper::lead(['email' => 'merge-list@acme.test']);
    $duplicate = CRMHelper::lead(['email' => 'merge-list@acme.test']);
    $removed = CRMHelper::lead(['email' => 'merge-list@acme.test']);

    Livewire::test(ListLeads::class)
        ->callTableBulkAction('mergeLeads', [$head, $duplicate, $removed], [
            'records' => [['id' => $head->id], ['id' => $duplicate->id]],
            'head'    => $head->id,
        ])
        ->assertHasNoTableBulkActionErrors();

    expect(Lead::withTrashed()->find($duplicate->id))->toBeNull()
        ->and(Lead::query()->find($removed->id))->not->toBeNull()
        ->and(Lead::query()->find($head->id))->not->toBeNull();
});
