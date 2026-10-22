<?php

namespace Webkul\Crm\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Webkul\Crm\Services\LeadAssignment;
use Webkul\Crm\Settings\CrmSettings;
use Webkul\Sale\Models\Team;

class AssignLeads extends Command
{
    protected $signature = 'crm:assign-leads
        {--team= : Only assign the leads of this sales team id}
        {--scheduled : Run only when the repeated assignment is due}';

    protected $description = 'Assign leads to sales teams and salespeople using the CRM assignment rules';

    public function handle(CrmSettings $settings, LeadAssignment $assignment): int
    {
        if (! $settings->use_auto_assignment) {
            $this->components->info('Rule-based assignment is disabled in CRM settings.');

            return self::SUCCESS;
        }

        if ($this->option('scheduled')) {
            if ($settings->auto_assignment_action !== 'auto') {
                return self::SUCCESS;
            }

            if (filled($settings->auto_assignment_run_at) && Carbon::parse($settings->auto_assignment_run_at)->isFuture()) {
                return self::SUCCESS;
            }

            $settings->auto_assignment_run_at = static::nextRun($settings)->toIso8601String();
            $settings->save();
        }

        $team = filled($this->option('team')) ? Team::query()->withoutGlobalScopes()->findOrFail($this->option('team')) : null;

        $result = $assignment->run($team);

        $this->components->info("Assigned {$result['teams']} lead(s) to sales teams and {$result['salespeople']} to salespeople.");

        return self::SUCCESS;
    }

    public static function nextRun(CrmSettings $settings, ?Carbon $from = null): Carbon
    {
        $from ??= now();

        $number = max(1, (int) $settings->auto_assignment_interval_number);

        return match ($settings->auto_assignment_interval_type) {
            'minutes' => $from->copy()->addMinutes($number),
            'hours'   => $from->copy()->addHours($number),
            'weeks'   => $from->copy()->addWeeks($number),
            default   => $from->copy()->addDays($number),
        };
    }
}
