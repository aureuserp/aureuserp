<?php

namespace Webkul\Crm\Console\Commands;

use Illuminate\Console\Command;
use Webkul\Crm\Services\VisitsToLeads;

class ProcessVisitsToLeads extends Command
{
    protected $signature = 'crm:visits-to-leads
        {--existing : Also apply the rules to visitors recorded before the setting was enabled}';

    protected $description = 'Create CRM leads from website visits that match a Visitors to Leads rule';

    public function handle(VisitsToLeads $visitsToLeads): int
    {
        if (! VisitsToLeads::enabled()) {
            $this->components->info('Visitors to Leads is disabled in CRM settings, or the website plugin is not installed.');

            return self::SUCCESS;
        }

        $created = $visitsToLeads->processPending();

        if ($this->option('existing')) {
            $created += $visitsToLeads->generateForExistingVisitors();
        }

        $this->components->info("{$created} lead(s) created from website visitors.");

        return self::SUCCESS;
    }
}
