<?php

namespace Webkul\Account\Filament\Resources;

use BackedEnum;
use Exception;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Pages\Enums\SubNavigationPosition;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Webkul\Account\Enums\AmountType;
use Webkul\Account\Enums\RepartitionType;
use Webkul\Account\Enums\TaxIncludeOverride;
use Webkul\Account\Enums\TaxScope;
use Webkul\Account\Enums\TypeTaxUse;
use Webkul\Account\Filament\Resources\TaxResource\Pages\CreateTax;
use Webkul\Account\Filament\Resources\TaxResource\Pages\EditTax;
use Webkul\Account\Filament\Resources\TaxResource\Pages\ListTaxes;
use Webkul\Account\Filament\Resources\TaxResource\Pages\ViewTax;
use Webkul\Account\Models\Tax;
use Webkul\Support\Filament\Forms\Components\Repeater;
use Webkul\Support\Filament\Forms\Components\Repeater\TableColumn;

class TaxResource extends Resource
{
    protected static ?string $model = Tax::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-receipt-percent';

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?\Filament\Pages\Enums\SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Start;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        Group::make()
                            ->schema([
                                TextInput::make('name')
                                    ->label(__('accounts::filament/resources/tax.form.sections.fields.name'))
                                    ->required(),
                                Select::make('type_tax_use')
                                    ->options(TypeTaxUse::class)
                                    ->native(false)
                                    ->label(__('accounts::filament/resources/tax.form.sections.fields.tax-type'))
                                    ->required(),
                                Select::make('amount_type')
                                    ->native(false)
                                    ->options(AmountType::class)
                                    ->label(__('accounts::filament/resources/tax.form.sections.fields.tax-computation'))
                                    ->required(),
                                Select::make('tax_scope')
                                    ->native(false)
                                    ->options(TaxScope::class)
                                    ->label(__('accounts::filament/resources/tax.form.sections.fields.tax-scope')),
                                Toggle::make('is_active')
                                    ->label(__('accounts::filament/resources/tax.form.sections.fields.status'))
                                    ->inline(false),
                                TextInput::make('amount')
                                    ->label(__('accounts::filament/resources/tax.form.sections.fields.amount'))
                                    ->suffix('%')
                                    ->numeric()
                                    ->minValue(0)
                                    ->maxValue(99999999999)
                                    ->required(),
                            ])->columns(2),
                        Fieldset::make(__('accounts::filament/resources/tax.form.sections.field-set.advanced-options.title'))
                            ->schema([
                                TextInput::make('invoice_label')
                                    ->label(__('accounts::filament/resources/tax.form.sections.field-set.advanced-options.fields.invoice-label')),
                                Select::make('tax_group_id')
                                    ->relationship('taxGroup', 'name')
                                    ->required()
                                    ->native(false)
                                    ->createOptionForm(fn (Schema $schema): Schema => TaxGroupResource::form($schema))
                                    ->label(__('accounts::filament/resources/tax.form.sections.field-set.advanced-options.fields.tax-group')),
                                Select::make('country_id')
                                    ->searchable()
                                    ->preload()
                                    ->relationship('country', 'name')
                                    ->label(__('accounts::filament/resources/tax.form.sections.field-set.advanced-options.fields.country')),
                                Select::make('price_include_override')
                                    ->options(TaxIncludeOverride::class)
                                    ->native(false)
                                    ->default(TaxIncludeOverride::DEFAULT->value)
                                    ->label(__('accounts::filament/resources/tax.form.sections.field-set.advanced-options.fields.include-in-price'))
                                    ->hintIcon('heroicon-o-question-mark-circle', tooltip: __('Overrides the Company\'s default on whether the price you use on the product and invoices includes this tax.')),
                                Toggle::make('include_base_amount')
                                    ->inline(false)
                                    ->label(__('accounts::filament/resources/tax.form.sections.field-set.advanced-options.fields.include-base-amount'))
                                    ->hintIcon('heroicon-o-question-mark-circle', tooltip: __('If set, taxes with a higher sequence than this one will be affected by it, provided they accept it.')),
                                Toggle::make('is_base_affected')
                                    ->inline(false)
                                    ->label(__('accounts::filament/resources/tax.form.sections.field-set.advanced-options.fields.is-base-affected'))
                                    ->hintIcon('heroicon-o-question-mark-circle', tooltip: __('If set, taxes with a lower sequence might affect this one, provided they try to do it.')),
                            ]),
                    ]),

                Tabs::make('Tax Configuration')
                    ->tabs([
                        Tab::make('Repartition Lines')
                            ->icon('heroicon-o-banknotes')
                            ->schema([
                                Section::make('Invoice & Refund Distribution')
                                    ->description('Define how this tax affects accounts for invoices and refunds.')
                                    ->schema([
                                        Group::make()
                                            ->schema([
                                                Repeater::make('invoiceRepartitionLines')
                                                    ->label(__('accounts::filament/resources/tax.form.sections.repeater.invoice-repartition-lines.label'))
                                                    ->relationship('invoiceRepartitionLines')
                                                    ->minItems(1)
                                                    ->compact()
                                                    ->default([
                                                        ['repartition_type' => 'base', 'factor_percent' => null, 'account_id' => null],
                                                        ['repartition_type' => 'tax', 'factor_percent' => 100],
                                                    ])
                                                    ->schema([
                                                        Hidden::make('document_type')
                                                            ->default('invoice'),
                                                        Select::make('repartition_type')
                                                            ->label(__('accounts::filament/resources/tax.form.sections.repeater.fields.type'))
                                                            ->options(RepartitionType::options())
                                                            ->required()
                                                            ->native(false)
                                                            ->reactive()
                                                            ->afterStateUpdated(function ($state, callable $set) {
                                                                if ($state === 'base') {
                                                                    $set('account_id', null);
                                                                    $set('factor_percent', null);
                                                                }
                                                            }),

                                                        TextInput::make('factor_percent')
                                                            ->label(__('accounts::filament/resources/tax.form.sections.repeater.fields.factor-percent'))
                                                            ->numeric()
                                                            ->minValue(0)
                                                            ->maxValue(100)
                                                            ->required(fn (callable $get) => $get('repartition_type') !== 'base')
                                                            ->disabled(fn (callable $get) => $get('repartition_type') === 'base'),

                                                        Select::make('account_id')
                                                            ->label(__('accounts::filament/resources/tax.form.sections.repeater.fields.account'))
                                                            ->relationship('account', 'name')
                                                            ->required(fn (callable $get) => $get('repartition_type') !== 'base')
                                                            ->preload()
                                                            ->searchable()
                                                            ->disabled(fn (callable $get) => $get('repartition_type') === 'base'),
                                                    ])
                                                    ->columns(3)
                                                    ->reorderable('sort')
                                                    ->table([
                                                        TableColumn::make('repartition_type')
                                                            ->label(__('accounts::filament/resources/tax.form.sections.repeater.fields.type'))
                                                            ->width('30%'),
                                                        TableColumn::make('factor_percent')
                                                            ->label(__('accounts::filament/resources/tax.form.sections.repeater.fields.factor-percent'))
                                                            ->width(100),
                                                        TableColumn::make('account_id')
                                                            ->label(__('accounts::filament/resources/tax.form.sections.repeater.fields.account'))
                                                            ->width('clamp(200px, 14rem, 30rem)'),
                                                    ]),
                                            ])
                                            ->columnSpan(1),

                                        Group::make()
                                            ->schema([
                                                Repeater::make('refundRepartitionLines')
                                                    ->label(__('accounts::filament/resources/tax.form.sections.repeater.refund-repartition-lines.label'))
                                                    ->relationship('refundRepartitionLines')
                                                    ->minItems(1)
                                                    ->compact()
                                                    ->default([
                                                        ['repartition_type' => 'base', 'factor_percent' => null, 'account_id' => null],
                                                        ['repartition_type' => 'tax', 'factor_percent' => 100],
                                                    ])
                                                    ->schema([
                                                        Hidden::make('document_type')
                                                            ->default('refund'),
                                                        Select::make('repartition_type')
                                                            ->label(__('accounts::filament/resources/tax.form.sections.repeater.fields.type'))
                                                            ->options(RepartitionType::options())
                                                            ->required()
                                                            ->native(false)
                                                            ->reactive()
                                                            ->afterStateUpdated(function ($state, callable $set) {
                                                                if ($state === 'base') {
                                                                    $set('account_id', null);
                                                                    $set('factor_percent', null);
                                                                }
                                                            }),

                                                        TextInput::make('factor_percent')
                                                            ->label(__('accounts::filament/resources/tax.form.sections.repeater.fields.factor-percent'))
                                                            ->numeric()
                                                            ->minValue(0)
                                                            ->maxValue(100)
                                                            ->required(fn (callable $get) => $get('repartition_type') !== 'base')
                                                            ->disabled(fn (callable $get) => $get('repartition_type') === 'base'),

                                                        Select::make('account_id')
                                                            ->label(__('accounts::filament/resources/tax.form.sections.repeater.fields.account'))
                                                            ->relationship('account', 'name')
                                                            ->required(fn (callable $get) => $get('repartition_type') !== 'base')
                                                            ->preload()
                                                            ->searchable()
                                                            ->disabled(fn (callable $get) => $get('repartition_type') === 'base'),
                                                    ])
                                                    ->columns(3)
                                                    ->reorderable('sort')
                                                    ->table([
                                                        TableColumn::make('repartition_type')
                                                            ->label(__('accounts::filament/resources/tax.form.sections.repeater.fields.type'))
                                                            ->width('30%'),
                                                        TableColumn::make('factor_percent')
                                                            ->label(__('accounts::filament/resources/tax.form.sections.repeater.fields.factor-percent'))
                                                            ->width(100),
                                                        TableColumn::make('account_id')
                                                            ->label(__('accounts::filament/resources/tax.form.sections.repeater.fields.account'))
                                                            ->width('clamp(200px, 14rem, 30rem)'),
                                                    ]),
                                            ])
                                            ->columnSpan(1),
                                    ])
                                    ->columns([
                                        'default' => 1,
                                        '2xl'     => 2,
                                    ]),

                            ]),

                        Tab::make('Descriptions')
                            ->icon('heroicon-o-document-text')
                            ->schema([
                                RichEditor::make('description')
                                    ->label(__('accounts::filament/resources/tax.form.sections.field-set.fields.description')),
                                RichEditor::make('invoice_legal_notes')
                                    ->label(__('accounts::filament/resources/tax.form.sections.field-set.fields.legal-notes')),
                            ]),
                    ]),
            ])
            ->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->reorderableColumns()
            ->columnManagerColumns(2)
            ->columns([
                TextColumn::make('name')
                    ->label(__('accounts::filament/resources/tax.table.columns.name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('company.name')
                    ->label(__('accounts::filament/resources/tax.table.columns.company'))
                    ->sortable(),
                TextColumn::make('taxGroup.name')
                    ->label(__('Tax Group'))
                    ->label(__('accounts::filament/resources/tax.table.columns.tax-group'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('country.name')
                    ->label(__('accounts::filament/resources/tax.table.columns.country'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('type_tax_use')
                    ->label(__('accounts::filament/resources/tax.table.columns.tax-type'))
                    ->sortable(),
                TextColumn::make('tax_scope')
                    ->label(__('accounts::filament/resources/tax.table.columns.tax-scope'))
                    ->formatStateUsing(fn ($state) => TaxScope::options()[$state])
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('amount_type')
                    ->label(__('accounts::filament/resources/tax.table.columns.amount-type'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('invoice_label')
                    ->label(__('accounts::filament/resources/tax.table.columns.invoice-label'))
                    ->sortable(),
                TextColumn::make('tax_exigibility')
                    ->label(__('accounts::filament/resources/tax.table.columns.tax-exigibility'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('price_include_override')
                    ->label(__('accounts::filament/resources/tax.table.columns.price-include-override'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('amount')
                    ->label(__('accounts::filament/resources/tax.table.columns.amount'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                IconColumn::make('is_active')
                    ->boolean()
                    ->label(__('accounts::filament/resources/tax.table.columns.status'))
                    ->sortable(),
                IconColumn::make('include_base_amount')
                    ->boolean()
                    ->label(__('accounts::filament/resources/tax.table.columns.include-base-amount'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                IconColumn::make('is_base_affected')
                    ->boolean()
                    ->label(__('accounts::filament/resources/tax.table.columns.is-base-affected'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->groups([
                Tables\Grouping\Group::make('name')
                    ->label(__('accounts::filament/resources/tax.table.groups.name'))
                    ->collapsible(),
                Tables\Grouping\Group::make('company.name')
                    ->label(__('accounts::filament/resources/tax.table.groups.company'))
                    ->collapsible(),
                Tables\Grouping\Group::make('taxGroup.name')
                    ->label(__('accounts::filament/resources/tax.table.groups.tax-group'))
                    ->collapsible(),
                Tables\Grouping\Group::make('country.name')
                    ->label(__('accounts::filament/resources/tax.table.groups.country'))
                    ->collapsible(),
                Tables\Grouping\Group::make('creator.name')
                    ->label(__('accounts::filament/resources/tax.table.groups.created-by'))
                    ->collapsible(),
                Tables\Grouping\Group::make('type_tax_use')
                    ->label(__('accounts::filament/resources/tax.table.groups.type-tax-use'))
                    ->collapsible(),
                Tables\Grouping\Group::make('tax_scope')
                    ->label(__('accounts::filament/resources/tax.table.groups.tax-scope'))
                    ->collapsible(),
                Tables\Grouping\Group::make('amount_type')
                    ->label(__('accounts::filament/resources/tax.table.groups.amount-type'))
                    ->collapsible(),
            ])
            ->recordActions([
                ActionGroup::make([
                    EditAction::make(),
                    ViewAction::make(),
                    DeleteAction::make()
                        ->action(function (Tax $record) {
                            try {
                                $record->delete();
                            } catch (QueryException $e) {
                                Notification::make()
                                    ->danger()
                                    ->title(__('accounts::filament/resources/tax.table.actions.delete.notification.error.title'))
                                    ->body(__('accounts::filament/resources/tax.table.actions.delete.notification.error.body'))
                                    ->send();
                            }
                        })
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('accounts::filament/resources/tax.table.actions.delete.notification.success.title'))
                                ->body(__('accounts::filament/resources/tax.table.actions.delete.notification.success.body'))
                        ),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->action(function (Collection $records) {
                            try {
                                $records->each(fn (Model $record) => $record->delete());
                            } catch (QueryException $e) {
                                Notification::make()
                                    ->danger()
                                    ->title(__('accounts::filament/resources/tax.table.bulk-actions.delete.notification.error.title'))
                                    ->body(__('accounts::filament/resources/tax.table.bulk-actions.delete.notification.error.body'))
                                    ->send();
                            }
                        })
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('accounts::filament/resources/tax.table.bulk-actions.delete.notification.success.title'))
                                ->body(__('accounts::filament/resources/tax.table.bulk-actions.delete.notification.success.body'))
                        ),
                ]),
            ])
            ->reorderable('sort', 'desc');
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(['default' => 3])
                    ->schema([
                        Group::make()
                            ->schema([
                                Section::make()
                                    ->schema([
                                        TextEntry::make('name')
                                            ->icon('heroicon-o-document-text')
                                            ->label(__('accounts::filament/resources/tax.infolist.sections.entries.name'))
                                            ->placeholder('—'),
                                        TextEntry::make('type_tax_use')
                                            ->icon('heroicon-o-calculator')
                                            ->label(__('accounts::filament/resources/tax.infolist.sections.entries.tax-type'))
                                            ->placeholder('—'),
                                        TextEntry::make('amount_type')
                                            ->icon('heroicon-o-calculator')
                                            ->label(__('accounts::filament/resources/tax.infolist.sections.entries.tax-computation'))
                                            ->placeholder('—'),
                                        TextEntry::make('tax_scope')
                                            ->icon('heroicon-o-globe-alt')
                                            ->label(__('accounts::filament/resources/tax.infolist.sections.entries.tax-scope'))
                                            ->placeholder('—'),
                                        TextEntry::make('amount')
                                            ->icon('heroicon-o-currency-dollar')
                                            ->label(__('accounts::filament/resources/tax.infolist.sections.entries.amount'))
                                            ->suffix('%')
                                            ->placeholder('—'),
                                        IconEntry::make('is_active')
                                            ->boolean()
                                            ->label(__('accounts::filament/resources/tax.infolist.sections.entries.status')),
                                    ])->columns(2),
                                Section::make()
                                    ->schema([
                                        TextEntry::make('description')
                                            ->label(__('accounts::filament/resources/tax.infolist.sections.field-set.description-and-legal-notes.entries.description'))
                                            ->markdown()
                                            ->placeholder('—')
                                            ->columnSpanFull(),
                                        TextEntry::make('invoice_legal_notes')
                                            ->label(__('accounts::filament/resources/tax.infolist.sections.field-set.description-and-legal-notes.entries.legal-notes'))
                                            ->markdown()
                                            ->placeholder('—')
                                            ->columnSpanFull(),
                                    ]),
                            ])->columnSpan(2),
                        Group::make([
                            Section::make()
                                ->schema([
                                    TextEntry::make('invoice_label')
                                        ->label(__('accounts::filament/resources/tax.infolist.sections.field-set.advanced-options.entries.invoice-label'))
                                        ->placeholder('—'),
                                    TextEntry::make('taxGroup.name')
                                        ->label(__('accounts::filament/resources/tax.infolist.sections.field-set.advanced-options.entries.tax-group'))
                                        ->placeholder('—'),
                                    TextEntry::make('country.name')
                                        ->label(__('accounts::filament/resources/tax.infolist.sections.field-set.advanced-options.entries.country'))
                                        ->placeholder('—'),
                                    TextEntry::make('price_include_override')
                                        ->label(__('accounts::filament/resources/tax.infolist.sections.field-set.advanced-options.entries.include-in-price')),
                                    IconEntry::make('include_base_amount')
                                        ->boolean()
                                        ->label(__('accounts::filament/resources/tax.infolist.sections.field-set.advanced-options.entries.include-base-amount')),
                                    IconEntry::make('is_base_affected')
                                        ->boolean()
                                        ->label(__('accounts::filament/resources/tax.infolist.sections.field-set.advanced-options.entries.is-base-affected')),
                                ]),
                        ])->columnSpan(1),
                    ]),
            ])->columns(1);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListTaxes::route('/'),
            'create' => CreateTax::route('/create'),
            'view'   => ViewTax::route('/{record}'),
            'edit'   => EditTax::route('/{record}/edit'),
        ];
    }

    public static function validateRepartitionData(array $invoiceLines, array $refundLines): void
    {
        $invoice = collect($invoiceLines)->values();
        $refund = collect($refundLines)->values();

        if (
            $invoice->where('repartition_type', 'base')->count() !== 1 ||
            $refund->where('repartition_type', 'base')->count() !== 1
        ) {
            throw new Exception('Each must contain exactly one BASE repartition line.');
        }

        if (
            $invoice->where('repartition_type', 'tax')->isEmpty() ||
            $refund->where('repartition_type', 'tax')->isEmpty()
        ) {
            throw new Exception('Each must contain at least one TAX repartition line.');
        }

        if ($invoice->count() !== $refund->count()) {
            throw new Exception('Invoice and refund must have the same number of repartition lines.');
        }

        foreach ($invoice as $index => $invLine) {

            $refLine = $refund[$index] ?? null;

            $invPercent = (float) ($invLine['factor_percent'] ?? 0);
            $refPercent = (float) ($refLine['factor_percent'] ?? 0);

            if (
                ! $refLine ||
                $invLine['repartition_type'] !== $refLine['repartition_type'] ||
                $invPercent !== $refPercent

            ) {
                throw new Exception('Line #'.($index + 1).' does not match between Invoice and Refund.');
            }
        }

        $positive = $invoice
            ->filter(fn ($l) => $l['repartition_type'] === 'tax' && is_numeric($l['factor_percent'] ?? null) && $l['factor_percent'] > 0)
            ->sum(fn ($l) => (float) $l['factor_percent']);

        $negative = $invoice
            ->filter(fn ($l) => $l['repartition_type'] === 'tax' && is_numeric($l['factor_percent'] ?? null) && $l['factor_percent'] < 0)
            ->sum(fn ($l) => (float) $l['factor_percent']);

        if (bccomp(number_format($positive, 2, '.', ''), '100', 2) !== 0) {
            throw new Exception("Invoice total positive TAX percentages must equal 100% (got {$positive}%).");
        }

        if ($negative && bccomp(number_format($negative, 2, '.', ''), '-100', 2) !== 0) {
            throw new Exception("Invoice total negative TAX percentages must equal -100% (got {$negative}%).");
        }
    }
}
