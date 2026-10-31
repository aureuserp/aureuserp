<?php

use Filament\Actions\Testing\TestAction;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;
use Webkul\Contact\Models\Partner;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\LostReasonResource\Pages\ManageLostReasons;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\PartnerLevelResource\Pages\ManagePartnerLevels;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\RecurringPlanResource\Pages\ManageRecurringPlans;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\StageResource\Pages\ManageStages;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\TagResource\Pages\ManageTags;
use Webkul\Crm\Filament\Clusters\Sales\Resources\ActivityResource\Pages\ListActivities;
use Webkul\Crm\Filament\Clusters\Sales\Resources\CustomerResource\Pages\ListCustomers;
use Webkul\Crm\Mail\LeadMessageMail;
use Webkul\Crm\Models\Activity;
use Webkul\Crm\Models\LostReason;
use Webkul\Crm\Models\PartnerLevel;
use Webkul\Crm\Models\RecurringPlan;
use Webkul\Partner\Enums\AccountType;
use Webkul\Partner\Models\Partner as BasePartner;
use Webkul\Support\Models\ActivityType;

require_once __DIR__.'/../../Helpers/CRMHelper.php';

beforeEach(fn () => CRMHelper::bootstrap());

function bulkContact(array $attributes = []): Partner
{
    $partner = BasePartner::factory()->create([
        'account_type'  => AccountType::INDIVIDUAL,
        'sub_type'      => 'partner',
        'company_id'    => CRMHelper::companyId(),
        'customer_rank' => 1,
        ...$attributes,
    ]);

    return Partner::query()->findOrFail($partner->id);
}

it('groups export with the core delete, restore and force delete on the configuration lists', function (string $page, string $resource, array $actions, array $missing) {
    CRMHelper::actingAsCRMUser([$resource]);

    $component = Livewire::test($page);

    foreach ($actions as $name) {
        $component->assertTableBulkActionExists($name);
    }

    foreach ($missing as $name) {
        $component->assertTableBulkActionDoesNotExist($name);
    }
})->with([
    'tags'         => [ManageTags::class, 'tag', ['export', 'delete'], ['archiveRecords', 'duplicateRecords', 'restore', 'forceDelete']],
    'stages'       => [ManageStages::class, 'stage', ['export', 'delete'], ['archiveRecords', 'duplicateRecords', 'restore', 'forceDelete']],
    'lost reasons' => [ManageLostReasons::class, 'lost::reason', ['export', 'delete', 'restore', 'forceDelete'], ['archiveRecords', 'duplicateRecords']],
]);

it('soft-deletes a configuration row with the core delete action and then offers restore instead', function (string $page, string $resource, Closure $makeRecord) {
    CRMHelper::actingAs(CRMHelper::permissionsFor([$resource]));

    $record = $makeRecord();

    Livewire::test($page)
        ->assertActionHasLabel(TestAction::make('delete')->table($record), __('filament-actions::delete.single.label'))
        ->callAction(TestAction::make('delete')->table($record));

    $record = $record::withTrashed()->find($record->getKey());

    expect($record?->trashed())->toBeTrue();

    Livewire::test($page)
        ->set('activeTab', 'archived')
        ->assertActionHidden(TestAction::make('delete')->table($record))
        ->assertActionVisible(TestAction::make('restore')->table($record));
})->with([
    'recurring plans' => [ManageRecurringPlans::class, 'recurring::plan', fn (): RecurringPlan => RecurringPlan::query()->create(['name' => 'Row delete plan', 'number_of_months' => 2])],
    'partner levels'  => [ManagePartnerLevels::class, 'partner::level', fn (): PartnerLevel => PartnerLevel::query()->create(['name' => 'Row delete level', 'partner_weight' => 1])],
    'lost reasons'    => [ManageLostReasons::class, 'lost::reason', fn (): LostReason => LostReason::factory()->create()],
]);

it('soft-deletes, restores and permanently deletes lost reasons', function () {
    CRMHelper::actingAsCRMUser(['lost::reason']);

    $archived = LostReason::factory()->create();
    $deleted = LostReason::factory()->create();

    Livewire::test(ManageLostReasons::class)->callTableBulkAction('delete', [$archived]);

    expect(LostReason::withTrashed()->find($archived->id)->trashed())->toBeTrue();

    Livewire::test(ManageLostReasons::class)->callTableBulkAction('restore', [LostReason::withTrashed()->find($archived->id)]);
    Livewire::test(ManageLostReasons::class)->callTableBulkAction('forceDelete', [$deleted]);

    expect(LostReason::query()->find($archived->id))->not->toBeNull()
        ->and(LostReason::withTrashed()->find($deleted->id))->toBeNull();
});

it('emails and texts the selected contacts and logs it on each contact', function () {
    Mail::fake();
    Http::fake(['api.msg91.com/*' => Http::response('ok')]);

    CRMHelper::configureSMSGateway();
    CRMHelper::actingAsCRMUser(['customer']);

    $contact = bulkContact(['email' => 'bulk-contact@acme.test', 'mobile' => '+91 98765 43210']);
    $withoutDetails = bulkContact(['email' => null, 'mobile' => null, 'phone' => null]);

    Livewire::test(ListCustomers::class)
        ->assertTableBulkActionExists('export')
        ->assertTableBulkActionExists('delete')
        ->assertTableBulkActionDoesNotExist('duplicateRecords')
        ->callTableBulkAction('sendCustomersEmail', [$contact, $withoutDetails], ['subject' => 'Hello', 'body' => '<p>News</p>'])
        ->callTableBulkAction('sendCustomersSMS', [$contact, $withoutDetails], ['message' => 'Hi there']);

    Mail::assertSent(LeadMessageMail::class, fn (LeadMessageMail $mail): bool => $mail->hasTo('bulk-contact@acme.test'));
    Mail::assertSentCount(1);
    Http::assertSentCount(1);
    Http::assertSent(fn (Request $request): bool => $request['mobiles'] === '919876543210');

    expect($contact->messages()->where('type', 'comment')->where('body', 'Hi there')->exists())->toBeTrue();
});

it('marks the selected activities as done', function () {
    CRMHelper::actingAsCRMUser(['activity', 'opportunity', 'lead']);

    $lead = CRMHelper::opportunity();
    $type = ActivityType::query()->whereIn('plugin', ['support', 'crm'])->firstOrFail();

    $activities = collect([1, 2])->map(fn (): Activity => Activity::query()->findOrFail($lead->addMessage([
        'type'             => 'activity',
        'activity_type_id' => $type->id,
        'date_deadline'    => now()->addDay()->toDateString(),
        'summary'          => 'Bulk follow-up',
        'assigned_to'      => auth()->id(),
    ])->getKey()));

    Livewire::test(ListActivities::class)
        ->assertTableBulkActionExists('export')
        ->assertTableBulkActionExists('delete')
        ->callTableBulkAction('markActivitiesAsDone', $activities, ['feedback' => 'All done']);

    expect(Activity::query()->whereKey($activities->modelKeys())->count())->toBe(0);
});
