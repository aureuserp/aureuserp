<?php

namespace Webkul\Website\Console\Commands;

use Illuminate\Console\Command;
use Webkul\Website\Services\VisitorTracker;

class DeleteInactiveVisitors extends Command
{
    protected $signature = 'website:delete-inactive-visitors {--days=60 : Delete anonymous visitors inactive for this many days}';

    protected $description = 'Delete website visitors without a contact who have not visited for a while, with their page views';

    public function handle(VisitorTracker $tracker): int
    {
        $count = $tracker->deleteInactive(max(1, (int) $this->option('days')));

        $this->components->info("Deleted {$count} inactive visitor(s).");

        return self::SUCCESS;
    }
}
