<?php

namespace Webkul\Recruitment;

use Webkul\Support\Console\Commands\InstallCommand;
use Webkul\Support\Console\Commands\UninstallCommand;
use Webkul\Support\Package;
use Webkul\Support\PackageServiceProvider;

class RecruitmentServiceProvider extends PackageServiceProvider
{
    public static string $name = 'recruitments';

    public static string $viewNamespace = 'recruitments';

    public function configureCustomPackage(Package $package): void
    {
        $package->name(static::$name)
            ->hasViews()
            ->hasTranslations()
            ->hasMigrations([
                '2025_01_06_133002_create_recruitments_stages_table',
                '2025_01_07_053021_create_recruitments_stages_jobs_table',
                '2025_01_09_071817_create_recruitments_degrees_table',
                '2025_01_09_082748_create_recruitments_refuse_reasons_table',
                '2025_01_09_095909_create_recruitments_applicant_categories_table',
                '2025_01_09_125852_create_recruitments_candidates_table',
                '2025_01_10_045048_create_recruitments_candidate_applicant_categories_table',
                '2025_01_10_082944_create_recruitments_candidate_skills_table',
                '2025_01_10_115422_create_recruitments_applicants_table',
                '2025_01_13_072547_create_recruitments_applicant_interviewers_table',
                '2025_01_13_075926_create_recruitments_applicant_applicant_categories_table',
                '2025_01_14_080159_add_is_default_column_stages_table',
                '2025_01_14_143102_add_columns_to_employees_job_positions_table',
                '2025_01_16_081327_create_recruitments_job_position_interviewers_table',
            ])
            ->runsMigrations()
            ->hasDependencies([
                'employees',
            ])
            ->hasSeeder('Webkul\\Recruitment\\Database\Seeders\\DatabaseSeeder')
            ->hasInstallCommand(function (InstallCommand $command) {
                $command
                    ->installDependencies()
                    ->runsMigrations()
                    ->runsSeeders();
            })
            ->hasUninstallCommand(function (UninstallCommand $command) {})
            ->icon('recruitments');
    }
}
