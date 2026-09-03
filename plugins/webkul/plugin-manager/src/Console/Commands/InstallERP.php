<?php

namespace Webkul\PluginManager\Console\Commands;

use BezhanSalleh\FilamentShield\Support\Utils;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Webkul\PluginManager\Package;
use Webkul\Support\Models\Company;
use Webkul\Support\Models\Country;
use Webkul\Support\Models\Currency;

use function Laravel\Prompts\password;
use function Laravel\Prompts\search;
use function Laravel\Prompts\text;

class InstallERP extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'erp:install
        {--force : Force reinstallation without confirmation}
        {--admin-name= : Admin user name}
        {--admin-email= : Admin user email}
        {--admin-password= : Admin user password}
        {--country= : Country name or two letter code used to derive the default currency}
        {--currency= : Currency ISO code for the default company, overrides the country}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install the ERP system with Filament and Filament Shield';

    /**
     * Execute the console command.
     */
    protected ?int $defaultCurrencyId = null;

    protected ?string $countryCode = null;

    /** @var array<int, array<string, mixed>>|null */
    protected ?array $countryData = null;

    /** @var array<int, array<string, mixed>>|null */
    protected ?array $currencyData = null;

    public function handle()
    {
        if (
            $this->isAlreadyInstalled()
            && ! $this->option('force')
        ) {
            if (! $this->handleReinstallation()) {
                $this->info('Installation cancelled.');

                return;
            }
        }

        $this->info('🚀 Starting ERP System Installation...');

        $this->runMigrations();

        $this->generateRolesAndPermissions();

        $this->storageLink();

        $this->resolveLocalisation();

        $this->runSeeder();

        $this->applyLocalisation();

        $this->createAdminUser();

        $this->markAsInstalled();

        Event::dispatch('aureus.installed');

        $this->info('🎉 ERP System installation completed successfully!');
    }

    /**
     * Check if the system is already installed.
     */
    protected function isAlreadyInstalled(): bool
    {
        $filePath = storage_path('installed');

        return File::exists($filePath);
    }

    /**
     * Handle reinstallation with warning and confirmation.
     */
    protected function handleReinstallation(): bool
    {
        $this->newLine();
        $this->error('⚠️  WARNING: AUREIUS ERP IS ALREADY INSTALLED!');
        $this->newLine();
        $this->warn('🚨 DANGER ZONE 🚨');
        $this->warn('Proceeding with reinstallation will:');
        $this->warn('• WIPE ALL EXISTING DATA');
        $this->warn('• DROP ALL DATABASE TABLES');
        $this->warn('• REMOVE ALL USER ACCOUNTS');
        $this->warn('• DELETE ALL COMPANY DATA');
        $this->warn('• RESET ALL CONFIGURATIONS');
        $this->newLine();
        $this->error('THIS ACTION CANNOT BE UNDONE!');
        $this->newLine();

        $confirmation = $this->ask('Type "REINSTALL" (in capital letters) to confirm you want to proceed with reinstallation');

        if ($confirmation !== 'REINSTALL') {
            $this->error('Confirmation failed. Installation cancelled for safety.');

            return false;
        }

        $doubleConfirmation = $this->confirm('Are you absolutely sure you want to wipe the database and reinstall? This is your last chance to cancel.');

        if (! $doubleConfirmation) {
            $this->info('Wise choice! Installation cancelled.');

            return false;
        }

        $this->info('🔄 Proceeding with reinstallation...');
        $this->wipeDatabase();
        $this->removeInstallationMarker();

        return true;
    }

    /**
     * Wipe the database for fresh installation.
     */
    protected function wipeDatabase(): void
    {
        $this->info('🗑️  Wiping database...');

        try {
            Artisan::call('migrate:fresh', [], $this->getOutput());
            $this->info('✅ Database wiped successfully.');
        } catch (Exception $e) {
            $this->error('❌ Failed to wipe database: '.$e->getMessage());

            $this->error('Please manually drop your database and create a new one before proceeding.');

            exit(1);
        }
    }

    /**
     * Mark the system as installed.
     */
    protected function markAsInstalled(): void
    {
        $filePath = storage_path('installed');

        $content = sprintf(
            "AureusERP is successfully installed.\nInstalled at: %s",
            now()->toDateTimeString(),
        );

        File::put($filePath, $content);
    }

    /**
     * Remove the installation marker file.
     */
    protected function removeInstallationMarker(): void
    {
        $filePath = storage_path('installed');

        if (File::exists($filePath)) {
            File::delete($filePath);
        }
    }

    /**
     * Run database migrations.
     */
    protected function runMigrations(): void
    {
        $this->info('⚙️ Running database migrations...');

        Artisan::call('migrate', [], $this->getOutput());

        $this->info('✅ Migrations completed successfully.');
    }

    /**
     * Run database seeders.
     */
    protected function runSeeder()
    {
        $this->info('⚙️ Running database seeders...');

        Artisan::call('db:seed', [], $this->getOutput());

        Package::syncPostgresSequences();

        $this->info('✅ Seeders completed successfully.');
    }

    /**
     * Generate roles and permissions using Filament Shield.
     */
    protected function generateRolesAndPermissions(): void
    {
        $this->info('🛡 Generating roles and permissions...');

        $adminRole = Role::firstOrCreate([
            'name'       => $this->getAdminRoleName(),
            'is_default' => true,
        ]);

        Artisan::call('shield:generate', [
            '--all'    => true,
            '--option' => 'permissions',
            '--panel'  => 'admin',
        ], $this->getOutput());

        $permissions = Permission::all();
        $adminRole->syncPermissions($permissions);

        $this->info('✅ Roles and permissions generated and assigned successfully.');
    }

    /**
     * Resolve the country and currency before the seeders run, so the default
     * company and everything seeded from it use the right currency.
     */
    protected function resolveLocalisation(): void
    {
        $this->info('🌍 Resolving the default country and currency...');

        $this->countryCode = $this->resolveCountryCode();

        $currencyCode = $this->option('currency') ?: $this->currencyCodeForCountry($this->countryCode);

        if (blank($currencyCode)) {
            return;
        }

        config(['app.currency' => $currencyCode]);

        $this->info("✅ Using {$currencyCode} as the default currency.");
    }

    /**
     * Stamp the resolved country and currency onto the seeded default company.
     */
    protected function applyLocalisation(): void
    {
        $company = Company::first();

        if (! $company) {
            $this->warn('⚠️  No default company found. Skipping country and currency configuration.');

            return;
        }

        $currency = $this->resolveCurrencyOption() ?? Currency::resolveDefault();

        if (! $currency) {
            $this->warn('⚠️  No currency could be resolved. Keeping the seeded default.');

            return;
        }

        $country = $this->countryCode
            ? Country::query()->whereRaw('LOWER(code) = ?', [strtolower($this->countryCode)])->first()
            : null;

        $company->update([
            'country_id'  => $country?->id ?? $company->country_id,
            'currency_id' => $currency->id,
        ]);

        $this->defaultCurrencyId = $currency->id;
    }

    /**
     * Resolve the two letter country code from the command option or a prompt.
     *
     * The countries table is not seeded yet at this point, so the shipped data
     * file is the source of truth here.
     */
    protected function resolveCountryCode(): ?string
    {
        $countries = $this->countryData();

        $input = $this->option('country');

        if (filled($input)) {
            $match = collect($countries)->first(fn (array $country) => strcasecmp($country['code'], $input) === 0
                || strcasecmp($country['name'], $input) === 0);

            if (! $match) {
                $this->error("Unknown country: {$input}");

                exit(1);
            }

            return $match['code'];
        }

        if (filled($this->option('currency')) || ! $this->canPrompt()) {
            return null;
        }

        return search(
            label: 'Which country is your business based in?',
            options: fn (string $value) => collect($countries)
                ->when($value !== '', fn ($collection) => $collection->filter(
                    fn (array $country) => str_contains(strtolower($country['name']), strtolower($value))
                ))
                ->sortBy('name')
                ->take(20)
                ->pluck('name', 'code')
                ->all(),
            placeholder: 'Search for a country...',
            hint: 'The default currency is derived from the country you pick.',
        );
    }

    /**
     * Map a country code to its currency ISO code using the shipped data files.
     */
    protected function currencyCodeForCountry(?string $countryCode): ?string
    {
        if (blank($countryCode)) {
            return null;
        }

        $country = collect($this->countryData())
            ->first(fn (array $country) => strcasecmp($country['code'], $countryCode) === 0);

        $currencyId = (int) ($country['currency_id'] ?? 0);

        if (! $currencyId) {
            return null;
        }

        return $this->currencyData()[$currencyId - 1]['name'] ?? null;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    protected function countryData(): array
    {
        return $this->countryData ??= json_decode(
            File::get(base_path('plugins/webkul/security/src/Data/countries.json')),
            true,
        ) ?: [];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    protected function currencyData(): array
    {
        return $this->currencyData ??= json_decode(
            File::get(base_path('plugins/webkul/security/src/Data/currencies.json')),
            true,
        ) ?: [];
    }

    protected function resolveCurrencyOption(): ?Currency
    {
        $code = $this->option('currency');

        if (blank($code)) {
            return null;
        }

        $currency = Currency::findByCode($code);

        if (! $currency) {
            $this->error("Unknown currency: {$code}");

            exit(1);
        }

        return $currency;
    }

    /**
     * Determine whether the command can safely render an interactive prompt.
     */
    protected function canPrompt(): bool
    {
        return $this->input->isInteractive()
            && defined('STDIN')
            && stream_isatty(STDIN);
    }

    /**
     * Resolve the country from the command option or an interactive prompt.
     */
    protected function resolveCountry(): ?Country
    {
        $input = $this->option('country');

        if (filled($input)) {
            $country = Country::query()
                ->whereRaw('LOWER(code) = ?', [strtolower($input)])
                ->orWhereRaw('LOWER(name) = ?', [strtolower($input)])
                ->first();

            if (! $country) {
                $this->error("Unknown country: {$input}");

                exit(1);
            }

            return $country;
        }

        if (filled($this->option('currency')) || ! $this->canPrompt()) {
            return null;
        }

        $countryId = search(
            label: 'Which country is your business based in?',
            options: fn (string $value) => Country::query()
                ->when($value !== '', fn ($query) => $query->where('name', 'like', "%{$value}%"))
                ->orderBy('name')
                ->limit(20)
                ->pluck('name', 'id')
                ->all(),
            placeholder: 'Search for a country...',
            hint: 'The default currency is derived from the country you pick.',
        );

        return Country::find($countryId);
    }

    /**
     * Create the initial Admin user with the Super Admin role.
     */
    protected function createAdminUser(): void
    {
        $this->info('👤 Creating an Admin user...');

        $defaultCompany = Company::first();

        $userModel = app(Utils::getAuthProviderFQCN());

        $adminData = $this->getAdminCredentials($userModel);

        $adminData['resource_permission'] = 'global';

        $adminData['default_company_id'] = $defaultCompany->id;

        $adminData['is_default'] = true;

        $adminUser = $userModel::updateOrCreate(['email' => $adminData['email']], $adminData);

        $adminUser->allowedCompanies()->syncWithoutDetaching([$defaultCompany->id]);

        $defaultCompany->update(['creator_id' => $adminUser->id]);

        $adminRoleName = $this->getAdminRoleName();

        if (! $adminUser->hasRole($adminRoleName)) {
            $adminUser->assignRole($adminRoleName);
        }

        $adminUser->allowedCompanies()->syncWithoutDetaching(Company::pluck('id')->all());

        $this->backfillMissingCreatorIds($adminUser);

        $this->syncDefaultSettings($adminUser);

        $this->info("✅ Admin user '{$adminUser->name}' created and assigned the '{$this->getAdminRoleName()}' role successfully.");
    }

    /**
     * Get admin data from command options or interactive prompts.
     */
    protected function getAdminCredentials(Model $userModel): array
    {
        $name = $this->option('admin-name');

        if (empty($name)) {
            $name = text(
                'Name',
                default: 'Example',
                required: true
            );
        }

        $email = $this->option('admin-email');

        if (empty($email)) {
            $email = text(
                'Email address',
                default: 'admin@example.com',
                required: true,
                validate: fn ($email) => $this->validateAdminEmail($email, $userModel)
            );
        } else {
            $emailValidation = $this->validateAdminEmail($email, $userModel);

            if ($emailValidation) {
                $this->error("Invalid email: {$emailValidation}");

                exit(1);
            }
        }

        $passwordInput = $this->option('admin-password');

        if (empty($passwordInput)) {
            $passwordInput = password(
                'Password',
                required: true,
                validate: fn ($value) => $this->validateAdminPassword($value)
            );
        } else {
            $passwordValidation = $this->validateAdminPassword($passwordInput);

            if ($passwordValidation) {
                $this->error("Invalid password: {$passwordValidation}");

                exit(1);
            }
        }

        return [
            'name'     => $name,
            'email'    => $email,
            'password' => Hash::make($passwordInput),
        ];
    }

    /**
     * Retrieve the Super Admin role name from the configuration.
     */
    protected function getAdminRoleName(): string
    {
        return Utils::getPanelUserRoleName();
    }

    /**
     * Validate the provided admin email.
     */
    protected function validateAdminEmail(string $email, Model $userModel): ?string
    {
        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return 'The email address must be valid.';
        }

        if ($userModel::where('email', $email)->exists()) {
            return 'A user with this email address already exists.';
        }

        return null;
    }

    /**
     * Validate the provided admin password.
     */
    protected function validateAdminPassword(string $password): ?string
    {
        return strlen($password) >= 8 ? null : 'The password must be at least 8 characters long.';
    }

    /**
     * Ask the user to star the GitHub repository.
     */
    protected function askToStarGithubRepository(): void
    {
        if (! $this->confirm('Would you like to star our repo on GitHub?')) {
            return;
        }

        $repoUrl = 'https://github.com/aureuserp/aureuserp';

        Package::openInBrowser($repoUrl);
    }

    /**
     * Storage link command to create a symbolic link from "public/storage" to "storage/app/public".
     */
    private function storageLink()
    {
        if (file_exists(public_path('storage'))) {
            return;
        }

        $this->info('🔗 Linking storage directory...');

        Artisan::call('storage:link', [], $this->getOutput());

        $this->info('✅ Storage directory linked successfully.');
    }

    public function backfillMissingCreatorIds($user)
    {
        $mappings = [
            'activity_plans'              => 'creator_id',
            'partners_partners'           => 'creator_id',
            'unit_of_measure_categories'  => 'creator_id',
            'unit_of_measures'            => 'creator_id',
            'utm_campaigns'               => 'creator_id',
            'utm_mediums'                 => 'creator_id',
            'utm_stages'                  => 'creator_id',
        ];

        collect($mappings)
            ->filter(fn ($column) => ! is_null($column))
            ->each(fn ($column, $table) => DB::table($table)->whereNull($column)->update([$column => $user->id]));
    }

    /**
     * Resolve default settings for the user.
     */
    private function syncDefaultSettings($user)
    {
        $settings = [
            [
                'group'   => 'general',
                'name'    => 'default_company_id',
                'payload' => $user->default_company_id,
            ],
            [
                'group'   => 'general',
                'name'    => 'default_role_id',
                'payload' => Role::first()?->id,
            ],
            [
                'group'   => 'currency',
                'name'    => 'default_currency_id',
                'payload' => $this->defaultCurrencyId ?? Currency::active()->first()?->id,
            ],
        ];

        foreach ($settings as $setting) {
            if (! isset($setting['payload'])) {
                continue;
            }

            DB::table('settings')->updateOrInsert(
                ['group' => $setting['group'], 'name' => $setting['name']],
                [
                    'payload'    => json_encode($setting['payload']),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
