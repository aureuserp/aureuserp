<?php

namespace Webkul\Account\Filament\Resources\Taxes\Schemas;

use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Webkul\Account\Enums\AmountType;
use Webkul\Account\Enums\TaxIncludeOverride;
use Webkul\Account\Enums\TaxScope;
use Webkul\Account\Enums\TypeTaxUse;
use Webkul\Account\Filament\Resources\TaxGroups\TaxGroupResource;

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
                                    ->options(TypeTaxUse::options())
                                    ->label(__('accounts::filament/resources/tax.form.sections.fields.tax-type'))
                                    ->required(),
                                Select::make('amount_type')
                                    ->options(AmountType::options())
                                    ->label(__('accounts::filament/resources/tax.form.sections.fields.tax-computation'))
                                    ->required(),
                                Select::make('tax_scope')
                                    ->options(TaxScope::options())
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
                                    ->createOptionForm(fn (Schema $schema): Schema => TaxGroupResource::form($schema))
                                    ->label(__('accounts::filament/resources/tax.form.sections.field-set.advanced-options.fields.tax-group')),
                                Select::make('country_id')
                                    ->relationship('country', 'name')
                                    ->label(__('accounts::filament/resources/tax.form.sections.field-set.advanced-options.fields.country')),
                                Select::make('price_include_override')
                                    ->options(TaxIncludeOverride::class)
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
                        RichEditor::make('description')
                            ->label(__('accounts::filament/resources/tax.form.sections.field-set.fields.description')),
                        RichEditor::make('invoice_legal_notes')
                            ->label(__('accounts::filament/resources/tax.form.sections.field-set.fields.legal-notes')),
                    ]),
            ]);
    }
}
