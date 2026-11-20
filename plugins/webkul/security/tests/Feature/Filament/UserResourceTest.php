<?php

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;
use Livewire\Livewire;
use Webkul\Security\Enums\PermissionType;
use Webkul\Security\Filament\Resources\UserResource\Pages\CreateUser;
use Webkul\Security\Models\Role;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;

require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/FilamentHelper.php';

beforeEach(function () {
    TestBootstrapHelper::ensureERPInstalled();

    URL::resolveMissingNamedRoutesUsing(fn () => '#');
});

afterEach(fn () => Model::preventSilentlyDiscardingAttributes(false));

it('creates a user with a confirmed password through the create form with strict model writes', function () {
    FilamentHelper::actingAs(['view_any_security_user', 'create_security_user']);

    $company = Company::query()->orderBy('id')->firstOrFail();

    $role = Role::create([
        'name'       => 'strict-model-writes-role',
        'guard_name' => 'web',
    ]);

    Model::preventSilentlyDiscardingAttributes();

    Livewire::test(CreateUser::class)
        ->fillForm([
            'name'                  => 'Strict Writes User',
            'email'                 => 'strict-writes-user@example.com',
            'password'              => 'Strict-pass-123',
            'password_confirmation' => 'Strict-pass-123',
            'roles'                 => [$role->id],
            'resource_permission'   => PermissionType::GLOBAL->value,
            'allowed_companies'     => [$company->id],
            'default_company_id'    => $company->id,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $user = User::query()->where('email', 'strict-writes-user@example.com')->firstOrFail();

    expect(Hash::check('Strict-pass-123', $user->password))->toBeTrue()
        ->and($user->roles->pluck('id')->all())->toBe([$role->id]);
});

it('still rejects a password confirmation that does not match', function () {
    FilamentHelper::actingAs(['view_any_security_user', 'create_security_user']);

    $company = Company::query()->orderBy('id')->firstOrFail();

    $role = Role::create([
        'name'       => 'strict-model-writes-mismatch-role',
        'guard_name' => 'web',
    ]);

    Livewire::test(CreateUser::class)
        ->fillForm([
            'name'                  => 'Mismatched User',
            'email'                 => 'strict-writes-mismatch@example.com',
            'password'              => 'Strict-pass-123',
            'password_confirmation' => 'Another-pass-456',
            'roles'                 => [$role->id],
            'resource_permission'   => PermissionType::GLOBAL->value,
            'allowed_companies'     => [$company->id],
            'default_company_id'    => $company->id,
        ])
        ->call('create')
        ->assertHasFormErrors(['password_confirmation' => 'same']);

    expect(User::query()->where('email', 'strict-writes-mismatch@example.com')->exists())->toBeFalse();
});
