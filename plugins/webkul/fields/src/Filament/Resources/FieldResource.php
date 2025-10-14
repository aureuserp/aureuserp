<?php

namespace Webkul\Field\Filament\Resources;

use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Facades\Filament;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\IconPosition;
use Filament\Support\Enums\TextSize;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Schema;
use Webkul\Field\FieldsColumnManager;
use Webkul\Field\Filament\Resources\FieldResource\Pages\CreateField;
use Webkul\Field\Filament\Resources\FieldResource\Pages\EditField;
use Webkul\Field\Filament\Resources\FieldResource\Pages\ListFields;
use Webkul\Field\Models\Field;

class FieldResource extends Resource
{
    protected static ?string $model = Field::class;

    protected static ?int $navigationSort = 5;

    public static function getModelLabel(): string
    {
        return __('fields::filament/resources/field.navigation.title');
    }

    public static function getNavigationLabel(): string
    {
        return __('fields::filament/resources/field.navigation.title');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('fields::filament/resources/field.navigation.group');
    }

    public static function form(\Filament\Schemas\Schema $schema): \Filament\Schemas\Schema
    {
        return $schema
            ->components([
                Group::make()
                    ->schema([
                        Section::make()
                            ->schema([
                                TextInput::make('name')
                                    ->label(__('fields::filament/resources/field.form.sections.general.fields.name'))
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('code')
                                    ->required()
                                    ->label(__('fields::filament/resources/field.form.sections.general.fields.code'))
                                    ->maxLength(255)
                                    ->disabledOn('edit')
                                    ->helperText(__('fields::filament/resources/field.form.sections.general.fields.code-helper-text'))
                                    ->unique(ignoreRecord: true)
                                    ->notIn(function (Get $get) {
                                        if ($get('id') || ! $get('customizable_type')) {
                                            return [];
                                        }

                                        $table = app($get('customizable_type'))->getTable();

                                        return Schema::getColumnListing($table);
                                    })
                                    ->rules([
                                        'regex:/^[a-zA-Z_][a-zA-Z0-9_]*$/',
                                    ]),
                            ])
                            ->columns(2),

                        Section::make(__('fields::filament/resources/field.form.sections.options.title'))
                            ->visible(fn (Get $get): bool => in_array($get('type'), [
                                'select',
                                'checkbox_list',
                                'radio',
                            ]))
                            ->schema([
                                Repeater::make('options')
                                    ->hiddenLabel()
                                    ->simple(
                                        TextInput::make('name')
                                            ->required()
                                            ->maxLength(255),
                                    )
                                    ->addActionLabel(__('fields::filament/resources/field.form.sections.options.fields.add-option')),
                            ]),

                        Section::make(__('fields::filament/resources/field.form.sections.form-settings.title'))
                            ->schema([
                                Group::make()
                                    ->schema(static::getFormSettingsSchema())
                                    ->statePath('form_settings'),
                            ]),

                        Section::make(__('fields::filament/resources/field.form.sections.table-settings.title'))
                            ->schema(static::getTableSettingsSchema()),

                        Section::make(__('fields::filament/resources/field.form.sections.infolist-settings.title'))
                            ->schema(static::getInfolistSettingsSchema()),
                    ])
                    ->columnSpan(['lg' => 2]),

                Group::make()
                    ->schema([
                        Section::make(__('fields::filament/resources/field.form.sections.settings.title'))
                            ->schema([
                                Select::make('type')
                                    ->label(__('fields::filament/resources/field.form.sections.settings.fields.type'))
                                    ->required()
                                    ->disabledOn('edit')
                                    ->searchable()
                                    ->native(false)
                                    ->live()
                                    ->options([
                                        'text'          => __('fields::filament/resources/field.form.sections.settings.fields.type-options.text'),
                                        'textarea'      => __('fields::filament/resources/field.form.sections.settings.fields.type-options.textarea'),
                                        'select'        => __('fields::filament/resources/field.form.sections.settings.fields.type-options.select'),
                                        'checkbox'      => __('fields::filament/resources/field.form.sections.settings.fields.type-options.checkbox'),
                                        'radio'         => __('fields::filament/resources/field.form.sections.settings.fields.type-options.radio'),
                                        'toggle'        => __('fields::filament/resources/field.form.sections.settings.fields.type-options.toggle'),
                                        'checkbox_list' => __('fields::filament/resources/field.form.sections.settings.fields.type-options.checkbox-list'),
                                        'datetime'      => __('fields::filament/resources/field.form.sections.settings.fields.type-options.datetime'),
                                        'editor'        => __('fields::filament/resources/field.form.sections.settings.fields.type-options.editor'),
                                        'markdown'      => __('fields::filament/resources/field.form.sections.settings.fields.type-options.markdown'),
                                        'color'         => __('fields::filament/resources/field.form.sections.settings.fields.type-options.color'),
                                    ]),
                                Select::make('input_type')
                                    ->label(__('fields::filament/resources/field.form.sections.settings.fields.input-type'))
                                    ->required()
                                    ->disabledOn('edit')
                                    ->native(false)
                                    ->visible(fn (Get $get): bool => $get('type') == 'text')
                                    ->options([
                                        'text'     => __('fields::filament/resources/field.form.sections.settings.fields.input-type-options.text'),
                                        'email'    => __('fields::filament/resources/field.form.sections.settings.fields.input-type-options.email'),
                                        'numeric'  => __('fields::filament/resources/field.form.sections.settings.fields.input-type-options.numeric'),
                                        'integer'  => __('fields::filament/resources/field.form.sections.settings.fields.input-type-options.integer'),
                                        'password' => __('fields::filament/resources/field.form.sections.settings.fields.input-type-options.password'),
                                        'tel'      => __('fields::filament/resources/field.form.sections.settings.fields.input-type-options.tel'),
                                        'url'      => __('fields::filament/resources/field.form.sections.settings.fields.input-type-options.url'),
                                        'color'    => __('fields::filament/resources/field.form.sections.settings.fields.input-type-options.color'),
                                    ]),
                                Toggle::make('is_multiselect')
                                    ->label(__('fields::filament/resources/field.form.sections.settings.fields.is-multiselect'))
                                    ->required()
                                    ->visible(fn (Get $get): bool => $get('type') == 'select')
                                    ->live(),
                                TextInput::make('sort')
                                    ->label(__('fields::filament/resources/field.form.sections.settings.fields.sort-order'))
                                    ->required()
                                    ->integer()
                                    ->maxLength(255),
                            ]),

                        Section::make(__('fields::filament/resources/field.form.sections.resource.title'))
                            ->schema([
                                Select::make('customizable_type')
                                    ->label(__('fields::filament/resources/field.form.sections.resource.fields.resource'))
                                    ->required()
                                    ->searchable()
                                    ->native(false)
                                    ->disabledOn('edit')
                                    ->options(fn () => collect(Filament::getResources())->filter(fn ($resource) => in_array('Webkul\Field\Filament\Traits\HasCustomFields', class_uses($resource)))->mapWithKeys(fn ($resource) => [
                                        $resource::getModel() => str($resource)->afterLast('\\')->toString(),
                                    ])),
                            ]),
                    ])
                    ->columnSpan(['lg' => 1]),
            ])
            ->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')
                    ->label(__('fields::filament/resources/field.table.columns.code'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('name')
                    ->label(__('fields::filament/resources/field.table.columns.name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('type')
                    ->label(__('fields::filament/resources/field.table.columns.type'))
                    ->sortable(),
                TextColumn::make('customizable_type')
                    ->label(__('fields::filament/resources/field.table.columns.resource'))
                    ->description(fn (Field $record): string => str($record->customizable_type)->afterLast('\\')->toString().'Resource')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label(__('fields::filament/resources/field.table.columns.created-at'))
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label(__('fields::filament/resources/field.table.filters.type'))
                    ->options([
                        'text'          => __('fields::filament/resources/field.table.filters.type-options.text'),
                        'textarea'      => __('fields::filament/resources/field.table.filters.type-options.textarea'),
                        'select'        => __('fields::filament/resources/field.table.filters.type-options.select'),
                        'checkbox'      => __('fields::filament/resources/field.table.filters.type-options.checkbox'),
                        'radio'         => __('fields::filament/resources/field.table.filters.type-options.radio'),
                        'toggle'        => __('fields::filament/resources/field.table.filters.type-options.toggle'),
                        'checkbox_list' => __('fields::filament/resources/field.table.filters.type-options.checkbox-list'),
                        'datetime'      => __('fields::filament/resources/field.table.filters.type-options.datetime'),
                        'editor'        => __('fields::filament/resources/field.table.filters.type-options.editor'),
                        'markdown'      => __('fields::filament/resources/field.table.filters.type-options.markdown'),
                        'color'         => __('fields::filament/resources/field.table.filters.type-options.color'),
                    ]),
                SelectFilter::make('customizable_type')
                    ->label(__('fields::filament/resources/field.table.filters.resource'))
                    ->options(fn () => collect(Filament::getResources())->filter(fn ($resource) => in_array('Webkul\Field\Filament\Traits\HasCustomFields', class_uses($resource)))->mapWithKeys(fn ($resource) => [
                        $resource::getModel() => str($resource)->afterLast('\\')->toString(),
                    ])),
            ])
            ->recordActions([
                ActionGroup::make([
                    EditAction::make()
                        ->hidden(fn ($record) => $record->trashed()),
                    RestoreAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('fields::filament/resources/field.table.actions.restore.notification.title'))
                                ->body(__('fields::filament/resources/field.table.actions.restore.notification.body')),
                        ),
                    DeleteAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('fields::filament/resources/field.table.actions.delete.notification.title'))
                                ->body(__('fields::filament/resources/field.table.actions.delete.notification.body')),
                        ),
                    ForceDeleteAction::make()
                        ->before(function ($record) {
                            FieldsColumnManager::deleteColumn($record);
                        })
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('fields::filament/resources/field.table.actions.force-delete.notification.title'))
                                ->body(__('fields::filament/resources/field.table.actions.force-delete.notification.body')),
                        ),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    RestoreBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('fields::filament/resources/field.table.bulk-actions.restore.notification.title'))
                                ->body(__('fields::filament/resources/field.table.bulk-actions.restore.notification.body')),
                        ),
                    DeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('fields::filament/resources/field.table.bulk-actions.delete.notification.title'))
                                ->body(__('fields::filament/resources/field.table.bulk-actions.delete.notification.body')),
                        ),
                    ForceDeleteBulkAction::make()
                        ->before(function ($records) {
                            foreach ($records as $record) {
                                FieldsColumnManager::deleteColumn($record);
                            }
                        })
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('fields::filament/resources/field.table.bulk-actions.force-delete.notification.title'))
                                ->body(__('fields::filament/resources/field.table.bulk-actions.force-delete.notification.body')),
                        ),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListFields::route('/'),
            'create' => CreateField::route('/create'),
            'edit'   => EditField::route('/{record}/edit'),
        ];
    }

    public static function getFormSettingsSchema(): array
    {
        return [
            Fieldset::make(__('fields::filament/resources/field.form.sections.form-settings.field-sets.validations.title'))
                ->schema([
                    Repeater::make('validations')
                        ->hiddenLabel()
                        ->schema([
                            Select::make('validation')
                                ->label(__('fields::filament/resources/field.form.sections.form-settings.field-sets.validations.fields.validation'))
                                ->searchable()
                                ->required()
                                ->distinct()
                                ->live()
                                ->options(fn (Get $get): array => static::getTypeFormValidations($get('../../../type'))),
                            TextInput::make('field')
                                ->label(__('fields::filament/resources/field.form.sections.form-settings.field-sets.validations.fields.field'))
                                ->required()
                                ->visible(fn (Get $get): bool => in_array($get('validation'), [
                                    'prohibitedIf',
                                    'prohibitedUnless',
                                    'requiredIf',
                                    'requiredUnless',
                                ])),
                            TextInput::make('value')
                                ->label(__('fields::filament/resources/field.form.sections.form-settings.field-sets.validations.fields.value'))
                                ->required()
                                ->visible(fn (Get $get): bool => in_array($get('validation'), [
                                    'after',
                                    'afterOrEqual',
                                    'before',
                                    'beforeOrEqual',
                                    'different',
                                    'doesntEndWith',
                                    'doesntStartWith',
                                    'endsWith',
                                    'gt',
                                    'gte',
                                    'in',
                                    'lt',
                                    'lte',
                                    'maxSize',
                                    'minSize',
                                    'multipleOf',
                                    'notIn',
                                    'notRegex',
                                    'prohibitedIf',
                                    'prohibitedUnless',
                                    'prohibits',
                                    'regex',
                                    'requiredIf',
                                    'requiredUnless',
                                    'requiredWith',
                                    'requiredWithAll',
                                    'requiredWithout',
                                    'requiredWithoutAll',
                                    'rules',
                                    'same',
                                    'startsWith',
                                ])),
                        ])
                        ->addActionLabel(__('fields::filament/resources/field.form.sections.form-settings.field-sets.validations.fields.add-validation'))
                        ->columns(3)
                        ->collapsible()
                        ->itemLabel(function (array $state, Get $get): ?string {
                            $validations = static::getTypeFormValidations($get('../type'));

                            return $validations[$state['validation']] ?? null;
                        }),
                ])
                ->columns(1),

            Fieldset::make(__('fields::filament/resources/field.form.sections.form-settings.field-sets.additional-settings.title'))
                ->schema([
                    Repeater::make('settings')
                        ->hiddenLabel()
                        ->schema([
                            Select::make('setting')
                                ->label(__('fields::filament/resources/field.form.sections.form-settings.field-sets.additional-settings.fields.setting'))
                                ->required()
                                ->distinct()
                                ->searchable()
                                ->live()
                                ->options(fn (Get $get): array => static::getTypeFormSettings($get('../../../type'))),
                            TextInput::make('value')
                                ->label(__('fields::filament/resources/field.form.sections.form-settings.field-sets.additional-settings.fields.value'))
                                ->required()
                                ->maxLength(255)
                                ->visible(fn (Get $get): bool => in_array($get('setting'), [
                                    'autocapitalize',
                                    'autocomplete',
                                    'default',
                                    'disabledDates',
                                    'displayFormat',
                                    'format',
                                    'helperText',
                                    'hint',
                                    'hintIcon',
                                    'id',
                                    'loadingMessage',
                                    'locale',
                                    'mask',
                                    'noSearchResultsMessage',
                                    'offIcon',
                                    'onIcon',
                                    'placeholder',
                                    'prefix',
                                    'prefixIcon',
                                    'searchingMessage',
                                    'searchPrompt',
                                    'suffix',
                                    'suffixIcon',
                                    'timezone',
                                ])),
                            TextInput::make('value')
                                ->label(__('fields::filament/resources/field.form.sections.form-settings.field-sets.additional-settings.fields.value'))
                                ->required()
                                ->numeric()
                                ->minValue(0)
                                ->maxValue(99999999999)
                                ->visible(fn (Get $get): bool => in_array($get('setting'), [
                                    'cols',
                                    'columns',
                                    'firstDayOfWeek',
                                    'hoursStep',
                                    'maxItems',
                                    'minItems',
                                    'minutesStep',
                                    'optionsLimit',
                                    'rows',
                                    'searchDebounce',
                                    'seconds',
                                    'secondsStep',
                                    'step',
                                ])),
                            Select::make('value')
                                ->label(__('fields::filament/resources/field.form.sections.form-settings.field-sets.additional-settings.fields.color'))
                                ->required()
                                ->visible(fn (Get $get): bool => in_array($get('setting'), [
                                    'hintColor',
                                    'prefixIconColor',
                                    'suffixIconColor',
                                    'onColor',
                                    'offColor',
                                ]))
                                ->options([
                                    'danger'    => __('fields::filament/resources/field.form.sections.form-settings.field-sets.additional-settings.fields.color-options.danger'),
                                    'info'      => __('fields::filament/resources/field.form.sections.form-settings.field-sets.additional-settings.fields.color-options.info'),
                                    'primary'   => __('fields::filament/resources/field.form.sections.form-settings.field-sets.additional-settings.fields.color-options.primary'),
                                    'secondary' => __('fields::filament/resources/field.form.sections.form-settings.field-sets.additional-settings.fields.color-options.secondary'),
                                    'warning'   => __('fields::filament/resources/field.form.sections.form-settings.field-sets.additional-settings.fields.color-options.warning'),
                                    'success'   => __('fields::filament/resources/field.form.sections.form-settings.field-sets.additional-settings.fields.color-options.success'),
                                ]),
                            Select::make('value')
                                ->label(__('fields::filament/resources/field.form.sections.form-settings.field-sets.additional-settings.fields.value'))
                                ->required()
                                ->visible(fn (Get $get): bool => in_array($get('setting'), [
                                    'gridDirection',
                                ]))
                                ->options([
                                    'row'    => __('fields::filament/resources/field.form.sections.form-settings.field-sets.additional-settings.fields.grid-options.row'),
                                    'column' => __('fields::filament/resources/field.form.sections.form-settings.field-sets.additional-settings.fields.grid-options.column'),
                                ]),
                            Select::make('value')
                                ->label(__('fields::filament/resources/field.form.sections.form-settings.field-sets.additional-settings.fields.value'))
                                ->required()
                                ->visible(fn (Get $get): bool => in_array($get('setting'), [
                                    'inputMode',
                                ]))
                                ->options([
                                    'none'    => __('fields::filament/resources/field.form.sections.form-settings.field-sets.additional-settings.fields.input-modes.none'),
                                    'text'    => __('fields::filament/resources/field.form.sections.form-settings.field-sets.additional-settings.fields.input-modes.text'),
                                    'numeric' => __('fields::filament/resources/field.form.sections.form-settings.field-sets.additional-settings.fields.input-modes.numeric'),
                                    'decimal' => __('fields::filament/resources/field.form.sections.form-settings.field-sets.additional-settings.fields.input-modes.decimal'),
                                    'tel'     => __('fields::filament/resources/field.form.sections.form-settings.field-sets.additional-settings.fields.input-modes.tel'),
                                    'search'  => __('fields::filament/resources/field.form.sections.form-settings.field-sets.additional-settings.fields.input-modes.search'),
                                    'email'   => __('fields::filament/resources/field.form.sections.form-settings.field-sets.additional-settings.fields.input-modes.email'),
                                    'url'     => __('fields::filament/resources/field.form.sections.form-settings.field-sets.additional-settings.fields.input-modes.url'),
                                ]),
                        ])
                        ->addActionLabel(__('fields::filament/resources/field.form.sections.form-settings.field-sets.additional-settings.fields.add-setting'))
                        ->columns(2)
                        ->collapsible()
                        ->itemLabel(function (array $state, Get $get): ?string {
                            $settings = static::getTypeFormSettings($get('../type'));

                            return $settings[$state['setting']] ?? null;
                        }),
                ])
                ->columns(1),
        ];
    }

    public static function getTypeFormValidations(?string $type): array
    {
        if (is_null($type)) {
            return [];
        }

        $commonValidations = [
            'gt'                 => __('fields::filament/resources/field.form.sections.form-settings.validations.common.gt'),
            'gte'                => __('fields::filament/resources/field.form.sections.form-settings.validations.common.gte'),
            'lt'                 => __('fields::filament/resources/field.form.sections.form-settings.validations.common.lt'),
            'lte'                => __('fields::filament/resources/field.form.sections.form-settings.validations.common.lte'),
            'maxSize'            => __('fields::filament/resources/field.form.sections.form-settings.validations.common.max-size'),
            'minSize'            => __('fields::filament/resources/field.form.sections.form-settings.validations.common.min-size'),
            'multipleOf'         => __('fields::filament/resources/field.form.sections.form-settings.validations.common.multiple-of'),
            'nullable'           => __('fields::filament/resources/field.form.sections.form-settings.validations.common.nullable'),
            'prohibited'         => __('fields::filament/resources/field.form.sections.form-settings.validations.common.prohibited'),
            'prohibitedIf'       => __('fields::filament/resources/field.form.sections.form-settings.validations.common.prohibited-if'),
            'prohibitedUnless'   => __('fields::filament/resources/field.form.sections.form-settings.validations.common.prohibited-unless'),
            'prohibits'          => __('fields::filament/resources/field.form.sections.form-settings.validations.common.prohibits'),
            'required'           => __('fields::filament/resources/field.form.sections.form-settings.validations.common.required'),
            'requiredIf'         => __('fields::filament/resources/field.form.sections.form-settings.validations.common.required-if'),
            'requiredIfAccepted' => __('fields::filament/resources/field.form.sections.form-settings.validations.common.required-if-accepted'),
            'requiredUnless'     => __('fields::filament/resources/field.form.sections.form-settings.validations.common.required-unless'),
            'requiredWith'       => __('fields::filament/resources/field.form.sections.form-settings.validations.common.required-with'),
            'requiredWithAll'    => __('fields::filament/resources/field.form.sections.form-settings.validations.common.required-with-all'),
            'requiredWithout'    => __('fields::filament/resources/field.form.sections.form-settings.validations.common.required-without'),
            'requiredWithoutAll' => __('fields::filament/resources/field.form.sections.form-settings.validations.common.required-without-all'),
            'rules'              => __('fields::filament/resources/field.form.sections.form-settings.validations.common.rules'),
            'unique'             => __('fields::filament/resources/field.form.sections.form-settings.validations.common.unique'),
        ];

        $typeValidations = match ($type) {
            'text' => [
                'alphaDash'       => __('fields::filament/resources/field.form.sections.form-settings.validations.text.alpha-dash'),
                'alphaNum'        => __('fields::filament/resources/field.form.sections.form-settings.validations.text.alpha-num'),
                'ascii'           => __('fields::filament/resources/field.form.sections.form-settings.validations.text.ascii'),
                'doesntEndWith'   => __('fields::filament/resources/field.form.sections.form-settings.validations.text.doesnt-end-with'),
                'doesntStartWith' => __('fields::filament/resources/field.form.sections.form-settings.validations.text.doesnt-start-with'),
                'endsWith'        => __('fields::filament/resources/field.form.sections.form-settings.validations.text.ends-with'),
                'filled'          => __('fields::filament/resources/field.form.sections.form-settings.validations.text.filled'),
                'ip'              => __('fields::filament/resources/field.form.sections.form-settings.validations.text.ip'),
                'ipv4'            => __('fields::filament/resources/field.form.sections.form-settings.validations.text.ipv4'),
                'ipv6'            => __('fields::filament/resources/field.form.sections.form-settings.validations.text.ipv6'),
                'length'          => __('fields::filament/resources/field.form.sections.form-settings.validations.text.length'),
                'macAddress'      => __('fields::filament/resources/field.form.sections.form-settings.validations.text.mac-address'),
                'maxLength'       => __('fields::filament/resources/field.form.sections.form-settings.validations.text.max-length'),
                'minLength'       => __('fields::filament/resources/field.form.sections.form-settings.validations.text.min-length'),
                'regex'           => __('fields::filament/resources/field.form.sections.form-settings.validations.text.regex'),
                'startsWith'      => __('fields::filament/resources/field.form.sections.form-settings.validations.text.starts-with'),
                'ulid'            => __('fields::filament/resources/field.form.sections.form-settings.validations.text.ulid'),
                'uuid'            => __('fields::filament/resources/field.form.sections.form-settings.validations.text.uuid'),
            ],

            'textarea' => [
                'filled'    => __('fields::filament/resources/field.form.sections.form-settings.validations.textarea.filled'),
                'maxLength' => __('fields::filament/resources/field.form.sections.form-settings.validations.textarea.max-length'),
                'minLength' => __('fields::filament/resources/field.form.sections.form-settings.validations.textarea.min-length'),
            ],

            'select' => [
                'different' => __('fields::filament/resources/field.form.sections.form-settings.validations.select.different'),
                'exists'    => __('fields::filament/resources/field.form.sections.form-settings.validations.select.exists'),
                'in'        => __('fields::filament/resources/field.form.sections.form-settings.validations.select.in'),
                'notIn'     => __('fields::filament/resources/field.form.sections.form-settings.validations.select.not-in'),
                'same'      => __('fields::filament/resources/field.form.sections.form-settings.validations.select.same'),
            ],

            'radio' => [],

            'checkbox' => [
                'accepted' => __('fields::filament/resources/field.form.sections.form-settings.validations.checkbox.accepted'),
                'declined' => __('fields::filament/resources/field.form.sections.form-settings.validations.checkbox.declined'),
            ],

            'toggle' => [
                'accepted' => __('fields::filament/resources/field.form.sections.form-settings.validations.toggle.accepted'),
                'declined' => __('fields::filament/resources/field.form.sections.form-settings.validations.toggle.declined'),
            ],

            'checkbox_list' => [
                'in'       => __('fields::filament/resources/field.form.sections.form-settings.validations.checkbox-list.in'),
                'maxItems' => __('fields::filament/resources/field.form.sections.form-settings.validations.checkbox-list.max-items'),
                'minItems' => __('fields::filament/resources/field.form.sections.form-settings.validations.checkbox-list.min-items'),
            ],

            'datetime' => [
                'after'         => __('fields::filament/resources/field.form.sections.form-settings.validations.datetime.after'),
                'afterOrEqual'  => __('fields::filament/resources/field.form.sections.form-settings.validations.datetime.after-or-equal'),
                'before'        => __('fields::filament/resources/field.form.sections.form-settings.validations.datetime.before'),
                'beforeOrEqual' => __('fields::filament/resources/field.form.sections.form-settings.validations.datetime.before-or-equal'),
            ],

            'editor' => [
                'filled'    => __('fields::filament/resources/field.form.sections.form-settings.validations.editor.filled'),
                'maxLength' => __('fields::filament/resources/field.form.sections.form-settings.validations.editor.max-length'),
                'minLength' => __('fields::filament/resources/field.form.sections.form-settings.validations.editor.min-length'),
            ],

            'markdown' => [
                'filled'    => __('fields::filament/resources/field.form.sections.form-settings.validations.markdown.filled'),
                'maxLength' => __('fields::filament/resources/field.form.sections.form-settings.validations.markdown.max-length'),
                'minLength' => __('fields::filament/resources/field.form.sections.form-settings.validations.markdown.min-length'),
            ],

            'color' => [
                'hexColor' => __('fields::filament/resources/field.form.sections.form-settings.validations.color.hex-color'),
            ],

            default => [],
        };

        return array_merge($typeValidations, $commonValidations);
    }

    public static function getTypeFormSettings(?string $type): array
    {
        if (is_null($type)) {
            return [];
        }

        return match ($type) {
            'text' => [
                'autocapitalize'  => __('fields::filament/resources/field.form.sections.form-settings.settings.text.autocapitalize'),
                'autocomplete'    => __('fields::filament/resources/field.form.sections.form-settings.settings.text.autocomplete'),
                'autofocus'       => __('fields::filament/resources/field.form.sections.form-settings.settings.text.autofocus'),
                'default'         => __('fields::filament/resources/field.form.sections.form-settings.settings.text.default'),
                'disabled'        => __('fields::filament/resources/field.form.sections.form-settings.settings.text.disabled'),
                'helperText'      => __('fields::filament/resources/field.form.sections.form-settings.settings.text.helper-text'),
                'hint'            => __('fields::filament/resources/field.form.sections.form-settings.settings.text.hint'),
                'hintColor'       => __('fields::filament/resources/field.form.sections.form-settings.settings.text.hint-color'),
                'hintIcon'        => __('fields::filament/resources/field.form.sections.form-settings.settings.text.hint-icon'),
                'id'              => __('fields::filament/resources/field.form.sections.form-settings.settings.text.id'),
                'inputMode'       => __('fields::filament/resources/field.form.sections.form-settings.settings.text.input-mode'),
                'mask'            => __('fields::filament/resources/field.form.sections.form-settings.settings.text.mask'),
                'placeholder'     => __('fields::filament/resources/field.form.sections.form-settings.settings.text.placeholder'),
                'prefix'          => __('fields::filament/resources/field.form.sections.form-settings.settings.text.prefix'),
                'prefixIcon'      => __('fields::filament/resources/field.form.sections.form-settings.settings.text.prefix-icon'),
                'prefixIconColor' => __('fields::filament/resources/field.form.sections.form-settings.settings.text.prefix-icon-color'),
                'readOnly'        => __('fields::filament/resources/field.form.sections.form-settings.settings.text.read-only'),
                'step'            => __('fields::filament/resources/field.form.sections.form-settings.settings.text.step'),
                'suffix'          => __('fields::filament/resources/field.form.sections.form-settings.settings.text.suffix'),
                'suffixIcon'      => __('fields::filament/resources/field.form.sections.form-settings.settings.text.suffix-icon'),
                'suffixIconColor' => __('fields::filament/resources/field.form.sections.form-settings.settings.text.suffix-icon-color'),
            ],

            'textarea' => [
                'autofocus'   => __('fields::filament/resources/field.form.sections.form-settings.settings.textarea.autofocus'),
                'autosize'    => __('fields::filament/resources/field.form.sections.form-settings.settings.textarea.autosize'),
                'cols'        => __('fields::filament/resources/field.form.sections.form-settings.settings.textarea.cols'),
                'default'     => __('fields::filament/resources/field.form.sections.form-settings.settings.textarea.default'),
                'disabled'    => __('fields::filament/resources/field.form.sections.form-settings.settings.textarea.disabled'),
                'helperText'  => __('fields::filament/resources/field.form.sections.form-settings.settings.textarea.helper-text'),
                'hint'        => __('fields::filament/resources/field.form.sections.form-settings.settings.textarea.hint'),
                'hintColor'   => __('fields::filament/resources/field.form.sections.form-settings.settings.textarea.hint-color'),
                'hintIcon'    => __('fields::filament/resources/field.form.sections.form-settings.settings.textarea.hinticon'),
                'id'          => __('fields::filament/resources/field.form.sections.form-settings.settings.textarea.id'),
                'placeholder' => __('fields::filament/resources/field.form.sections.form-settings.settings.textarea.placeholder'),
                'readOnly'    => __('fields::filament/resources/field.form.sections.form-settings.settings.textarea.read-only'),
                'rows'        => __('fields::filament/resources/field.form.sections.form-settings.settings.textarea.rows'),
            ],

            'select' => [
                'default'                => __('fields::filament/resources/field.form.sections.form-settings.settings.select.default'),
                'disabled'               => __('fields::filament/resources/field.form.sections.form-settings.settings.select.disabled'),
                'helperText'             => __('fields::filament/resources/field.form.sections.form-settings.settings.select.helper-text'),
                'hint'                   => __('fields::filament/resources/field.form.sections.form-settings.settings.select.hint'),
                'hintColor'              => __('fields::filament/resources/field.form.sections.form-settings.settings.select.hint-color'),
                'hintIcon'               => __('fields::filament/resources/field.form.sections.form-settings.settings.select.hint-icon'),
                'id'                     => __('fields::filament/resources/field.form.sections.form-settings.settings.select.id'),
                'loadingMessage'         => __('fields::filament/resources/field.form.sections.form-settings.settings.select.loading-message'),
                'noSearchResultsMessage' => __('fields::filament/resources/field.form.sections.form-settings.settings.select.no-search-results-message'),
                'optionsLimit'           => __('fields::filament/resources/field.form.sections.form-settings.settings.select.options-limit'),
                'preload'                => __('fields::filament/resources/field.form.sections.form-settings.settings.select.preload'),
                'searchable'             => __('fields::filament/resources/field.form.sections.form-settings.settings.select.searchable'),
                'searchDebounce'         => __('fields::filament/resources/field.form.sections.form-settings.settings.select.search-debounce'),
                'searchingMessage'       => __('fields::filament/resources/field.form.sections.form-settings.settings.select.searching-message'),
                'searchPrompt'           => __('fields::filament/resources/field.form.sections.form-settings.settings.select.search-prompt'),
            ],

            'radio' => [
                'default'    => __('fields::filament/resources/field.form.sections.form-settings.settings.radio.default'),
                'disabled'   => __('fields::filament/resources/field.form.sections.form-settings.settings.radio.disabled'),
                'helperText' => __('fields::filament/resources/field.form.sections.form-settings.settings.radio.helper-text'),
                'hint'       => __('fields::filament/resources/field.form.sections.form-settings.settings.radio.hint'),
                'hintColor'  => __('fields::filament/resources/field.form.sections.form-settings.settings.radio.hint-color'),
                'hintIcon'   => __('fields::filament/resources/field.form.sections.form-settings.settings.radio.hint-icon'),
                'id'         => __('fields::filament/resources/field.form.sections.form-settings.settings.radio.id'),
            ],

            'checkbox' => [
                'default'    => __('fields::filament/resources/field.form.sections.form-settings.settings.checkbox.default'),
                'disabled'   => __('fields::filament/resources/field.form.sections.form-settings.settings.checkbox.disabled'),
                'helperText' => __('fields::filament/resources/field.form.sections.form-settings.settings.checkbox.helper-text'),
                'hint'       => __('fields::filament/resources/field.form.sections.form-settings.settings.checkbox.hint'),
                'hintColor'  => __('fields::filament/resources/field.form.sections.form-settings.settings.checkbox.hint-color'),
                'hintIcon'   => __('fields::filament/resources/field.form.sections.form-settings.settings.checkbox.hint-icon'),
                'id'         => __('fields::filament/resources/field.form.sections.form-settings.settings.checkbox.id'),
                'inline'     => __('fields::filament/resources/field.form.sections.form-settings.settings.checkbox.inline'),
            ],

            'toggle' => [
                'default'    => __('fields::filament/resources/field.form.sections.form-settings.settings.toggle.default'),
                'disabled'   => __('fields::filament/resources/field.form.sections.form-settings.settings.toggle.disabled'),
                'helperText' => __('fields::filament/resources/field.form.sections.form-settings.settings.toggle.helper-text'),
                'hint'       => __('fields::filament/resources/field.form.sections.form-settings.settings.toggle.hint'),
                'hintColor'  => __('fields::filament/resources/field.form.sections.form-settings.settings.toggle.hint-color'),
                'hintIcon'   => __('fields::filament/resources/field.form.sections.form-settings.settings.toggle.hint-icon'),
                'id'         => __('fields::filament/resources/field.form.sections.form-settings.settings.toggle.id'),
                'offColor'   => __('fields::filament/resources/field.form.sections.form-settings.settings.toggle.off-color'),
                'offIcon'    => __('fields::filament/resources/field.form.sections.form-settings.settings.toggle.off-icon'),
                'onColor'    => __('fields::filament/resources/field.form.sections.form-settings.settings.toggle.on-color'),
                'onIcon'     => __('fields::filament/resources/field.form.sections.form-settings.settings.toggle.on-icon'),
            ],

            'checkbox_list' => [
                'bulkToggleable'         => __('fields::filament/resources/field.form.sections.form-settings.settings.checkbox-list.bulk-toggleable'),
                'columns'                => __('fields::filament/resources/field.form.sections.form-settings.settings.checkbox-list.columns'),
                'default'                => __('fields::filament/resources/field.form.sections.form-settings.settings.checkbox-list.default'),
                'disabled'               => __('fields::filament/resources/field.form.sections.form-settings.settings.checkbox-list.disabled'),
                'gridDirection'          => __('fields::filament/resources/field.form.sections.form-settings.settings.checkbox-list.grid-direction'),
                'helperText'             => __('fields::filament/resources/field.form.sections.form-settings.settings.checkbox-list.helper-text'),
                'hint'                   => __('fields::filament/resources/field.form.sections.form-settings.settings.checkbox-list.hint'),
                'hintColor'              => __('fields::filament/resources/field.form.sections.form-settings.settings.checkbox-list.hint-color'),
                'hintIcon'               => __('fields::filament/resources/field.form.sections.form-settings.settings.checkbox-list.hint-icon'),
                'id'                     => __('fields::filament/resources/field.form.sections.form-settings.settings.checkbox-list.id'),
                'maxItems'               => __('fields::filament/resources/field.form.sections.form-settings.settings.checkbox-list.max-items'),
                'minItems'               => __('fields::filament/resources/field.form.sections.form-settings.settings.checkbox-list.min-items'),
                'noSearchResultsMessage' => __('fields::filament/resources/field.form.sections.form-settings.settings.checkbox-list.no-search-results-message'),
                'searchable'             => __('fields::filament/resources/field.form.sections.form-settings.settings.checkbox-list.searchable'),
            ],

            'datetime' => [
                'closeOnDateSelection'   => __('fields::filament/resources/field.form.sections.form-settings.settings.datetime.close-on-date-selection'),
                'default'                => __('fields::filament/resources/field.form.sections.form-settings.settings.datetime.default'),
                'disabled'               => __('fields::filament/resources/field.form.sections.form-settings.settings.datetime.disabled'),
                'disabledDates'          => __('fields::filament/resources/field.form.sections.form-settings.settings.datetime.disabled-dates'),
                'displayFormat'          => __('fields::filament/resources/field.form.sections.form-settings.settings.datetime.display-format'),
                'firstDayOfWeek'         => __('fields::filament/resources/field.form.sections.form-settings.settings.datetime.first-day-of-week'),
                'format'                 => __('fields::filament/resources/field.form.sections.form-settings.settings.datetime.format'),
                'helperText'             => __('fields::filament/resources/field.form.sections.form-settings.settings.datetime.helper-text'),
                'hint'                   => __('fields::filament/resources/field.form.sections.form-settings.settings.datetime.hint'),
                'hintColor'              => __('fields::filament/resources/field.form.sections.form-settings.settings.datetime.hint-color'),
                'hintIcon'               => __('fields::filament/resources/field.form.sections.form-settings.settings.datetime.hint-icon'),
                'hoursStep'              => __('fields::filament/resources/field.form.sections.form-settings.settings.datetime.hours-step'),
                'id'                     => __('fields::filament/resources/field.form.sections.form-settings.settings.datetime.id'),
                'locale'                 => __('fields::filament/resources/field.form.sections.form-settings.settings.datetime.locale'),
                'minutesStep'            => __('fields::filament/resources/field.form.sections.form-settings.settings.datetime.minutes-step'),
                'seconds'                => __('fields::filament/resources/field.form.sections.form-settings.settings.datetime.seconds'),
                'secondsStep'            => __('fields::filament/resources/field.form.sections.form-settings.settings.datetime.seconds-step'),
                'timezone'               => __('fields::filament/resources/field.form.sections.form-settings.settings.datetime.timezone'),
                'weekStartsOnMonday'     => __('fields::filament/resources/field.form.sections.form-settings.settings.datetime.week-starts-on-monday'),
                'weekStartsOnSunday'     => __('fields::filament/resources/field.form.sections.form-settings.settings.datetime.week-starts-on-sunday'),
            ],

            'editor' => [
                'default'     => __('fields::filament/resources/field.form.sections.form-settings.settings.editor.default'),
                'disabled'    => __('fields::filament/resources/field.form.sections.form-settings.settings.editor.disabled'),
                'helperText'  => __('fields::filament/resources/field.form.sections.form-settings.settings.editor.helper-text'),
                'hint'        => __('fields::filament/resources/field.form.sections.form-settings.settings.editor.hint'),
                'hintColor'   => __('fields::filament/resources/field.form.sections.form-settings.settings.editor.hint-color'),
                'hintIcon'    => __('fields::filament/resources/field.form.sections.form-settings.settings.editor.hint-icon'),
                'id'          => __('fields::filament/resources/field.form.sections.form-settings.settings.editor.id'),
                'placeholder' => __('fields::filament/resources/field.form.sections.form-settings.settings.editor.placeholder'),
                'readOnly'    => __('fields::filament/resources/field.form.sections.form-settings.settings.editor.read-only'),
            ],

            'markdown' => [
                'default'     => __('fields::filament/resources/field.form.sections.form-settings.settings.markdown.default'),
                'disabled'    => __('fields::filament/resources/field.form.sections.form-settings.settings.markdown.disabled'),
                'helperText'  => __('fields::filament/resources/field.form.sections.form-settings.settings.markdown.helper-text'),
                'hint'        => __('fields::filament/resources/field.form.sections.form-settings.settings.markdown.hint'),
                'hintColor'   => __('fields::filament/resources/field.form.sections.form-settings.settings.markdown.hint-color'),
                'hintIcon'    => __('fields::filament/resources/field.form.sections.form-settings.settings.markdown.hint-icon'),
                'id'          => __('fields::filament/resources/field.form.sections.form-settings.settings.markdown.id'),
                'placeholder' => __('fields::filament/resources/field.form.sections.form-settings.settings.markdown.placeholder'),
                'readOnly'    => __('fields::filament/resources/field.form.sections.form-settings.settings.markdown.read-only'),
            ],

            'color' => [
                'default'    => __('fields::filament/resources/field.form.sections.form-settings.settings.color.default'),
                'disabled'   => __('fields::filament/resources/field.form.sections.form-settings.settings.color.disabled'),
                'helperText' => __('fields::filament/resources/field.form.sections.form-settings.settings.color.helper-text'),
                'hint'       => __('fields::filament/resources/field.form.sections.form-settings.settings.color.hint'),
                'hintColor'  => __('fields::filament/resources/field.form.sections.form-settings.settings.color.hint-color'),
                'hintIcon'   => __('fields::filament/resources/field.form.sections.form-settings.settings.color.hint-icon'),
                'hsl'        => __('fields::filament/resources/field.form.sections.form-settings.settings.color.hsl'),
                'id'         => __('fields::filament/resources/field.form.sections.form-settings.settings.color.id'),
                'rgb'        => __('fields::filament/resources/field.form.sections.form-settings.settings.color.rgb'),
                'rgba'       => __('fields::filament/resources/field.form.sections.form-settings.settings.color.rgba'),
            ],

            'file' => [
                'acceptedFileTypes'                => __('fields::filament/resources/field.form.sections.form-settings.settings.file.accepted-file-types'),
                'appendFiles'                      => __('fields::filament/resources/field.form.sections.form-settings.settings.file.append-files'),
                'deletable'                        => __('fields::filament/resources/field.form.sections.form-settings.settings.file.deletable'),
                'directory'                        => __('fields::filament/resources/field.form.sections.form-settings.settings.file.directory'),
                'downloadable'                     => __('fields::filament/resources/field.form.sections.form-settings.settings.file.downloadable'),
                'fetchFileInformation'             => __('fields::filament/resources/field.form.sections.form-settings.settings.file.fetch-file-information'),
                'fileAttachmentsDirectory'         => __('fields::filament/resources/field.form.sections.form-settings.settings.file.file-attachment-directory'),
                'fileAttachmentsVisibility'        => __('fields::filament/resources/field.form.sections.form-settings.settings.file.file-attachments-visibility'),
                'image'                            => __('fields::filament/resources/field.form.sections.form-settings.settings.file.image'),
                'imageCropAspectRatio'             => __('fields::filament/resources/field.form.sections.form-settings.settings.file.image-crop-aspect-ratio'),
                'imageEditor'                      => __('fields::filament/resources/field.form.sections.form-settings.settings.file.image-editor'),
                'imageEditorAspectRatios'          => __('fields::filament/resources/field.form.sections.form-settings.settings.file.image-editor-aspect-ratios'),
                'imageEditorEmptyFillColor'        => __('fields::filament/resources/field.form.sections.form-settings.settings.file.image-editor-empty-fill-color'),
                'imageEditorMode'                  => __('fields::filament/resources/field.form.sections.form-settings.settings.file.image-editor-mode'),
                'imagePreviewHeight'               => __('fields::filament/resources/field.form.sections.form-settings.settings.file.image-preview-height'),
                'imageResizeMode'                  => __('fields::filament/resources/field.form.sections.form-settings.settings.file.image-resize-mode'),
                'imageResizeTargetHeight'          => __('fields::filament/resources/field.form.sections.form-settings.settings.file.image-resize-target-height'),
                'imageResizeTargetWidth'           => __('fields::filament/resources/field.form.sections.form-settings.settings.file.image-resize-target-width'),
                'loadingIndicatorPosition'         => __('fields::filament/resources/field.form.sections.form-settings.settings.file.loading-indicator-position'),
                'moveFiles'                        => __('fields::filament/resources/field.form.sections.form-settings.settings.file.move-files'),
                'openable'                         => __('fields::filament/resources/field.form.sections.form-settings.settings.file.openable'),
                'orientImagesFromExif'             => __('fields::filament/resources/field.form.sections.form-settings.settings.file.orient-images-from-exif'),
                'panelAspectRatio'                 => __('fields::filament/resources/field.form.sections.form-settings.settings.file.panel-aspect-ratio'),
                'panelLayout'                      => __('fields::filament/resources/field.form.sections.form-settings.settings.file.panel-layout'),
                'previewable'                      => __('fields::filament/resources/field.form.sections.form-settings.settings.file.previewable'),
                'removeUploadedFileButtonPosition' => __('fields::filament/resources/field.form.sections.form-settings.settings.file.remove-uploaded-file-button-position'),
                'reorderable'                      => __('fields::filament/resources/field.form.sections.form-settings.settings.file.reorderable'),
                'storeFiles'                       => __('fields::filament/resources/field.form.sections.form-settings.settings.file.store-files'),
                'uploadButtonPosition'             => __('fields::filament/resources/field.form.sections.form-settings.settings.file.upload-button-position'),
                'uploadingMessage'                 => __('fields::filament/resources/field.form.sections.form-settings.settings.file.uploading-message'),
                'uploadProgressIndicatorPosition'  => __('fields::filament/resources/field.form.sections.form-settings.settings.file.upload-progress-indicator-position'),
                'visibility'                       => __('fields::filament/resources/field.form.sections.form-settings.settings.file.visibility'),
            ],
        };
    }

    public static function getTableSettingsSchema(): array
    {
        return [
            Toggle::make('use_in_table')
                ->label(__('fields::filament/resources/field.form.sections.table-settings.fields.use-in-table'))
                ->required()
                ->live(),
            Repeater::make('table_settings')
                ->hiddenLabel()
                ->visible(fn (Get $get): bool => $get('use_in_table'))
                ->schema([
                    Select::make('setting')
                        ->label(__('fields::filament/resources/field.form.sections.table-settings.fields.setting'))
                        ->searchable()
                        ->required()
                        ->distinct()
                        ->live()
                        ->options(fn (Get $get): array => static::getTypeTableSettings($get('../../type'))),
                    TextInput::make('value')
                        ->label(__('fields::filament/resources/field.form.sections.table-settings.fields.value'))
                        ->required()
                        ->maxLength(255)
                        ->visible(fn (Get $get): bool => in_array($get('setting'), [
                            'copyMessage',
                            'dateTimeTooltip',
                            'default',
                            'icon',
                            'label',
                            'money',
                            'placeholder',
                            'prefix',
                            'suffix',
                            'tooltip',
                            'width',
                        ])),

                    Select::make('value')
                        ->label(__('fields::filament/resources/field.form.sections.table-settings.fields.color'))
                        ->required()
                        ->visible(fn (Get $get): bool => in_array($get('setting'), [
                            'color',
                            'iconColor',
                        ]))
                        ->options([
                            'danger'    => __('fields::filament/resources/field.form.sections.table-settings.fields.color-options.danger'),
                            'info'      => __('fields::filament/resources/field.form.sections.table-settings.fields.color-options.info'),
                            'primary'   => __('fields::filament/resources/field.form.sections.table-settings.fields.color-options.primary'),
                            'secondary' => __('fields::filament/resources/field.form.sections.table-settings.fields.color-options.secondary'),
                            'warning'   => __('fields::filament/resources/field.form.sections.table-settings.fields.color-options.warning'),
                            'success'   => __('fields::filament/resources/field.form.sections.table-settings.fields.color-options.success'),
                        ]),

                    Select::make('value')
                        ->label(__('fields::filament/resources/field.form.sections.table-settings.fields.alignment'))
                        ->required()
                        ->visible(fn (Get $get): bool => in_array($get('setting'), [
                            'alignment',
                            'verticalAlignment',
                        ]))
                        ->options([
                            Alignment::Start->value   => __('fields::filament/resources/field.form.sections.table-settings.fields.alignment-options.start'),
                            Alignment::Left->value    => __('fields::filament/resources/field.form.sections.table-settings.fields.alignment-options.left'),
                            Alignment::Center->value  => __('fields::filament/resources/field.form.sections.table-settings.fields.alignment-options.center'),
                            Alignment::End->value     => __('fields::filament/resources/field.form.sections.table-settings.fields.alignment-options.end'),
                            Alignment::Right->value   => __('fields::filament/resources/field.form.sections.table-settings.fields.alignment-options.right'),
                            Alignment::Justify->value => __('fields::filament/resources/field.form.sections.table-settings.fields.alignment-options.justify'),
                            Alignment::Between->value => __('fields::filament/resources/field.form.sections.table-settings.fields.alignment-options.between'),
                        ]),

                    Select::make('value')
                        ->label(__('fields::filament/resources/field.form.sections.table-settings.fields.font-weight'))
                        ->required()
                        ->visible(fn (Get $get): bool => in_array($get('setting'), [
                            'weight',
                        ]))
                        ->options([
                            FontWeight::Thin->name       => __('fields::filament/resources/field.form.sections.table-settings.fields.font-weight-options.thin'),
                            FontWeight::ExtraLight->name => __('fields::filament/resources/field.form.sections.table-settings.fields.font-weight-options.extra-light'),
                            FontWeight::Light->name      => __('fields::filament/resources/field.form.sections.table-settings.fields.font-weight-options.light'),
                            FontWeight::Normal->name     => __('fields::filament/resources/field.form.sections.table-settings.fields.font-weight-options.normal'),
                            FontWeight::Medium->name     => __('fields::filament/resources/field.form.sections.table-settings.fields.font-weight-options.medium'),
                            FontWeight::SemiBold->name   => __('fields::filament/resources/field.form.sections.table-settings.fields.font-weight-options.semi-bold'),
                            FontWeight::Bold->name       => __('fields::filament/resources/field.form.sections.table-settings.fields.font-weight-options.bold'),
                            FontWeight::ExtraBold->name  => __('fields::filament/resources/field.form.sections.table-settings.fields.font-weight-options.extra-bold'),
                            FontWeight::Black->name      => __('fields::filament/resources/field.form.sections.table-settings.fields.font-weight-options.black'),
                        ]),

                    Select::make('value')
                        ->label(__('fields::filament/resources/field.form.sections.table-settings.fields.icon-position'))
                        ->required()
                        ->visible(fn (Get $get): bool => in_array($get('setting'), [
                            'iconPosition',
                        ]))
                        ->options([
                            IconPosition::Before->value => __('fields::filament/resources/field.form.sections.table-settings.fields.icon-position-options.before'),
                            IconPosition::After->value  => __('fields::filament/resources/field.form.sections.table-settings.fields.icon-position-options.after'),
                        ]),

                    Select::make('value')
                        ->label(__('fields::filament/resources/field.form.sections.table-settings.fields.size'))
                        ->required()
                        ->visible(fn (Get $get): bool => in_array($get('setting'), [
                            'size',
                        ]))
                        ->options([
                            TextSize::ExtraSmall->name  => __('fields::filament/resources/field.form.sections.table-settings.fields.size-options.extra-small'),
                            TextSize::Small->name       => __('fields::filament/resources/field.form.sections.table-settings.fields.size-options.small'),
                            TextSize::Medium->name      => __('fields::filament/resources/field.form.sections.table-settings.fields.size-options.medium'),
                            TextSize::Large->name       => __('fields::filament/resources/field.form.sections.table-settings.fields.size-options.large'),
                        ]),

                    TextInput::make('value')
                        ->label(__('fields::filament/resources/field.form.sections.table-settings.fields.value'))
                        ->required()
                        ->numeric()
                        ->minValue(0)
                        ->maxValue(99999999999)
                        ->visible(fn (Get $get): bool => in_array($get('setting'), [
                            'limit',
                            'words',
                            'lineClamp',
                            'copyMessageDuration',
                        ])),
                ])
                ->addActionLabel(__('fields::filament/resources/field.form.sections.table-settings.fields.add-setting'))
                ->columns(2)
                ->collapsible()
                ->itemLabel(function (array $state, Get $get): ?string {
                    $settings = static::getTypeTableSettings($get('type'));

                    return $settings[$state['setting']] ?? null;
                }),
        ];
    }

    public static function getTypeTableSettings(?string $type): array
    {
        if (is_null($type)) {
            return [];
        }

        $commonSettings = [
            'alignEnd'             => __('fields::filament/resources/field.form.sections.table-settings.settings.common.align-end'),
            'alignment'            => __('fields::filament/resources/field.form.sections.table-settings.settings.common.alignment'),
            'alignStart'           => __('fields::filament/resources/field.form.sections.table-settings.settings.common.align-start'),
            'badge'                => __('fields::filament/resources/field.form.sections.table-settings.settings.common.badge'),
            'boolean'              => __('fields::filament/resources/field.form.sections.table-settings.settings.common.boolean'),
            'color'                => __('fields::filament/resources/field.form.sections.table-settings.settings.common.color'),
            'copyable'             => __('fields::filament/resources/field.form.sections.table-settings.settings.common.copyable'),
            'copyMessage'          => __('fields::filament/resources/field.form.sections.table-settings.settings.common.copy-message'),
            'copyMessageDuration'  => __('fields::filament/resources/field.form.sections.table-settings.settings.common.copy-message-duration'),
            'default'              => __('fields::filament/resources/field.form.sections.table-settings.settings.common.default'),
            'filterable'           => __('fields::filament/resources/field.form.sections.table-settings.settings.common.filterable'),
            'groupable'            => __('fields::filament/resources/field.form.sections.table-settings.settings.common.groupable'),
            'grow'                 => __('fields::filament/resources/field.form.sections.table-settings.settings.common.grow'),
            'icon'                 => __('fields::filament/resources/field.form.sections.table-settings.settings.common.icon'),
            'iconColor'            => __('fields::filament/resources/field.form.sections.table-settings.settings.common.icon-color'),
            'iconPosition'         => __('fields::filament/resources/field.form.sections.table-settings.settings.common.icon-position'),
            'label'                => __('fields::filament/resources/field.form.sections.table-settings.settings.common.label'),
            'limit'                => __('fields::filament/resources/field.form.sections.table-settings.settings.common.limit'),
            'lineClamp'            => __('fields::filament/resources/field.form.sections.table-settings.settings.common.line-clamp'),
            'money'                => __('fields::filament/resources/field.form.sections.table-settings.settings.common.money'),
            'placeholder'          => __('fields::filament/resources/field.form.sections.table-settings.settings.common.placeholder'),
            'prefix'               => __('fields::filament/resources/field.form.sections.table-settings.settings.common.prefix'),
            'searchable'           => __('fields::filament/resources/field.form.sections.table-settings.settings.common.searchable'),
            'size'                 => __('fields::filament/resources/field.form.sections.table-settings.settings.common.size'),
            'sortable'             => __('fields::filament/resources/field.form.sections.table-settings.settings.common.sortable'),
            'suffix'               => __('fields::filament/resources/field.form.sections.table-settings.settings.common.suffix'),
            'toggleable'           => __('fields::filament/resources/field.form.sections.table-settings.settings.common.toggleable'),
            'tooltip'              => __('fields::filament/resources/field.form.sections.table-settings.settings.common.tooltip'),
            'verticalAlignment'    => __('fields::filament/resources/field.form.sections.table-settings.settings.common.vertical-alignment'),
            'verticallyAlignStart' => __('fields::filament/resources/field.form.sections.table-settings.settings.common.vertically-align-start'),
            'weight'               => __('fields::filament/resources/field.form.sections.table-settings.settings.common.weight'),
            'width'                => __('fields::filament/resources/field.form.sections.table-settings.settings.common.width'),
            'words'                => __('fields::filament/resources/field.form.sections.table-settings.settings.common.words'),
            'wrapHeader'           => __('fields::filament/resources/field.form.sections.table-settings.settings.common.wrap-header'),
        ];

        $typeSettings = match ($type) {
            'datetime' => [
                'date'            => __('fields::filament/resources/field.form.sections.table-settings.settings.datetime.date'),
                'dateTime'        => __('fields::filament/resources/field.form.sections.table-settings.settings.datetime.date-time'),
                'dateTimeTooltip' => __('fields::filament/resources/field.form.sections.table-settings.settings.datetime.date-time-tooltip'),
                'since'           => __('fields::filament/resources/field.form.sections.table-settings.settings.datetime.since'),
            ],

            default => [],
        };

        return array_merge($typeSettings, $commonSettings);
    }

    public static function getInfolistSettingsSchema(): array
    {
        return [
            Repeater::make('infolist_settings')
                ->hiddenLabel()
                ->schema([
                    Select::make('setting')
                        ->label(__('fields::filament/resources/field.form.sections.infolist-settings.fields.setting'))
                        ->searchable()
                        ->required()
                        ->distinct()
                        ->live()
                        ->options(fn (Get $get): array => static::getTypeInfolistSettings($get('../../type'))),
                    TextInput::make('value')
                        ->label(__('fields::filament/resources/field.form.sections.infolist-settings.fields.value'))
                        ->required()
                        ->maxLength(255)
                        ->visible(fn (Get $get): bool => in_array($get('setting'), [
                            'copyMessage',
                            'dateTimeTooltip',
                            'default',
                            'icon',
                            'label',
                            'money',
                            'placeholder',
                            'tooltip',
                            'helperText',
                            'hint',
                            'hintIcon',
                            'separator',
                            'trueIcon',
                            'falseIcon',
                        ])),

                    Select::make('value')
                        ->label(__('fields::filament/resources/field.form.sections.infolist-settings.fields.color'))
                        ->required()
                        ->visible(fn (Get $get): bool => in_array($get('setting'), [
                            'color',
                            'iconColor',
                            'hintColor',
                            'trueColor',
                            'falseColor',
                        ]))
                        ->options([
                            'danger'    => __('fields::filament/resources/field.form.sections.infolist-settings.fields.color-options.danger'),
                            'info'      => __('fields::filament/resources/field.form.sections.infolist-settings.fields.color-options.info'),
                            'primary'   => __('fields::filament/resources/field.form.sections.infolist-settings.fields.color-options.primary'),
                            'secondary' => __('fields::filament/resources/field.form.sections.infolist-settings.fields.color-options.secondary'),
                            'warning'   => __('fields::filament/resources/field.form.sections.infolist-settings.fields.color-options.warning'),
                            'success'   => __('fields::filament/resources/field.form.sections.infolist-settings.fields.color-options.success'),
                        ]),

                    Select::make('value')
                        ->label(__('fields::filament/resources/field.form.sections.infolist-settings.fields.font-weight'))
                        ->required()
                        ->visible(fn (Get $get): bool => in_array($get('setting'), [
                            'weight',
                        ]))
                        ->options([
                            FontWeight::Thin->name       => __('fields::filament/resources/field.form.sections.infolist-settings.fields.font-weight-options.thin'),
                            FontWeight::ExtraLight->name => __('fields::filament/resources/field.form.sections.infolist-settings.fields.font-weight-options.extra-light'),
                            FontWeight::Light->name      => __('fields::filament/resources/field.form.sections.infolist-settings.fields.font-weight-options.light'),
                            FontWeight::Normal->name     => __('fields::filament/resources/field.form.sections.infolist-settings.fields.font-weight-options.normal'),
                            FontWeight::Medium->name     => __('fields::filament/resources/field.form.sections.infolist-settings.fields.font-weight-options.medium'),
                            FontWeight::SemiBold->name   => __('fields::filament/resources/field.form.sections.infolist-settings.fields.font-weight-options.semi-bold'),
                            FontWeight::Bold->name       => __('fields::filament/resources/field.form.sections.infolist-settings.fields.font-weight-options.bold'),
                            FontWeight::ExtraBold->name  => __('fields::filament/resources/field.form.sections.infolist-settings.fields.font-weight-options.extra-bold'),
                            FontWeight::Black->name      => __('fields::filament/resources/field.form.sections.infolist-settings.fields.font-weight-options.black'),
                        ]),

                    Select::make('value')
                        ->label(__('fields::filament/resources/field.form.sections.infolist-settings.fields.icon-position'))
                        ->required()
                        ->visible(fn (Get $get): bool => in_array($get('setting'), [
                            'iconPosition',
                        ]))
                        ->options([
                            IconPosition::Before->value => __('fields::filament/resources/field.form.sections.infolist-settings.fields.icon-position-options.before'),
                            IconPosition::After->value  => __('fields::filament/resources/field.form.sections.infolist-settings.fields.icon-position-options.after'),
                        ]),

                    Select::make('value')
                        ->label(__('fields::filament/resources/field.form.sections.infolist-settings.fields.size'))
                        ->required()
                        ->visible(fn (Get $get): bool => in_array($get('setting'), [
                            'size',
                        ]))
                        ->options([
                            TextSize::Small->name  => __('fields::filament/resources/field.form.sections.infolist-settings.fields.size-options.small'),
                            TextSize::Medium->name => __('fields::filament/resources/field.form.sections.infolist-settings.fields.size-options.medium'),
                            TextSize::Large->name  => __('fields::filament/resources/field.form.sections.infolist-settings.fields.size-options.large'),
                        ]),

                    TextInput::make('value')
                        ->label(__('fields::filament/resources/field.form.sections.infolist-settings.fields.value'))
                        ->required()
                        ->numeric()
                        ->minValue(0)
                        ->maxValue(99999999999)
                        ->visible(fn (Get $get): bool => in_array($get('setting'), [
                            'limit',
                            'words',
                            'lineClamp',
                            'copyMessageDuration',
                            'columnSpan',
                            'limitList',
                        ])),
                ])
                ->addActionLabel(__('fields::filament/resources/field.form.sections.infolist-settings.fields.add-setting'))
                ->columns(2)
                ->collapsible()
                ->itemLabel(function (array $state, Get $get): ?string {
                    $settings = static::getTypeInfolistSettings($get('type'));

                    return $settings[$state['setting']] ?? null;
                }),
        ];
    }

    public static function getTypeInfolistSettings(?string $type): array
    {
        if (is_null($type)) {
            return [];
        }

        $commonSettings = [
            'badge'               => __('fields::filament/resources/field.form.sections.infolist-settings.settings.common.badge'),
            'color'               => __('fields::filament/resources/field.form.sections.infolist-settings.settings.common.color'),
            'copyable'            => __('fields::filament/resources/field.form.sections.infolist-settings.settings.common.copyable'),
            'copyMessage'         => __('fields::filament/resources/field.form.sections.infolist-settings.settings.common.copy-message'),
            'copyMessageDuration' => __('fields::filament/resources/field.form.sections.infolist-settings.settings.common.copy-message-duration'),
            'default'             => __('fields::filament/resources/field.form.sections.infolist-settings.settings.common.default'),
            'icon'                => __('fields::filament/resources/field.form.sections.infolist-settings.settings.common.icon'),
            'iconColor'           => __('fields::filament/resources/field.form.sections.infolist-settings.settings.common.icon-color'),
            'iconPosition'        => __('fields::filament/resources/field.form.sections.infolist-settings.settings.common.icon-position'),
            'label'               => __('fields::filament/resources/field.form.sections.infolist-settings.settings.common.label'),
            'limit'               => __('fields::filament/resources/field.form.sections.infolist-settings.settings.common.limit'),
            'lineClamp'           => __('fields::filament/resources/field.form.sections.infolist-settings.settings.common.line-clamp'),
            'money'               => __('fields::filament/resources/field.form.sections.infolist-settings.settings.common.money'),
            'placeholder'         => __('fields::filament/resources/field.form.sections.infolist-settings.settings.common.placeholder'),
            'size'                => __('fields::filament/resources/field.form.sections.infolist-settings.settings.common.size'),
            'tooltip'             => __('fields::filament/resources/field.form.sections.infolist-settings.settings.common.tooltip'),
            'weight'              => __('fields::filament/resources/field.form.sections.infolist-settings.settings.common.weight'),
            'words'               => __('fields::filament/resources/field.form.sections.infolist-settings.settings.common.words'),
            'columnSpan'          => __('fields::filament/resources/field.form.sections.infolist-settings.settings.common.column-span'),
            'helperText'          => __('fields::filament/resources/field.form.sections.infolist-settings.settings.common.helper-text'),
            'hint'                => __('fields::filament/resources/field.form.sections.infolist-settings.settings.common.hint'),
            'hintColor'           => __('fields::filament/resources/field.form.sections.infolist-settings.settings.common.hint-color'),
            'hintIcon'            => __('fields::filament/resources/field.form.sections.infolist-settings.settings.common.hint-icon'),
        ];

        $typeSettings = match ($type) {
            'datetime' => [
                'date'            => __('fields::filament/resources/field.form.sections.infolist-settings.settings.datetime.date'),
                'dateTime'        => __('fields::filament/resources/field.form.sections.infolist-settings.settings.datetime.date-time'),
                'dateTimeTooltip' => __('fields::filament/resources/field.form.sections.infolist-settings.settings.datetime.date-time-tooltip'),
                'since'           => __('fields::filament/resources/field.form.sections.infolist-settings.settings.datetime.since'),
            ],

            'checkbox_list' => [
                'separator'             => __('fields::filament/resources/field.form.sections.infolist-settings.settings.checkbox-list.separator'),
                'listWithLineBreaks'    => __('fields::filament/resources/field.form.sections.infolist-settings.settings.checkbox-list.list-with-line-breaks'),
                'bulleted'              => __('fields::filament/resources/field.form.sections.infolist-settings.settings.checkbox-list.bulleted'),
                'limitList'             => __('fields::filament/resources/field.form.sections.infolist-settings.settings.checkbox-list.limit-list'),
                'expandableLimitedList' => __('fields::filament/resources/field.form.sections.infolist-settings.settings.checkbox-list.expandable-limited-list'),
            ],

            'select' => [
                'separator'             => __('fields::filament/resources/field.form.sections.infolist-settings.settings.select.separator'),
                'listWithLineBreaks'    => __('fields::filament/resources/field.form.sections.infolist-settings.settings.select.list-with-line-breaks'),
                'bulleted'              => __('fields::filament/resources/field.form.sections.infolist-settings.settings.select.bulleted'),
                'limitList'             => __('fields::filament/resources/field.form.sections.infolist-settings.settings.select.limit-list'),
                'expandableLimitedList' => __('fields::filament/resources/field.form.sections.infolist-settings.settings.select.expandable-limited-list'),
            ],

            'checkbox' => [
                'boolean'    => __('fields::filament/resources/field.form.sections.infolist-settings.settings.checkbox.boolean'),
                'falseIcon'  => __('fields::filament/resources/field.form.sections.infolist-settings.settings.checkbox.false-icon'),
                'trueIcon'   => __('fields::filament/resources/field.form.sections.infolist-settings.settings.checkbox.true-icon'),
                'trueColor'  => __('fields::filament/resources/field.form.sections.infolist-settings.settings.checkbox.true-color'),
                'falseColor' => __('fields::filament/resources/field.form.sections.infolist-settings.settings.checkbox.false-color'),
            ],

            'toggle' => [
                'boolean'    => __('fields::filament/resources/field.form.sections.infolist-settings.settings.toggle.boolean'),
                'falseIcon'  => __('fields::filament/resources/field.form.sections.infolist-settings.settings.toggle.false-icon'),
                'trueIcon'   => __('fields::filament/resources/field.form.sections.infolist-settings.settings.toggle.true-icon'),
                'trueColor'  => __('fields::filament/resources/field.form.sections.infolist-settings.settings.toggle.true-color'),
                'falseColor' => __('fields::filament/resources/field.form.sections.infolist-settings.settings.toggle.false-color'),
            ],

            default => [],
        };

        return array_merge($typeSettings, $commonSettings);
    }
}
