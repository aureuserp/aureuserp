<?php

namespace Webkul\Field\Filament\Forms\Components;

use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Component;
use Illuminate\Support\Collection;
use Webkul\Field\Models\Field;

class CustomFields extends Component
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
            return $this->createField($field);
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

    protected function createField(Field $field): Component
    {
        $componentClass = match ($field->type) {
            'text'          => TextInput::class,
            'textarea'      => Textarea::class,
            'select'        => Select::class,
            'checkbox'      => Checkbox::class,
            'radio'         => Radio::class,
            'toggle'        => Toggle::class,
            'checkbox_list' => CheckboxList::class,
            'datetime'      => DateTimePicker::class,
            'editor'        => RichEditor::class,
            'markdown'      => MarkdownEditor::class,
            'color'         => ColorPicker::class,
            default         => TextInput::class,
        };

        $component = $componentClass::make($field->code)
            ->label($field->name);

        if (! empty($field->form_settings['validations'])) {
            foreach ($field->form_settings['validations'] as $validation) {
                $this->applyValidation($component, $validation);
            }
        }

        if (! empty($field->form_settings['settings'])) {
            foreach ($field->form_settings['settings'] as $setting) {
                $this->applySetting($component, $setting);
            }
        }

        if ($field->type == 'text' && $field->input_type != 'text') {
            $component->{$field->input_type}();
        }

        if (in_array($field->type, ['select', 'radio', 'checkbox_list']) && ! empty($field->options)) {
            $component->options(function () use ($field) {
                return collect($field->options)
                    ->mapWithKeys(fn ($option) => [$option => $option])
                    ->toArray();
            });

            if ($field->is_multiselect) {
                $component->multiple();
            }
        }

        if (in_array($field->type, ['select', 'datetime'])) {
            $component->native(false);
        }

        return $component;
    }

    protected function applyValidation(Component $component, array $validation): void
    {
        $rule = $validation['validation'];

        $field = $validation['field'] ?? null;

        $value = $validation['value'] ?? null;

        if (method_exists($component, $rule)) {
            if ($field) {
                $component->{$rule}($field, $value);
            } else {
                if ($value) {
                    $component->{$rule}($value);
                } else {
                    $component->{$rule}();
                }
            }
        }
    }

    protected function applySetting(Component $component, array $setting): void
    {
        $name = $setting['setting'];
        $value = $setting['value'] ?? null;

        if (method_exists($component, $name)) {
            if ($value !== null) {
                $component->{$name}($value);
            } else {
                $component->{$name}();
            }
        }
    }
}
