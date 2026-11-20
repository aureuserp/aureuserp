<?php

namespace Webkul\Website\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Throwable;
use Webkul\Website\Services\IpGeolocation;

class ImportIpCountries extends Command
{
    protected $signature = 'website:import-ip-countries
        {--file= : A local dbip-country-lite CSV or CSV.GZ file instead of downloading}
        {--month= : Month of the list to download (YYYY-MM, default: current month)}';

    protected $description = 'Import the free DB-IP "IP to Country Lite" list (CC BY 4.0) used to find a website visitor\'s country from the IP address';

    public function handle(): int
    {
        $path = $this->option('file') ?: $this->download();

        if (! $path || ! is_readable($path)) {
            $this->components->error('No IP list to import.');

            return self::FAILURE;
        }

        $handle = str_ends_with($path, '.gz') ? gzopen($path, 'rb') : fopen($path, 'rb');

        if (! $handle) {
            $this->components->error("Cannot read {$path}.");

            return self::FAILURE;
        }

        $staging = IpGeolocation::TABLE.'_import';

        DB::statement("DROP TABLE IF EXISTS {$staging}");
        DB::statement('CREATE TABLE '.$staging.' LIKE '.IpGeolocation::TABLE);

        $rows = [];
        $count = 0;
        $read = fn () => str_ends_with($path, '.gz') ? gzgets($handle) : fgets($handle);

        while (($line = $read()) !== false) {
            $columns = str_getcsv(trim($line));

            if (count($columns) < 3) {
                continue;
            }

            $from = IpGeolocation::toBinary($columns[0]);
            $to = IpGeolocation::toBinary($columns[1]);

            if ($from === null || $to === null || ! preg_match('/^[A-Z]{2}$/', $columns[2])) {
                continue;
            }

            $rows[] = ['ip_from' => $from, 'ip_to' => $to, 'country_code' => $columns[2]];

            if (count($rows) === 2000) {
                DB::table($staging)->insertOrIgnore($rows);
                $count += count($rows);
                $rows = [];
            }
        }

        if ($rows) {
            DB::table($staging)->insertOrIgnore($rows);
            $count += count($rows);
        }

        str_ends_with($path, '.gz') ? gzclose($handle) : fclose($handle);

        if (! $this->option('file')) {
            @unlink($path);
        }

        if ($count === 0) {
            DB::statement("DROP TABLE IF EXISTS {$staging}");
            $this->components->error('The list had no valid rows; the current data was kept.');

            return self::FAILURE;
        }

        $old = IpGeolocation::TABLE.'_old';

        DB::statement("DROP TABLE IF EXISTS {$old}");
        DB::statement('RENAME TABLE '.IpGeolocation::TABLE." TO {$old}, {$staging} TO ".IpGeolocation::TABLE);
        DB::statement("DROP TABLE IF EXISTS {$old}");

        Cache::forever(IpGeolocation::CACHE_VERSION_KEY, (int) Cache::get(IpGeolocation::CACHE_VERSION_KEY, 0) + 1);

        $this->components->info("Imported {$count} IP ranges.");

        return self::SUCCESS;
    }

    protected function download(): ?string
    {
        $month = $this->option('month') ?: now()->format('Y-m');

        if (! preg_match('/^\d{4}-\d{2}$/', $month)) {
            $this->components->error('--month must be YYYY-MM.');

            return null;
        }

        $target = storage_path("app/dbip-country-lite-{$month}.csv.gz");

        try {
            $response = Http::timeout(120)->sink($target)->get("https://download.db-ip.com/free/dbip-country-lite-{$month}.csv.gz");
        } catch (Throwable $exception) {
            $this->components->error('Download failed: '.$exception->getMessage());

            return null;
        }

        if (! $response->successful()) {
            @unlink($target);
            $this->components->error("Download failed (HTTP {$response->status()}). Try --month with an earlier month, or --file.");

            return null;
        }

        return $target;
    }
}
