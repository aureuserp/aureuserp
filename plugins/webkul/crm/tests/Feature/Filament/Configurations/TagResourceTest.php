<?php

use Filament\Actions\Testing\TestAction;
use Livewire\Livewire;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\TagResource\Pages\ManageTags;
use Webkul\Crm\Models\Tag;

require_once __DIR__.'/../../../Helpers/CrmTestHelper.php';

beforeEach(fn () => CrmTestHelper::bootstrap());

it('lists the tags', function () {
    CrmTestHelper::actingAsCrmUser(['tag']);

    $tags = Tag::factory()->count(2)->create();

    Livewire::test(ManageTags::class)
        ->assertCanSeeTableRecords($tags);
});

it('searches tags by name', function () {
    CrmTestHelper::actingAsCrmUser(['tag']);

    $hot = Tag::factory()->create(['name' => 'Hot lead (test)']);
    $cold = Tag::factory()->create(['name' => 'Cold lead (test)']);

    Livewire::test(ManageTags::class)
        ->searchTable('Hot lead')
        ->assertCanSeeTableRecords([$hot])
        ->assertCanNotSeeTableRecords([$cold]);
});

it('creates a tag with a color', function () {
    $user = CrmTestHelper::actingAsCrmUser(['tag']);

    Livewire::test(ManageTags::class)
        ->callAction('create', data: [
            'name'  => 'Enterprise (test)',
            'color' => '#ff0000',
        ])
        ->assertHasNoActionErrors();

    $tag = Tag::query()->where('name', 'Enterprise (test)')->first();

    expect($tag)->not->toBeNull()
        ->and($tag->color)->toBe('#ff0000')
        ->and($tag->creator_id)->toBe($user->id);
});

it('rejects a duplicate tag name', function () {
    CrmTestHelper::actingAsCrmUser(['tag']);

    Tag::factory()->create(['name' => 'Duplicate (test)']);

    Livewire::test(ManageTags::class)
        ->callAction('create', data: ['name' => 'Duplicate (test)'])
        ->assertHasActionErrors(['name' => 'unique']);

    expect(Tag::query()->where('name', 'Duplicate (test)')->count())->toBe(1);
});

it('edits a tag and keeps its own name valid', function () {
    CrmTestHelper::actingAsCrmUser(['tag']);

    $tag = Tag::factory()->create(['name' => 'Renew (test)', 'color' => '#000000']);

    Livewire::test(ManageTags::class)
        ->callAction(TestAction::make('edit')->table($tag), data: [
            'name'  => 'Renew (test)',
            'color' => '#00ff00',
        ])
        ->assertHasNoActionErrors();

    expect($tag->refresh()->color)->toBe('#00ff00');
});

it('rejects renaming a tag to an existing name', function () {
    CrmTestHelper::actingAsCrmUser(['tag']);

    Tag::factory()->create(['name' => 'Taken (test)']);
    $tag = Tag::factory()->create(['name' => 'Free (test)']);

    Livewire::test(ManageTags::class)
        ->callAction(TestAction::make('edit')->table($tag), data: ['name' => 'Taken (test)'])
        ->assertHasActionErrors(['name' => 'unique']);

    expect($tag->refresh()->name)->toBe('Free (test)');
});

it('deletes a tag', function () {
    CrmTestHelper::actingAsCrmUser(['tag']);

    $tag = Tag::factory()->create();

    Livewire::test(ManageTags::class)
        ->callAction(TestAction::make('delete')->table($tag));

    expect(Tag::query()->whereKey($tag->id)->exists())->toBeFalse();
});

it('hides the create action without the create permission', function () {
    CrmTestHelper::actingAs(['view_any_crm_tag', 'view_crm_tag']);

    Livewire::test(ManageTags::class)
        ->assertActionHidden('create');
});

it('forbids the tags page without crm tag permissions', function () {
    CrmTestHelper::actingAs(['view_any_sale_tag', 'view_any_crm_stage']);

    Livewire::test(ManageTags::class)->assertForbidden();
});
