<?php

namespace Webkul\Field\Filament\Infolists\Components;

use Filament\Infolists\Components\ColorEntry;
use Filament\Infolists\Components\Entry;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Component;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;
use Illuminate\Support\Collection;
use Webkul\Field\Models\Field;

class CustomEntries extends Component
{
    protected array $include = [];

    protected array $exclude = [];

    protected ?string $resourceClass = null;

    final public function __construct(string $resource)
    {
        $this->resourceClass = $resource;
    }

    public static function make(string $resource): static
    {
        $static = app(static::class, ['resource' => $resource]);

        $static->configure();

        return $static;
    }

    public function include(array $fields): static
    {
        $this->include = $fields;

        return $this;
    }

    public function exclude(array $fields): static
    {
        $this->exclude = $fields;

        return $this;
    }

    protected function getResourceClass(): string
    {
        return $this->resourceClass;
    }

    public function getSchema(): array
    {
        $fields = $this->getFields();

        return $fields->map(function ($field) {
            return $this->createEntry($field);
        })->toArray();
    }

    protected function getFields(): Collection
    {
        $query = Field::query()
            ->where('customizable_type', $this->getResourceClass()::getModel());

        if (! empty($this->include)) {
            $query->whereIn('code', $this->include);
        }

        if (! empty($this->exclude)) {
            $query->whereNotIn('code', $this->exclude);
        }

        return $query->orderBy('sort')->get();
    }

    protected function createEntry(Field $field): Component
    {
        $entryClass = match ($field->type) {
            'text', 'textarea', 'select', 'radio' => TextEntry::class,
            'checkbox', 'toggle' => IconEntry::class,
            'checkbox_list' => TextEntry::class,
            'datetime'      => TextEntry::class,
            'editor', 'markdown' => TextEntry::class,
            'color' => ColorEntry::class,
            default => TextEntry::class,
        };

        $entry = $entryClass::make($field->code)
            ->label($field->name);

        if (! empty($field->infolist_settings)) {
            foreach ($field->infolist_settings as $setting) {
                $this->applySetting($entry, $setting);
            }
        }

        return $entry;
    }

    protected function applySetting(Entry $column, array $setting): void
    {
        $name = $setting['setting'];
        $value = $setting['value'] ?? null;

        if (method_exists($column, $name)) {
            if ($value !== null) {
                if ($name == 'weight') {
                    $column->{$name}(constant(FontWeight::class."::$value"));
                } elseif ($name == 'size') {
                    $column->{$name}(constant(TextSize::class."::$value"));
                } else {
                    $column->{$name}($value);
                }
            } else {
                $column->{$name}();
            }
        }
    }
}
