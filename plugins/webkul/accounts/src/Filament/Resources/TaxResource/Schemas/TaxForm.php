<?php

namespace Webkul\Account\Filament\Resources\TaxResource\Schemas;

use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Webkul\Account\Enums\AmountType;
use Webkul\Account\Enums\RepartitionType;
use Webkul\Account\Enums\TaxIncludeOverride;
use Webkul\Account\Enums\TaxScope;
use Webkul\Account\Enums\TypeTaxUse;
use Webkul\Account\Filament\Resources\TaxGroupResource;
use Webkul\Support\Filament\Forms\Components\Repeater;
use Webkul\Support\Filament\Forms\Components\Repeater\TableColumn;

class TaxForm
{
    public static function configure(Schema $schema): Schema
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
}
