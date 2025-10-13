<?php

namespace Webkul\Support\Console\Commands;

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
use Webkul\Support\Models\Company;
use Webkul\Support\Models\Currency;

use function Laravel\Prompts\password;
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
        {--admin-password= : Admin user password}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install the ERP system with Filament and Filament Shield';

    /**
     * Execute the console command.
     */
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

        $this->runSeeder();

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

        $defaultCompany->update(['creator_id' => $adminUser->id]);

        $adminRoleName = $this->getAdminRoleName();

        if (! $adminUser->hasRole($adminRoleName)) {
            $adminUser->assignRole($adminRoleName);
        }

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

        if (PHP_OS_FAMILY == 'Darwin') {
            exec("open {$repoUrl}");
        }

        if (PHP_OS_FAMILY == 'Windows') {
            exec("start {$repoUrl}");
        }

        if (PHP_OS_FAMILY == 'Linux') {
            exec("xdg-open {$repoUrl}");
        }
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
            'utm_campaigns'               => 'created_by',
            'utm_mediums'                 => 'creator_id',
            'utm_stages'                  => 'created_by',
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
                'payload' => Currency::first()?->id,
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