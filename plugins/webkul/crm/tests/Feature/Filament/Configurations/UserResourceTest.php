<?php

use Livewire\Livewire;
use Webkul\Crm\Filament\Clusters\Configurations;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\UserResource;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\UserResource\Pages\CreateUser;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\UserResource\Pages\EditUser;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\UserResource\Pages\ListUsers;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\UserResource\Pages\ViewUser;
use Webkul\Crm\Policies\UserPolicy;

require_once __DIR__.'/../../../Helpers/CrmTestHelper.php';

beforeEach(fn () => CrmTestHelper::bootstrap());

it('lists users with the crm user permission', function () {
    $user = CrmTestHelper::actingAsCrmUser(['user']);

    Livewire::test(ListUsers::class)
        ->assertCanSeeTableRecords([$user]);
});

it('opens the create user page with the crm create permission', function () {
    CrmTestHelper::actingAsCrmUser(['user']);

    Livewire::test(CreateUser::class)
        ->assertSuccessful();
});

it('opens the view and edit pages of a user with the crm permissions', function () {
    $user = CrmTestHelper::actingAsCrmUser(['user']);

    Livewire::test(ViewUser::class, ['record' => $user->getRouteKey()])
        ->assertSee($user->name);

    Livewire::test(EditUser::class, ['record' => $user->getRouteKey()])
        ->assertSchemaStateSet(['name' => $user->name]);
});

it('forbids the crm users list with only the security user permissions', function () {
    CrmTestHelper::actingAs([
        'view_any_security_user',
        'view_security_user',
        'create_security_user',
        'update_security_user',
    ]);

    Livewire::test(ListUsers::class)->assertForbidden();
    Livewire::test(CreateUser::class)->assertForbidden();
});

it('forbids editing a user with only the security update permission', function () {
    $user = CrmTestHelper::actingAs(['view_any_crm_user', 'view_crm_user', 'update_security_user']);

    Livewire::test(EditUser::class, ['record' => $user->getRouteKey()])->assertForbidden();
});

it('authorizes the users resource against the crm user policy', function () {
    expect(UserResource::getCrmPolicy())->toBe(UserPolicy::class)
        ->and(UserResource::getCluster())->toBe(Configurations::class)
        ->and(UserResource::canGloballySearch())->toBeFalse();
});

it('takes the users resource labels from the crm translations', function () {
    expect(UserResource::getModelLabel())->toBe(__('crm::filament/clusters/configurations/resources/user.model-label'))
        ->and(UserResource::getPluralModelLabel())->toBe(__('crm::filament/clusters/configurations/resources/user.plural-model-label'))
        ->and(UserResource::getNavigationLabel())->toBe(__('crm::filament/clusters/configurations/resources/user.navigation.title'))
        ->and(UserResource::getNavigationGroup())->toBe(__('crm::filament/clusters/configurations/resources/user.navigation.group'));

    app()->setLocale('ar');

    expect(UserResource::getModelLabel())->toBe('مستخدم')
        ->and(UserResource::getPluralModelLabel())->toBe('المستخدمون');
});
