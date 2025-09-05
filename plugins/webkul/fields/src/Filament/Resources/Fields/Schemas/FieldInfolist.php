<?php

namespace Webkul\Field\Filament\Resources\Fields\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class FieldInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make()
                    ->schema([
                        Section::make()
                            ->schema([
                                TextEntry::make('name')
                                    ->label(__('fields::filament/resources/field.infolist.sections.general.entries.name'))
                                    ->icon('heroicon-o-document-text')
                                    ->placeholder('-'),
                                TextEntry::make('code')
                                    ->label(__('fields::filament/resources/field.infolist.sections.general.entries.code'))
                                    ->icon('heroicon-o-identification')
                                    ->placeholder('-'),
                            ])
                            ->columns(2),

                        Section::make(__('fields::filament/resources/field.infolist.sections.options.title'))
                            ->visible(fn ($record): bool => in_array($record->type ?? '', [
                                'select',
                                'checkbox_list',
                                'radio',
                            ]))
                            ->schema([
                                RepeatableEntry::make('options')
                                    ->label(__('fields::filament/resources/field.infolist.sections.options.entries.add-option'))
                                    ->schema([
                                        TextEntry::make('name')
                                            ->label('Option')
                                            ->placeholder('-'),
                                    ])
                                    ->placeholder(__('No options defined')),
                            ]),

                        Section::make(__('fields::filament/resources/field.infolist.sections.form-settings.title'))
                            ->schema([
                                Group::make([
                                    static::getFormValidationsInfolist(),
                                    static::getFormSettingsInfolist(),
                                ]),
                            ]),

                        Section::make(__('fields::filament/resources/field.infolist.sections.table-settings.title'))
                            ->schema(static::getTableSettingsInfolist()),

                        Section::make(__('fields::filament/resources/field.infolist.sections.infolist-settings.title'))
                            ->schema(static::getInfolistSettingsInfolist()),
                    ])
                    ->columnSpan(['lg' => 2]),

                Group::make()
                    ->schema([
                        Section::make(__('fields::filament/resources/field.infolist.sections.settings.title'))
                            ->schema([
                                TextEntry::make('type')
                                    ->label(__('fields::filament/resources/field.infolist.sections.settings.entries.type'))
                                    ->icon('heroicon-o-tag')
                                    ->badge()
                                    ->color(fn (string $state): string => match ($state) {
                                        'text'          => 'primary',
                                        'textarea'      => 'info',
                                        'select'        => 'warning',
                                        'checkbox'      => 'success',
                                        'radio'         => 'secondary',
                                        'toggle'        => 'success',
                                        'checkbox_list' => 'warning',
                                        'datetime'      => 'danger',
                                        'editor'        => 'info',
                                        'markdown'      => 'info',
                                        'color'         => 'primary',
                                        default         => 'gray',
                                    })
                                    ->formatStateUsing(fn (string $state): string => __('fields::filament/resources/field.infolist.sections.settings.entries.type-options.'.str_replace('_', '-', $state))
                                    )
                                    ->placeholder('-'),

                                TextEntry::make('input_type')
                                    ->label(__('fields::filament/resources/field.infolist.sections.settings.entries.input-type'))
                                    ->visible(fn ($record): bool => $record->type === 'text')
                                    ->formatStateUsing(fn (string $state): string => __('fields::filament/resources/field.infolist.sections.settings.entries.input-type-options.'.$state)
                                    )
                                    ->placeholder('-'),

                                IconEntry::make('is_multiselect')
                                    ->label(__('fields::filament/resources/field.infolist.sections.settings.entries.is-multiselect'))
                                    ->visible(fn ($record): bool => $record->type === 'select')
                                    ->boolean()
                                    ->trueIcon('heroicon-o-check-circle')
                                    ->falseIcon('heroicon-o-x-circle')
                                    ->trueColor('success')
                                    ->falseColor('danger'),

                                TextEntry::make('sort')
                                    ->label(__('fields::filament/resources/field.infolist.sections.settings.entries.sort-order'))
                                    ->icon('heroicon-o-arrows-up-down')
                                    ->placeholder('-'),
                            ]),

                        Section::make(__('fields::filament/resources/field.infolist.sections.resource.title'))
                            ->schema([
                                TextEntry::make('customizable_type')
                                    ->label(__('fields::filament/resources/field.infolist.sections.resource.entries.resource'))
                                    ->icon('heroicon-o-cube')
                                    ->formatStateUsing(fn (string $state): string => str($state)->afterLast('\\')->toString()
                                    )
                                    ->placeholder('-'),
                            ]),
                    ])
                    ->columnSpan(['lg' => 1]),
            ])
            ->columns(3);
    }

    public static function getFormValidationsInfolist(): Fieldset
    {
        return Fieldset::make(__('fields::filament/resources/field.infolist.sections.form-settings.field-sets.validations.title'))
            ->schema([
                RepeatableEntry::make('form_settings.validations')
                    ->label(__('Validations'))
                    ->schema([
                        TextEntry::make('validation')
                            ->label(__('fields::filament/resources/field.infolist.sections.form-settings.field-sets.validations.entries.validation'))
                            ->badge()
                            ->color('primary'),
                        TextEntry::make('field')
                            ->label(__('fields::filament/resources/field.infolist.sections.form-settings.field-sets.validations.entries.field'))
                            ->visible(fn ($state): bool => isset($state['field']))
                            ->placeholder('-'),
                        TextEntry::make('value')
                            ->label(__('fields::filament/resources/field.infolist.sections.form-settings.field-sets.validations.entries.value'))
                            ->visible(fn ($state): bool => isset($state['value']))
                            ->placeholder('-'),
                    ])
                    ->columns(3)
                    ->placeholder(__('No validations configured')),
            ])
            ->columns(1);
    }

    public static function getFormSettingsInfolist(): Fieldset
    {
        return Fieldset::make(__('fields::filament/resources/field.infolist.sections.form-settings.field-sets.additional-settings.title'))
            ->schema([
                RepeatableEntry::make('form_settings.settings')
                    ->label(__('Additional Settings'))
                    ->schema([
                        TextEntry::make('setting')
                            ->label(__('fields::filament/resources/field.infolist.sections.form-settings.field-sets.additional-settings.entries.setting'))
                            ->badge()
                            ->color('info'),
                        TextEntry::make('value')
                            ->label(__('fields::filament/resources/field.infolist.sections.form-settings.field-sets.additional-settings.entries.value'))
                            ->placeholder('-'),
                    ])
                    ->columns(2)
                    ->placeholder(__('No additional settings configured')),
            ])
            ->columns(1);
    }

    public static function getTableSettingsInfolist(): array
    {
        return [
            IconEntry::make('use_in_table')
                ->label(__('fields::filament/resources/field.infolist.sections.table-settings.entries.use-in-table'))
                ->boolean()
                ->trueIcon('heroicon-o-check-circle')
                ->falseIcon('heroicon-o-x-circle')
                ->trueColor('success')
                ->falseColor('danger'),

            RepeatableEntry::make('table_settings')
                ->label(__('Table Settings'))
                ->visible(fn ($record): bool => $record->use_in_table ?? false)
                ->schema([
                    TextEntry::make('setting')
                        ->label(__('fields::filament/resources/field.infolist.sections.table-settings.entries.setting'))
                        ->badge()
                        ->color('warning'),
                    TextEntry::make('value')
                        ->label(__('fields::filament/resources/field.infolist.sections.table-settings.entries.value'))
                        ->formatStateUsing(function ($state, $record) {
                            $setting = $record['setting'] ?? '';

                            // Format alignment values
                            if (in_array($setting, ['alignment', 'verticalAlignment'])) {
                                return match ($state) {
                                    'start'   => __('fields::filament/resources/field.infolist.sections.table-settings.entries.alignment-options.start'),
                                    'left'    => __('fields::filament/resources/field.infolist.sections.table-settings.entries.alignment-options.left'),
                                    'center'  => __('fields::filament/resources/field.infolist.sections.table-settings.entries.alignment-options.center'),
                                    'end'     => __('fields::filament/resources/field.infolist.sections.table-settings.entries.alignment-options.end'),
                                    'right'   => __('fields::filament/resources/field.infolist.sections.table-settings.entries.alignment-options.right'),
                                    'justify' => __('fields::filament/resources/field.infolist.sections.table-settings.entries.alignment-options.justify'),
                                    'between' => __('fields::filament/resources/field.infolist.sections.table-settings.entries.alignment-options.between'),
                                    default   => $state,
                                };
                            }

                            // Format color values
                            if (in_array($setting, ['color', 'iconColor'])) {
                                return match ($state) {
                                    'danger'    => __('fields::filament/resources/field.infolist.sections.table-settings.entries.color-options.danger'),
                                    'info'      => __('fields::filament/resources/field.infolist.sections.table-settings.entries.color-options.info'),
                                    'primary'   => __('fields::filament/resources/field.infolist.sections.table-settings.entries.color-options.primary'),
                                    'secondary' => __('fields::filament/resources/field.infolist.sections.table-settings.entries.color-options.secondary'),
                                    'warning'   => __('fields::filament/resources/field.infolist.sections.table-settings.entries.color-options.warning'),
                                    'success'   => __('fields::filament/resources/field.infolist.sections.table-settings.entries.color-options.success'),
                                    default     => $state,
                                };
                            }

                            // Format font weight values
                            if ($setting === 'weight') {
                                return match ($state) {
                                    'Thin'       => __('fields::filament/resources/field.infolist.sections.table-settings.entries.font-weight-options.thin'),
                                    'ExtraLight' => __('fields::filament/resources/field.infolist.sections.table-settings.entries.font-weight-options.extra-light'),
                                    'Light'      => __('fields::filament/resources/field.infolist.sections.table-settings.entries.font-weight-options.light'),
                                    'Normal'     => __('fields::filament/resources/field.infolist.sections.table-settings.entries.font-weight-options.normal'),
                                    'Medium'     => __('fields::filament/resources/field.infolist.sections.table-settings.entries.font-weight-options.medium'),
                                    'SemiBold'   => __('fields::filament/resources/field.infolist.sections.table-settings.entries.font-weight-options.semi-bold'),
                                    'Bold'       => __('fields::filament/resources/field.infolist.sections.table-settings.entries.font-weight-options.bold'),
                                    'ExtraBold'  => __('fields::filament/resources/field.infolist.sections.table-settings.entries.font-weight-options.extra-bold'),
                                    'Black'      => __('fields::filament/resources/field.infolist.sections.table-settings.entries.font-weight-options.black'),
                                    default      => $state,
                                };
                            }

                            // Format icon position values
                            if ($setting === 'iconPosition') {
                                return match ($state) {
                                    'before' => __('fields::filament/resources/field.infolist.sections.table-settings.entries.icon-position-options.before'),
                                    'after'  => __('fields::filament/resources/field.infolist.sections.table-settings.entries.icon-position-options.after'),
                                    default  => $state,
                                };
                            }

                            // Format size values
                            if ($setting === 'size') {
                                return match ($state) {
                                    'ExtraSmall' => __('fields::filament/resources/field.infolist.sections.table-settings.entries.size-options.extra-small'),
                                    'Small'      => __('fields::filament/resources/field.infolist.sections.table-settings.entries.size-options.small'),
                                    'Medium'     => __('fields::filament/resources/field.infolist.sections.table-settings.entries.size-options.medium'),
                                    'Large'      => __('fields::filament/resources/field.infolist.sections.table-settings.entries.size-options.large'),
                                    default      => $state,
                                };
                            }

                            return $state;
                        })
                        ->placeholder('-'),
                ])
                ->columns(2)
                ->placeholder(__('No table settings configured')),
        ];
    }

    public static function getInfolistSettingsInfolist(): array
    {
        return [
            RepeatableEntry::make('infolist_settings')
                ->label(__('Infolist Settings'))
                ->schema([
                    TextEntry::make('setting')
                        ->label(__('fields::filament/resources/field.infolist.sections.infolist-settings.entries.setting'))
                        ->badge()
                        ->color('success'),
                    TextEntry::make('value')
                        ->label(__('fields::filament/resources/field.infolist.sections.infolist-settings.entries.value'))
                        ->formatStateUsing(function ($state, $record) {
                            $setting = $record['setting'] ?? '';

                            // Format color values
                            if (in_array($setting, ['color', 'iconColor', 'hintColor', 'trueColor', 'falseColor'])) {
                                return match ($state) {
                                    'danger'    => __('fields::filament/resources/field.infolist.sections.infolist-settings.entries.color-options.danger'),
                                    'info'      => __('fields::filament/resources/field.infolist.sections.infolist-settings.entries.color-options.info'),
                                    'primary'   => __('fields::filament/resources/field.infolist.sections.infolist-settings.entries.color-options.primary'),
                                    'secondary' => __('fields::filament/resources/field.infolist.sections.infolist-settings.entries.color-options.secondary'),
                                    'warning'   => __('fields::filament/resources/field.infolist.sections.infolist-settings.entries.color-options.warning'),
                                    'success'   => __('fields::filament/resources/field.infolist.sections.infolist-settings.entries.color-options.success'),
                                    default     => $state,
                                };
                            }

                            // Format font weight values
                            if ($setting === 'weight') {
                                return match ($state) {
                                    'Thin'       => __('fields::filament/resources/field.infolist.sections.infolist-settings.entries.font-weight-options.thin'),
                                    'ExtraLight' => __('fields::filament/resources/field.infolist.sections.infolist-settings.entries.font-weight-options.extra-light'),
                                    'Light'      => __('fields::filament/resources/field.infolist.sections.infolist-settings.entries.font-weight-options.light'),
                                    'Normal'     => __('fields::filament/resources/field.infolist.sections.infolist-settings.entries.font-weight-options.normal'),
                                    'Medium'     => __('fields::filament/resources/field.infolist.sections.infolist-settings.entries.font-weight-options.medium'),
                                    'SemiBold'   => __('fields::filament/resources/field.infolist.sections.infolist-settings.entries.font-weight-options.semi-bold'),
                                    'Bold'       => __('fields::filament/resources/field.infolist.sections.infolist-settings.entries.font-weight-options.bold'),
                                    'ExtraBold'  => __('fields::filament/resources/field.infolist.sections.infolist-settings.entries.font-weight-options.extra-bold'),
                                    'Black'      => __('fields::filament/resources/field.infolist.sections.infolist-settings.entries.font-weight-options.black'),
                                    default      => $state,
                                };
                            }

                            // Format icon position values
                            if ($setting === 'iconPosition') {
                                return match ($state) {
                                    'before' => __('fields::filament/resources/field.infolist.sections.infolist-settings.entries.icon-position-options.before'),
                                    'after'  => __('fields::filament/resources/field.infolist.sections.infolist-settings.entries.icon-position-options.after'),
                                    default  => $state,
                                };
                            }

                            // Format size values
                            if ($setting === 'size') {
                                return match ($state) {
                                    'Small'  => __('fields::filament/resources/field.infolist.sections.infolist-settings.entries.size-options.small'),
                                    'Medium' => __('fields::filament/resources/field.infolist.sections.infolist-settings.entries.size-options.medium'),
                                    'Large'  => __('fields::filament/resources/field.infolist.sections.infolist-settings.entries.size-options.large'),
                                    default  => $state,
                                };
                            }

                            return $state;
                        })
                        ->placeholder('-'),
                ])
                ->columns(2)
                ->placeholder(__('No infolist settings configured')),
        ];
    }
}
