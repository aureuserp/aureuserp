<?php

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Schema;
use Livewire\Livewire;
use Webkul\Chatter\Models\Message;
use Webkul\Crm\CRMServiceProvider;
use Webkul\Crm\Database\Seeders\ActivityTypeSeeder;
use Webkul\Crm\Database\Seeders\DatabaseSeeder;
use Webkul\Crm\Filament\Support\Reports\Widgets\ReportChart;
use Webkul\Crm\Models\ActivityType;
use Webkul\Crm\Models\Lead;
use Webkul\Crm\Models\LostReason;
use Webkul\Crm\Models\PartnerLevel;
use Webkul\Crm\Models\RecurringPlan;
use Webkul\Crm\Models\Stage;
use Webkul\Crm\Models\Tag;
use Webkul\Sale\Models\Team;

require_once __DIR__.'/../../Helpers/CRMHelper.php';

beforeEach(fn () => CRMHelper::bootstrap());

it('creates the crm tables', function (string $table) {
    expect(Schema::hasTable($table))->toBeTrue();
})->with([
    'crm_leads',
    'crm_stages',
    'crm_tags',
    'crm_lost_reasons',
    'crm_tag_rel',
    'crm_activity_logs',
]);

it('seeds the default pipeline stages with a won stage last', function () {
    $stages = Stage::query()->orderBy('sort')->get();

    expect($stages->pluck('name')->all())->toContain('New', 'Qualified', 'Proposition', 'Won')
        ->and($stages->where('is_won', true))->not->toBeEmpty()
        ->and($stages->firstWhere('name', 'Won')->is_won)->toBeTrue()
        ->and($stages->firstWhere('name', 'New')->is_won)->toBeFalse()
        ->and($stages->first()->is_won)->toBeFalse();
});

it('seeds the default lost reasons', function () {
    expect(LostReason::query()->pluck('name')->all())
        ->toContain('Too expensive', 'Not enough stock', 'No resources or skills available');
});

it('adds only Email and reuses the shared Call, Meeting and To-Do activity types', function () {
    $crmNames = ActivityType::query()->where('plugin', 'crm')->pluck('name');
    $names = ActivityType::query()->whereIn('plugin', ['support', 'crm'])->pluck('name');

    expect($crmNames->all())->toContain('Email')
        ->and($crmNames->intersect(['Call', 'Meeting', 'To-Do'])->all())->toBeEmpty()
        ->and($names->intersect(['Email', 'Call', 'Meeting', 'To-Do'])->count())->toBe(4)
        ->and($names->duplicates())->toBeEmpty();
});

it('merges an old crm copy of a shared activity type into the shared one when seeding again', function () {
    $shared = ActivityType::query()->where('plugin', 'support')->where('name', 'Call')->firstOrFail();
    $copy = ActivityType::query()->create([...$shared->only(['name', 'delay_count', 'delay_unit', 'delay_from', 'category', 'decoration_type', 'chaining_type']), 'plugin' => 'crm', 'is_active' => true]);
    $lead = CRMHelper::lead();
    $activity = Message::query()->create(['type' => 'activity', 'messageable_type' => $lead->getMorphClass(), 'messageable_id' => $lead->id, 'activity_type_id' => $copy->id, 'date_deadline' => today()]);

    (new ActivityTypeSeeder)->run();

    expect(ActivityType::withTrashed()->find($copy->id))->toBeNull()
        ->and($activity->fresh()->activity_type_id)->toBe($shared->id)
        ->and(ActivityType::query()->whereIn('plugin', ['support', 'crm'])->where('name', 'Call')->count())->toBe(1)
        ->and(ActivityType::query()->where('plugin', 'crm')->where('name', 'Email')->count())->toBe(1);
});

it('seeds the default tags, recurring plans and partner levels', function () {
    expect(Tag::query()->pluck('name')->all())->toContain('Product', 'Software', 'Services', 'Training', 'Consulting', 'Upsell')
        ->and(RecurringPlan::query()->orderBy('sort')->pluck('number_of_months', 'name')->only(['Monthly', 'Yearly', 'Over 3 years', 'Over 5 years'])->all())
        ->toBe(['Monthly' => 1, 'Yearly' => 12, 'Over 3 years' => 36, 'Over 5 years' => 60])
        ->and(PartnerLevel::query()->pluck('partner_weight', 'name')->only(['Gold', 'Silver', 'Bronze'])->all())
        ->toBe(['Gold' => 3, 'Silver' => 2, 'Bronze' => 1]);
});

it('does not duplicate or overwrite the defaults when seeding again', function () {
    Tag::query()->where('name', 'Product')->update(['color' => '#000000']);
    $tagCount = Tag::query()->count();

    (new DatabaseSeeder)->run();

    expect(Tag::query()->count())->toBe($tagCount)
        ->and(Tag::query()->where('name', 'Product')->value('color'))->toBe('#000000')
        ->and(RecurringPlan::query()->where('name', 'Monthly')->count())->toBe(1)
        ->and(PartnerLevel::query()->where('name', 'Gold')->count())->toBe(1);
});

it('depends on the contacts and sales plugins', function () {
    $provider = app()->getProvider(CRMServiceProvider::class);

    expect($provider)->toBeInstanceOf(CRMServiceProvider::class);

    $package = Closure::bind(function () {
        return $this->package;
    }, $provider, CRMServiceProvider::class)();

    expect($package->name)->toBe('crm')
        ->and($package->dependencies)->toBe(['contacts', 'sales']);
});

it('registers the opportunities relation on sales teams', function () {
    CRMHelper::actingAs();

    $team = Team::factory()->create(['company_id' => CRMHelper::companyId()]);

    $opportunity = CRMHelper::opportunity(['team_id' => $team->id]);
    $otherOpportunity = CRMHelper::opportunity();

    $relation = $team->opportunities();

    expect($relation)->toBeInstanceOf(HasMany::class)
        ->and($relation->getRelated())->toBeInstanceOf(Lead::class)
        ->and($relation->pluck('id')->all())->toContain($opportunity->id)
        ->and($relation->pluck('id')->all())->not->toContain($otherOpportunity->id);
});

it('registers the report chart livewire component', function () {
    expect(Livewire::exists('crm-report-chart'))->toBeTrue()
        ->and(app('livewire.finder')->resolveClassComponentClassName('crm-report-chart'))->toBe(ReportChart::class);
});
