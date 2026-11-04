<?php

namespace Webkul\Crm\Console\Commands;

use Illuminate\Console\Command;
use Throwable;
use Webkul\Crm\Models\MailServer;
use Webkul\Crm\Services\Mail\InboundMailFetcher;

class FetchInboundEmails extends Command
{
    protected $signature = 'crm:fetch-mail
        {--server= : Only read this incoming mail server id}';

    protected $description = 'Create CRM leads from emails received on the incoming mail servers';

    public function handle(InboundMailFetcher $fetcher): int
    {
        $servers = MailServer::query()
            ->active()
            ->when($this->option('server'), fn ($query, $id) => $query->whereKey($id))
            ->get();

        if ($servers->isEmpty()) {
            $this->components->info('No active incoming mail server.');

            return self::SUCCESS;
        }

        $failed = false;

        foreach ($servers as $server) {
            try {
                $counts = $fetcher->fetch($server);

                $summary = collect($counts)->filter()->map(fn (int $count, string $status): string => "{$status} {$count}")->implode(', ');

                $this->components->info($server->name.': '.($summary ?: 'no new email'));
            } catch (Throwable $exception) {
                $failed = true;

                $this->components->error($server->name.': '.$exception->getMessage());
            }
        }

        return $failed ? self::FAILURE : self::SUCCESS;
    }
}
