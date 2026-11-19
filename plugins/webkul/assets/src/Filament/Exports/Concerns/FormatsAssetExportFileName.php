<?php

namespace Webkul\Asset\Filament\Exports\Concerns;

use BackedEnum;
use DateTimeInterface;
use Filament\Actions\ExportAction;
use Filament\Actions\ExportBulkAction;
use Filament\Actions\Exports\Models\Export;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Schema;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use ReflectionProperty;

trait FormatsAssetExportFileName
{
    /**
     * Reorders the native ExportAction modal schema so exporter filter options
     * render above Filament's columnMap Fieldset. Keeps the Fieldset instance
     * built by Filament untouched.
     */
    public static function configureExportAction(ExportAction|ExportBulkAction $action): ExportAction|ExportBulkAction
    {
        if (blank($action->getExporter()::getOptionsFormComponents())) {
            return $action;
        }

        $baseSchema = (new ReflectionProperty($action, 'schema'))->getValue($action);

        return $action
            ->model(fn (ExportAction|ExportBulkAction $action): string => $action->getExporter()::getModel())
            ->schema(function (Schema $schema) use ($action, $baseSchema): array {
                $components = (array) $action->evaluate($baseSchema, ['schema' => $schema, 'action' => $action]);

                return [
                    ...array_values(array_filter($components, fn ($component): bool => ! $component instanceof Fieldset)),
                    ...array_values(array_filter($components, fn ($component): bool => $component instanceof Fieldset)),
                ];
            });
    }

    /**
     * Reads active multi/single select table filter values from the Livewire table component.
     *
     * @return array<int, int|string>
     */
    protected static function tableFilterValues(mixed $livewire, string $filterName): array
    {
        if (! method_exists($livewire, 'getTableFilterState')) {
            return [];
        }

        $state = $livewire->getTableFilterState($filterName);

        if (! is_array($state)) {
            return [];
        }

        return array_values(array_filter(Arr::wrap($state['values'] ?? $state['value'] ?? [])));
    }

    /**
     * Reads a single scalar key from an active table filter on the Livewire table component.
     */
    protected static function tableFilterScalar(mixed $livewire, string $filterName, string $key): mixed
    {
        if (! method_exists($livewire, 'getTableFilterState')) {
            return null;
        }

        $state = $livewire->getTableFilterState($filterName);

        if (! is_array($state)) {
            return null;
        }

        return filled($state[$key] ?? null) ? $state[$key] : null;
    }

    /**
     * Builds a filesystem-safe file name shaped as {report}_{segments}_{Y-m-d}.
     */
    public function getFileName(Export $export): string
    {
        return static::formatExportFileName(
            static::exportReportPrefix(),
            static::fileNameSegments($export),
        );
    }

    /**
     * Context and filter segments describing the exported records.
     *
     * @return array<int, string|null>
     */
    protected static function fileNameSegments(Export $export): array
    {
        return [];
    }

    /**
     * @param  array<int, string|null>  $segments
     */
    protected static function formatExportFileName(string $report, array $segments): string
    {
        $parts = [static::sanitizeExportFileNameSegment($report)];

        foreach ($segments as $segment) {
            if (blank($segment)) {
                continue;
            }

            $sanitized = static::sanitizeExportFileNameSegment((string) $segment);

            if (filled($sanitized)) {
                $parts[] = $sanitized;
            }
        }

        $head = rtrim(implode('_', $parts), '-_');

        return Str::limit($head, 140, '').'_'.now()->format('Y-m-d');
    }

    protected static function sanitizeExportFileNameSegment(string $segment): string
    {
        $segment = str_replace(
            ['/', '\\', ':', '*', '?', '"', '<', '>', '|', "\0", "\n", "\r", "\t"],
            ' ',
            $segment,
        );

        $segment = str_replace(' ', '-', Str::squish($segment));

        return Str::limit(ltrim($segment, '-.'), 40, '');
    }

    protected static function exportReportPrefix(): string
    {
        return (string) str(static::getModel())
            ->classBasename()
            ->beforeLast('Exporter')
            ->kebab()
            ->replace('-', ' ')
            ->plural()
            ->replace(' ', '-');
    }

    /**
     * Resolves option ids into record names, keeping the ones still reachable for the current company.
     *
     * @param  array<int, int|string>  $ids
     * @return array<int, string>
     */
    protected static function optionRecordNames(string $model, mixed $ids, string $attribute): array
    {
        if (blank($ids) || ! is_array($ids)) {
            return [];
        }

        return $model::query()
            ->whereIn('id', $ids)
            ->pluck($attribute)
            ->filter(fn ($name): bool => filled($name))
            ->values()
            ->all();
    }

    /**
     * Resolves backed enum option values into their labels.
     *
     * @param  array<int, mixed>  $values
     * @return array<int, string>
     */
    protected static function optionEnumLabels(string $enum, mixed $values): array
    {
        if (blank($values) || ! is_array($values)) {
            return [];
        }

        return array_values(array_map(
            fn (mixed $value): string => $enum::tryFrom($value instanceof BackedEnum ? $value->value : $value)?->getLabel()
                ?? (string) $value,
            $values,
        ));
    }

    /**
     * Collapses resolved names into a single segment: the name when one record is
     * selected, or "N {label}" when several are.
     *
     * @param  array<int, string>  $names
     * @return array<int, string>
     */
    protected static function nameOrCountSegments(string $label, array $names): array
    {
        $names = array_values(array_filter($names, filled(...)));

        if (count($names) === 1) {
            return [$names[0]];
        }

        if (count($names) > 1) {
            return [count($names).' '.$label];
        }

        return [];
    }

    protected static function dateSegmentValue(mixed $value): ?string
    {
        if (blank($value)) {
            return null;
        }

        if ($value instanceof DateTimeInterface) {
            return $value->format('Y-m-d');
        }

        return (string) $value;
    }
}
