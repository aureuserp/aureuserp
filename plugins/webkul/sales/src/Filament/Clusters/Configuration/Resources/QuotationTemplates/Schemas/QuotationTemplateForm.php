<?php

namespace Webkul\Sale\Filament\Clusters\Configuration\Resources\QuotationTemplates\Schemas;

use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Webkul\Sale\Filament\Clusters\Configuration\Resources\QuotationTemplates\QuotationTemplateResource;

class QuotationTemplateForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make()
                    ->schema([
                        Group::make()
                            ->schema([
                                Tabs::make()
                                    ->tabs([
                                        Tab::make(__('sales::filament/clusters/configurations/resources/quotation-template.form.tabs.products.title'))
                                            ->schema([
                                                QuotationTemplateResource::getProductRepeater(),
                                                QuotationTemplateResource::getSectionRepeater(),
                                                QuotationTemplateResource::getNoteRepeater(),
                                            ]),
                                        Tab::make(__('sales::filament/clusters/configurations/resources/quotation-template.form.tabs.terms-and-conditions.title'))
                                            ->schema([
                                                RichEditor::make('note')
                                                    ->placeholder(__('sales::filament/clusters/configurations/resources/quotation-template.form.tabs.terms-and-conditions.note-placeholder'))
                                                    ->hiddenLabel(),
                                            ]),
                                    ])
                                    ->persistTabInQueryString(),
                            ])
                            ->columnSpan(['lg' => 2]),
                        Group::make()
                            ->schema([
                                Section::make()
                                    ->schema([
                                        Fieldset::make(__('sales::filament/clusters/configurations/resources/quotation-template.form.sections.general.title'))
                                            ->schema([
                                                TextInput::make('name')
                                                    ->label(__('sales::filament/clusters/configurations/resources/quotation-template.form.sections.general.fields.name'))
                                                    ->required(),
                                                TextInput::make('number_of_days')
                                                    ->label(__('sales::filament/clusters/configurations/resources/quotation-template.form.sections.general.fields.quotation-validity'))
                                                    ->default(0)
                                                    ->required(),
                                                Select::make('journal_id')
                                                    ->relationship('journal', 'name')
                                                    ->searchable()
                                                    ->preload()
                                                    ->label(__('sales::filament/clusters/configurations/resources/quotation-template.form.sections.general.fields.sale-journal'))
                                                    ->required(),
                                            ])->columns(1),
                                    ]),
                                Section::make()
                                    ->schema([
                                        Fieldset::make(__('sales::filament/clusters/configurations/resources/quotation-template.form.sections.signature-and-payment.title'))
                                            ->schema([
                                                Toggle::make('require_signature')
                                                    ->label(__('sales::filament/clusters/configurations/resources/quotation-template.form.sections.signature-and-payment.fields.online-signature')),
                                                Toggle::make('require_payment')
                                                    ->live()
                                                    ->label(__('sales::filament/clusters/configurations/resources/quotation-template.form.sections.signature-and-payment.fields.online-payment')),
                                                TextInput::make('prepayment_percentage')
                                                    ->prefix('of')
                                                    ->suffix('%')
                                                    ->label(__('sales::filament/clusters/configurations/resources/quotation-template.form.sections.signature-and-payment.fields.prepayment-percentage'))
                                                    ->visible(fn (Get $get) => $get('require_payment') === true),
                                            ])->columns(1),
                                    ]),
                            ])
                            ->columnSpan(['lg' => 1]),
                    ])
                    ->columns(3),
            ])
            ->columns('full');
    }
}
