<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Webkul\Crm\Enums\LeadType;
use Webkul\Crm\Models\Lead;
use Webkul\Crm\Models\Stage;
use Webkul\Crm\Settings\SMSSettings;
use Webkul\PluginManager\Models\Plugin;
use Webkul\PluginManager\Package;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;

require_once __DIR__.'/../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../../support/tests/Helpers/FilamentHelper.php';

/**
 * Shared setup for the CRM feature tests: installs the plugin (and its dependencies)
 * once, signs in users with given `*_crm_*` permissions and builds CRM records.
 *
 * Call `CRMHelper::bootstrap()` in each test file's `beforeEach()`.
 */
class CRMHelper
{
    /**
     * Every ability a CRM resource can have, used to build permission names.
     */
    public const ALL_AFFIXES = [
        'view_any', 'view', 'create', 'update', 'delete', 'delete_any',
        'restore', 'restore_any', 'force_delete', 'force_delete_any', 'reorder',
    ];

    public static function bootstrap(): void
    {
        TestBootstrapHelper::ensurePluginInstalled('crm');

        foreach (['contacts', 'sales', 'crm'] as $plugin) {
            DB::table('plugins')->updateOrInsert(
                ['name' => $plugin],
                ['is_installed' => true, 'is_active' => true, 'updated_at' => now()],
            );
        }

        Package::$plugins = Plugin::all()->keyBy('name');

        URL::resolveMissingNamedRoutesUsing(fn () => '#');
    }

    /**
     * Signs in a user with exactly the given permissions (global record access, first company).
     *
     * The shared helper leaves the default guard on `sanctum` (for API tests). Filament uses
     * the `web` guard, and permission checks on `sanctum` can return stale results after an
     * earlier check in the same test (e.g. a second sign-in), so `web` is restored here.
     *
     * @param  array<int, string>  $permissions
     */
    public static function actingAs(array $permissions = []): User
    {
        $user = FilamentHelper::actingAs($permissions);

        Auth::shouldUse('web');

        return $user;
    }

    /**
     * Signs in a user with every permission of the given CRM resources.
     *
     * @param  array<int, string>  $resources  Permission suffixes, e.g. `['opportunity', 'lead', 'lost::reason']`.
     */
    public static function actingAsCRMUser(array $resources): User
    {
        return static::actingAs(static::permissionsFor($resources));
    }

    /**
     * Permission names for the given CRM resources, e.g. `view_any_crm_opportunity`.
     *
     * @param  array<int, string>  $resources
     * @param  array<int, string>  $affixes
     * @return array<int, string>
     */
    public static function permissionsFor(array $resources, array $affixes = self::ALL_AFFIXES): array
    {
        $permissions = [];

        foreach ($resources as $resource) {
            foreach ($affixes as $affix) {
                $permissions[] = "{$affix}_crm_{$resource}";
            }
        }

        return $permissions;
    }

    public static function configureSMSGateway(): void
    {
        $settings = app(SMSSettings::class);
        $settings->is_active = true;
        $settings->auth_key = 'test-key';
        $settings->sender_id = 'AUREUS';
        $settings->route = '4';
        $settings->save();
    }

    public static function companyId(): int
    {
        return (int) Company::query()->value('id');
    }

    /**
     * An opportunity in the current company, assigned to the signed-in user unless overridden.
     *
     * @param  array<string, mixed>  $attributes
     */
    public static function opportunity(array $attributes = []): Lead
    {
        return Lead::factory()->create([
            'type'       => LeadType::OPPORTUNITY,
            'company_id' => static::companyId(),
            'user_id'    => auth()->id() ?? User::query()->value('id'),
            ...$attributes,
        ]);
    }

    /**
     * A lead (not yet an opportunity) in the current company.
     *
     * @param  array<string, mixed>  $attributes
     */
    public static function lead(array $attributes = []): Lead
    {
        return Lead::factory()->lead()->create([
            'company_id' => static::companyId(),
            'user_id'    => auth()->id() ?? User::query()->value('id'),
            ...$attributes,
        ]);
    }

    public static function firstStage(): Stage
    {
        return Stage::query()->orderBy('sort')->firstOrFail();
    }

    public static function wonStage(): Stage
    {
        return Stage::query()->where('is_won', true)->orderBy('sort')->first()
            ?? Stage::factory()->won()->create(['name' => 'Won (test)']);
    }
}
