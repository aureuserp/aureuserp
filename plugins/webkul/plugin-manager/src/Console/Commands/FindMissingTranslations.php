<?php

namespace Webkul\PluginManager\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use RuntimeException;
use Throwable;

class FindMissingTranslations extends Command
{
    protected $signature = 'translations:check
                            {--locale= : Check only a specific locale against EN.}
                            {--plugin= : Check only a specific plugin.}
                            {--details : Show detailed error information.}';

    protected $description = 'Check translation files consistency across all plugins (EN is canonical).';

    protected const BASE_LOCALE = 'en';

    protected const MAX_DISPLAY_ITEMS = 10;

    protected const PLUGIN_DIRECTORIES = [
        'plugins/webkul',
    ];

    protected const ROOT_LANG_DIRECTORY = 'lang';

    protected const PLUGIN_LANG_PATHS = [
        '/src/Resources/lang',
        '/resources/lang',
    ];

    protected static function supportedLocales(): array
    {
        return array_keys(config('app.supported_locales', ['en' => []]));
    }

    protected bool $hasError = false;

    protected Collection $errors;

    protected Collection $results;

    public function __construct()
    {
        parent::__construct();

        $this->errors = collect();

        $this->results = collect();
    }

    public function handle(): int
    {
        $targetLocale = $this->option('locale');

        $targetPlugin = $this->option('plugin');

        $showDetails = $this->option('details');

        $this->displayHeader($targetLocale, $targetPlugin);

        if (! $targetPlugin) {
            $this->processLangFolder('Root', base_path(self::ROOT_LANG_DIRECTORY), $targetLocale);
        }

        $this->processPlugins($targetPlugin, $targetLocale);

        $this->displayResults($showDetails);

        return $this->hasError ? self::FAILURE : self::SUCCESS;
    }

    protected function displayHeader(?string $targetLocale, ?string $targetPlugin): void
    {
        $this->newLine();

        $this->info('Translations Checker');

        $this->line('   Canonical Locale: <fg=cyan>'.Str::upper(self::BASE_LOCALE).'</>');

        if ($targetLocale) {
            $this->line("   Filter Locale: <fg=yellow>{$targetLocale}</>");
        }

        if ($targetPlugin) {
            $this->line("   Filter Plugin: <fg=yellow>{$targetPlugin}</>");
        }

        $this->newLine();
    }

    protected function processPlugins(?string $targetPlugin, ?string $targetLocale): void
    {
        collect(self::PLUGIN_DIRECTORIES)
            ->map(fn ($dir) => base_path($dir))
            ->filter(fn ($path) => File::isDirectory($path))
            ->flatMap(fn ($basePath) => File::directories($basePath))
            ->when($targetPlugin, fn ($collection) => $collection->filter(
                fn ($path) => Str::lower(basename($path)) === Str::lower($targetPlugin)
            ))
            ->each(function ($pluginDir) use ($targetLocale) {
                $pluginName = basename($pluginDir);

                $langPath = collect(self::PLUGIN_LANG_PATHS)
                    ->map(fn ($path) => $pluginDir.$path)
                    ->first(fn ($path) => File::isDirectory($path));

                if ($langPath) {
                    $this->processLangFolder($pluginName, $langPath, $targetLocale);
                }
            });
    }

    protected function processLangFolder(string $name, string $langRoot, ?string $targetLocale): void
    {
        $langRoot = str_replace('\\', '/', $langRoot);

        $enPath = $langRoot.'/'.self::BASE_LOCALE;

        if (! File::isDirectory($enPath)) {
            return;
        }

        $enFiles = $this->getPhpFiles($enPath);

        if ($enFiles->isEmpty()) {
            return;
        }

        $enFilesRel = $enFiles->map(fn ($f) => Str::after($f, $enPath.'/'));

        $locales = collect(self::supportedLocales())
            ->reject(fn ($d) => $d === self::BASE_LOCALE)
            ->when($targetLocale, fn ($collection) => $collection->filter(
                fn ($l) => Str::lower($l) === Str::lower($targetLocale)
            ))
            ->sort()
            ->values();

        if ($locales->isEmpty()) {
            return;
        }

        $locales->each(function ($locale) use ($name, $langRoot, $enPath, $enFilesRel) {
            $localePath = "{$langRoot}/{$locale}";

            if (! File::isDirectory($localePath)) {
                $this->results->push([
                    'plugin'  => $name,
                    'locale'  => $locale,
                    'status'  => 'fail',
                    'issues'  => 'missing locale directory',
                ]);

                $this->errors->push([
                    'plugin'  => $name,
                    'locale'  => $locale,
                    'type'    => 'missing_files',
                    'files'   => ['(entire locale directory)'],
                ]);

                $this->hasError = true;

                return;
            }

            $result = $this->checkLocale($name, $langRoot, $enPath, $enFilesRel, $locale);

            $this->results->push($result);

            if ($result['status'] !== 'pass') {
                $this->hasError = true;
            }
        });
    }

    protected function checkLocale(
        string $pluginName,
        string $langRoot,
        string $enPath,
        Collection $enFilesRel,
        string $locale
    ): array {
        $localePath = "{$langRoot}/{$locale}";

        $issues = [];

        $missingFiles = $enFilesRel->filter(
            fn ($relFile) => ! File::exists("{$localePath}/{$relFile}")
        )->values();

        if ($missingFiles->isNotEmpty()) {
            $issues['missing_files'] = $missingFiles->all();

            $this->errors->push([
                'plugin'  => $pluginName,
                'locale'  => $locale,
                'type'    => 'missing_files',
                'files'   => $missingFiles->all(),
            ]);
        }

        $localeFiles = $this->getPhpFiles($localePath);

        $localeFilesRel = $localeFiles->map(fn ($f) => Str::after($f, $localePath.'/'));

        $extraFiles = $localeFilesRel->diff($enFilesRel)->values();

        if ($extraFiles->isNotEmpty()) {
            $issues['extra_files'] = $extraFiles->all();

            $this->errors->push([
                'plugin'  => $pluginName,
                'locale'  => $locale,
                'type'    => 'extra_files',
                'files'   => $extraFiles->all(),
            ]);
        }

        $missingKeys = [];
        $extraKeys = [];
        $orderIssues = [];
        $structureIssues = [];

        $enFilesRel->each(function ($relFile) use ($enPath, $localePath, &$missingKeys, &$extraKeys, &$orderIssues, &$structureIssues, &$issues) {
            $enFile = "{$enPath}/{$relFile}";

            $localeFile = "{$localePath}/{$relFile}";

            if (! File::exists($localeFile)) {
                return;
            }

            try {
                $this->parseTranslationFile($localeFile);

                $enKeysWithLines = $this->flattenArrayWithLines($enFile);

                $locKeysWithLines = $this->flattenArrayWithLines($localeFile);

                $enKeys = collect($enKeysWithLines)->keys();

                $locKeys = collect($locKeysWithLines)->keys();

                $fileMissing = $enKeys->diff($locKeys);

                if ($fileMissing->isNotEmpty()) {
                    $missingWithLines = $fileMissing->mapWithKeys(
                        fn ($key) => [$key => $enKeysWithLines[$key] ?? null]
                    )->all();

                    $missingKeys[$relFile] = $missingWithLines;
                }

                $fileExtra = $locKeys->diff($enKeys);

                if ($fileExtra->isNotEmpty()) {
                    $extraWithLines = $fileExtra->mapWithKeys(
                        fn ($key) => [$key => $locKeysWithLines[$key] ?? null]
                    )->all();

                    $extraKeys[$relFile] = $extraWithLines;
                }

                $commonEnKeys = $enKeys->intersect($locKeys)->values();

                $commonLocKeys = $locKeys->intersect($enKeys)->values();

                if ($commonEnKeys->isNotEmpty() && $commonEnKeys->toArray() !== $commonLocKeys->toArray()) {
                    $orderIssues[$relFile] = $this->getOrderMismatches($commonEnKeys, $commonLocKeys);
                }

                $structureMismatches = $this->getStructureMismatches($enFile, $localeFile);

                if (! empty($structureMismatches)) {
                    $structureIssues[$relFile] = $structureMismatches;
                }
            } catch (Throwable) {
                $issues['parse_errors'][] = $relFile;
            }
        });

        if (! empty($missingKeys)) {
            $issues['missing_keys'] = $missingKeys;

            $this->errors->push([
                'plugin'  => $pluginName,
                'locale'  => $locale,
                'type'    => 'missing_keys',
                'data'    => $missingKeys,
            ]);
        }

        if (! empty($extraKeys)) {
            $issues['extra_keys'] = $extraKeys;

            $this->errors->push([
                'plugin'  => $pluginName,
                'locale'  => $locale,
                'type'    => 'extra_keys',
                'data'    => $extraKeys,
            ]);
        }

        if (! empty($orderIssues)) {
            $issues['order_issues'] = $orderIssues;

            $this->errors->push([
                'plugin'  => $pluginName,
                'locale'  => $locale,
                'type'    => 'order_issues',
                'data'    => $orderIssues,
            ]);
        }

        if (! empty($structureIssues)) {
            $issues['structure_issues'] = $structureIssues;

            $this->errors->push([
                'plugin'  => $pluginName,
                'locale'  => $locale,
                'type'    => 'structure_issues',
                'data'    => $structureIssues,
            ]);
        }

        return [
            'plugin'  => $pluginName,
            'locale'  => $locale,
            'status'  => empty($issues) ? 'pass' : 'fail',
            'issues'  => $this->buildIssueSummary($issues),
        ];
    }

    protected function getOrderMismatches(Collection $enKeys, Collection $locKeys): array
    {
        $mismatches = [];

        $enArray = $enKeys->values()->all();

        $locArray = $locKeys->values()->all();

        for ($i = 0; $i < count($enArray); $i++) {
            if (! isset($locArray[$i]) || $enArray[$i] !== $locArray[$i]) {
                $mismatches[] = [
                    'position' => $i + 1,
                    'en_key'   => $enArray[$i],
                    'loc_key'  => $locArray[$i] ?? '(missing)',
                ];

                if (count($mismatches) >= self::MAX_DISPLAY_ITEMS) {
                    break;
                }
            }
        }

        return $mismatches;
    }

    protected function buildIssueSummary(array $issues): string
    {
        $summary = collect();

        if (! empty($issues['missing_files'])) {
            $summary->push(count($issues['missing_files']).' missing file(s)');
        }

        if (! empty($issues['extra_files'])) {
            $summary->push(count($issues['extra_files']).' extra file(s)');
        }

        if (! empty($issues['missing_keys'])) {
            $count = collect($issues['missing_keys'])->map(fn ($keys) => count($keys))->sum();

            $summary->push("{$count} missing key(s)");
        }

        if (! empty($issues['extra_keys'])) {
            $count = collect($issues['extra_keys'])->map(fn ($keys) => count($keys))->sum();

            $summary->push("{$count} extra key(s)");
        }

        if (! empty($issues['order_issues'])) {
            $fileCount = count($issues['order_issues']);

            $summary->push("{$fileCount} file(s) with key order issue(s)");
        }

        if (! empty($issues['structure_issues'])) {
            $fileCount = count($issues['structure_issues']);

            $summary->push("{$fileCount} file(s) with structure issue(s)");
        }

        if (! empty($issues['parse_errors'])) {
            $summary->push(count($issues['parse_errors']).' parse error(s)');
        }

        return $summary->implode(', ') ?: '-';
    }

    protected function displayResults(bool $showDetails): void
    {
        if ($this->results->isEmpty()) {
            $this->warn('No translations found to check.');

            return;
        }

        $grouped = $this->results->groupBy('plugin');

        $passCount = 0;

        $failCount = 0;

        $grouped->each(function ($localeResults, $plugin) use (&$passCount, &$failCount) {
            $this->info("  {$plugin}");

            $tableRows = $localeResults->map(function ($result) use (&$passCount, &$failCount) {
                $isPassing = $result['status'] === 'pass';

                $statusIcon = $isPassing ? '<fg=green>PASS</>' : '<fg=red>FAIL</>';

                $issueText = $isPassing ? '<fg=green>OK</>' : "<fg=red>{$result['issues']}</>";

                $isPassing ? $passCount++ : $failCount++;

                return [$result['locale'], $statusIcon, $issueText];
            })->all();

            $this->table(['Locale', 'Status', 'Issues'], $tableRows);

            $this->newLine();
        });

        $this->displaySummary($passCount, $failCount, $showDetails);
    }

    protected function displaySummary(int $passCount, int $failCount, bool $showDetails): void
    {
        $this->line(str_repeat('-', 60));

        $total = $passCount + $failCount;

        $this->line("Summary: {$total} locale(s) checked");

        $this->line("   <fg=green>Passed:</> {$passCount}");

        $this->line("   <fg=red>Failed:</> {$failCount}");

        $this->line(str_repeat('-', 60));

        if ($this->hasError) {
            $this->newLine();

            $this->error('Translations check failed!');

            if ($showDetails) {
                $this->displayDetailedErrors();
            } else {
                $this->newLine();

                $this->line('<fg=yellow>Use --details flag to see specific missing/extra keys.</>');
            }
        } else {
            $this->newLine();

            $this->info('All translations are synchronized with EN!');
        }

        $this->newLine();
    }

    protected function displayDetailedErrors(): void
    {
        $this->newLine();

        $this->line('<fg=yellow;options=bold>Detailed Issues:</>');

        $this->newLine();

        $grouped = $this->errors->groupBy(fn ($error) => "{$error['plugin']}:{$error['locale']}");

        $grouped->each(function ($errors, $key) {
            [$plugin, $locale] = explode(':', $key);

            $this->line("<fg=cyan>[{$plugin}]</> <fg=yellow>{$locale}</>");

            $errors->each(fn ($error) => $this->displayError($error));

            $this->newLine();
        });
    }

    protected function displayError(array $error): void
    {
        match ($error['type']) {
            'missing_files'    => $this->displayMissingFiles($error['files']),
            'extra_files'      => $this->displayExtraFiles($error['files']),
            'missing_keys'     => $this->displayMissingKeys($error['data']),
            'extra_keys'       => $this->displayExtraKeys($error['data']),
            'order_issues'     => $this->displayOrderIssues($error['data']),
            'structure_issues' => $this->displayStructureIssues($error['data']),
            default            => null,
        };
    }

    protected function displayMissingFiles(array $files): void
    {
        $this->line('  <fg=red>Missing files:</>');

        collect($files)->each(fn ($file) => $this->line("    - {$file}"));

        $this->newLine();
    }

    protected function displayExtraFiles(array $files): void
    {
        $this->line('  <fg=magenta>Extra files (not in EN):</>');

        collect($files)->each(fn ($file) => $this->line("    - {$file}"));

        $this->newLine();
    }

    protected function displayMissingKeys(array $data): void
    {
        $this->line('  <fg=red>Missing keys:</>');

        collect($data)->each(function ($keys, $file) {
            $this->line("    <fg=white;options=bold>{$file}:</>");

            $this->displayKeysWithLines($keys);
        });

        $this->newLine();
    }

    protected function displayExtraKeys(array $data): void
    {
        $this->line('  <fg=magenta>Extra keys (not in EN):</>');

        collect($data)->each(function ($keys, $file) {
            $this->line("    <fg=white;options=bold>{$file}:</>");

            $this->displayKeysWithLines($keys);
        });

        $this->newLine();
    }

    protected function displayOrderIssues(array $data): void
    {
        $this->line('  <fg=yellow>Key order differs from EN:</>');

        collect($data)->each(function ($mismatches, $file) {
            $this->line("    <fg=white;options=bold>{$file}:</>");

            collect($mismatches)->each(function ($mismatch) {
                $this->line("      Position {$mismatch['position']}: EN has '{$mismatch['en_key']}', locale has '{$mismatch['loc_key']}'");
            });
        });

        $this->newLine();
    }

    protected function displayKeysWithLines(array $keys): void
    {
        $keysList = collect($keys);

        $displayed = 0;

        $keysList->each(function ($line, $key) use (&$displayed, $keysList) {
            if ($displayed >= self::MAX_DISPLAY_ITEMS) {
                if ($displayed === self::MAX_DISPLAY_ITEMS) {
                    $remaining = $keysList->count() - self::MAX_DISPLAY_ITEMS;

                    $this->line("      ... and {$remaining} more");
                }

                return false;
            }

            $lineInfo = $line ? "Line {$line}:" : 'Line ?:';

            $this->line("      {$lineInfo} '{$key}'");

            $displayed++;
        });
    }

    protected function displayStructureIssues(array $data): void
    {
        $this->line('  <fg=yellow>Structure differs from EN:</>');

        collect($data)->each(function ($mismatches, $file) {
            $this->line("    <fg=white;options=bold>{$file}:</>");

            collect($mismatches)->each(function ($mismatch) {
                if (isset($mismatch['line'])) {
                    $this->line("      Line {$mismatch['line']}: {$mismatch['message']}");

                    collect(['en_structure', 'locale_structure', 'en_content', 'locale_content'])
                        ->each(function ($field) use ($mismatch) {
                            if (isset($mismatch[$field])) {
                                $label = Str::startsWith($field, 'en_') ? 'EN' : 'Locale';

                                $this->line("        {$label}: {$mismatch[$field]}");
                            }
                        });
                } else {
                    $this->line("      {$mismatch['message']}");
                }
            });
        });

        $this->newLine();
    }

    protected function getStructureMismatches(string $enFile, string $localeFile): array
    {
        $enLines = collect(File::lines($enFile)->toArray());

        $locLines = collect(File::lines($localeFile)->toArray());

        $mismatches = [];

        $enLineCount = $enLines->count();

        $locLineCount = $locLines->count();

        if ($enLineCount !== $locLineCount) {
            $mismatches[] = [
                'type'    => 'line_count',
                'message' => "Line count differs: EN has {$enLineCount} lines, locale has {$locLineCount} lines",
            ];
        }

        $maxLines = max($enLineCount, $locLineCount);

        $detailedMismatches = collect();

        for ($lineNum = 0; $lineNum < $maxLines; $lineNum++) {
            $enLine = $enLines->get($lineNum);

            $locLine = $locLines->get($lineNum);

            $displayLineNum = $lineNum + 1;

            if ($enLine === null) {
                $detailedMismatches->push([
                    'line'           => $displayLineNum,
                    'type'           => 'extra_line',
                    'message'        => 'Extra line in locale',
                    'locale_content' => $this->truncateLine($locLine),
                ]);

                continue;
            }

            if ($locLine === null) {
                $detailedMismatches->push([
                    'line'       => $displayLineNum,
                    'type'       => 'missing_line',
                    'message'    => 'Missing line in locale',
                    'en_content' => $this->truncateLine($enLine),
                ]);

                continue;
            }

            $enStructure = $this->extractLineStructure($enLine);

            $locStructure = $this->extractLineStructure($locLine);

            if ($enStructure !== $locStructure) {
                $detailedMismatches->push([
                    'line'             => $displayLineNum,
                    'type'             => 'structure_diff',
                    'message'          => $this->describeStructureDifference($enLine, $locLine),
                    'en_structure'     => $this->truncateLine($enStructure),
                    'locale_structure' => $this->truncateLine($locStructure),
                ]);
            }
        }

        if ($detailedMismatches->isNotEmpty()) {
            $mismatches = array_merge($mismatches, $detailedMismatches->take(self::MAX_DISPLAY_ITEMS)->all());

            if ($detailedMismatches->count() > self::MAX_DISPLAY_ITEMS) {
                $remaining = $detailedMismatches->count() - self::MAX_DISPLAY_ITEMS;

                $mismatches[] = [
                    'type'    => 'truncated',
                    'message' => "... and {$remaining} more structure differences",
                ];
            }
        }

        return $mismatches;
    }

    protected function describeStructureDifference(string $enLine, string $locLine): string
    {
        $enKey = $this->extractKey($enLine);

        $locKey = $this->extractKey($locLine);

        if ($enKey !== null && $locKey !== null && $enKey !== $locKey) {
            return "Key mismatch: EN has '{$enKey}', locale has '{$locKey}'";
        }

        $enIndent = Str::length($enLine) - Str::length(ltrim($enLine));

        $locIndent = Str::length($locLine) - Str::length(ltrim($locLine));

        if ($enIndent !== $locIndent) {
            return "Indentation mismatch: EN has {$enIndent} spaces, locale has {$locIndent} spaces";
        }

        $enTrimmed = trim($enLine);

        $locTrimmed = trim($locLine);

        if ($enTrimmed === '' && $locTrimmed !== '') {
            return 'EN has blank line, locale has content';
        }

        if ($enTrimmed !== '' && $locTrimmed === '') {
            return 'EN has content, locale has blank line';
        }

        return 'Structure pattern differs';
    }

    protected function extractKey(string $line): ?string
    {
        if (preg_match('/[\'"]([^\'"]+)[\'"]\s*=>/', $line, $matches)) {
            return $matches[1];
        }

        return null;
    }

    protected function truncateLine(string $line, int $maxLen = 60): string
    {
        $line = trim($line);

        return Str::length($line) > $maxLen
            ? Str::substr($line, 0, $maxLen - 3).'...'
            : $line;
    }

    protected function extractLineStructure(string $line): string
    {
        $trimmed = trim($line);

        if ($trimmed === '' || Str::startsWith($trimmed, ['//', '/*', '*'])) {
            return $line;
        }

        if (preg_match('/^(\s*)([\'"][^\'"]*[\'"])\s*=>\s*/', $line, $matches)) {
            return $matches[1].$matches[2].' =>';
        }

        if (preg_match('/^(\s*)([\[\],\];]+)/', $line, $matches)) {
            return $line;
        }

        if (Str::contains($line, 'return')) {
            return $line;
        }

        if (Str::contains($line, '<?php')) {
            return $line;
        }

        return $line;
    }

    protected function flattenArray(array $array, string $prefix = ''): array
    {
        return collect($array)->flatMap(function ($value, $key) use ($prefix) {
            $fullKey = $prefix === '' ? (string) $key : "{$prefix}.{$key}";

            return is_array($value)
                ? $this->flattenArray($value, $fullKey)
                : [$fullKey => true];
        })->all();
    }

    protected function flattenArrayWithLines(string $file): array
    {
        $lines = collect(explode("\n", File::get($file)));

        $result = [];

        $keyStack = [];

        $lines->each(function ($line, $lineNum) use (&$result, &$keyStack) {
            $displayLine = $lineNum + 1;

            if (preg_match('/^(\s*)[\'"]([^\'"]+)[\'"]\s*=>/', $line, $matches)) {
                $indent = Str::length($matches[1]);

                $key = $matches[2];

                $indentLevel = (int) ($indent / 4);

                while (count($keyStack) > $indentLevel) {
                    array_pop($keyStack);
                }

                $keyStack[$indentLevel] = $key;

                if (! preg_match('/=>\s*\[/', $line)) {
                    $fullKey = implode('.', array_slice($keyStack, 0, $indentLevel + 1));

                    $result[$fullKey] = $displayLine;
                }
            }
        });

        return $result;
    }

    protected function parseTranslationFile(string $file): array
    {
        ob_start();

        try {
            $result = include $file;
        } catch (Throwable $e) {
            ob_end_clean();

            throw new RuntimeException("Failed to include file: {$file} - ".$e->getMessage());
        }

        ob_end_clean();

        if (! is_array($result)) {
            throw new RuntimeException("Translation file does not return an array: {$file}");
        }

        return $result;
    }

    protected function getPhpFiles(string $dir): Collection
    {
        if (! File::isDirectory($dir)) {
            return collect();
        }

        return collect(File::allFiles($dir))
            ->filter(fn ($file) => $file->getExtension() === 'php')
            ->map(fn ($file) => str_replace('\\', '/', $file->getPathname()))
            ->sort()
            ->values();
    }
}
