<?php

namespace Webkul\Inventory\Filament\Clusters\Products\Resources\Lots\Schemas;

use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Inventory\Enums\ProductTracking;
use Webkul\Inventory\Filament\Clusters\Operations\Resources\Deliveries\Pages\EditDelivery;
use Webkul\Inventory\Filament\Clusters\Operations\Resources\Dropships\Pages\EditDropship;
use Webkul\Inventory\Filament\Clusters\Operations\Resources\Internals\Pages\EditInternal;
use Webkul\Inventory\Filament\Clusters\Operations\Resources\Receipts\Pages\EditReceipt;
use Webkul\Inventory\Filament\Clusters\Operations\Resources\Scraps\Pages\CreateScrap;
use Webkul\Inventory\Filament\Clusters\Operations\Resources\Scraps\Pages\EditScrap;
use Webkul\Inventory\Filament\Clusters\Products\Resources\Products\Pages\ManageQuantities;

class LotForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('inventories::filament/clusters/products/resources/lot.form.sections.general.title'))
                    ->schema([
                        TextInput::make('name')
                            ->label(__('inventories::filament/clusters/products/resources/lot.form.sections.general.fields.name'))
                            ->required()
                            ->maxLength(255)
                            ->autofocus()
                            ->placeholder(__('inventories::filament/clusters/products/resources/lot.form.sections.general.fields.name-placeholder'))
                            ->extraInputAttributes(['style' => 'font-size: 1.5rem;height: 3rem;']),
                        Group::make()
                            ->schema([
                                Select::make('product_id')
                                    ->label(__('inventories::filament/clusters/products/resources/lot.form.sections.general.fields.product'))
                                    ->relationship('product', 'name')
                                    ->relationship(
                                        name: 'product',
                                        titleAttribute: 'name',
                                        modifyQueryUsing: fn (Builder $query) => $query->where('tracking', ProductTracking::LOT)->whereNull('is_configurable'),
                                    )
                                    ->required()
                                    ->searchable()
                                    ->preload()
                                    ->hintIcon('heroicon-m-question-mark-circle', tooltip: __('inventories::filament/clusters/products/resources/lot.form.sections.general.fields.product-hint-tooltip'))
                                    ->hiddenOn([
                                        EditReceipt::class,
                                        EditDelivery::class,
                                        EditInternal::class,
                                        EditDropship::class,
                                        ManageQuantities::class,
                                        CreateScrap::class,
                                        EditScrap::class,
                                    ]),
                                TextInput::make('reference')
                                    ->label(__('inventories::filament/clusters/products/resources/lot.form.sections.general.fields.reference'))
                                    ->maxLength(255)
                                    ->hintIcon('heroicon-m-question-mark-circle', tooltip: __('inventories::filament/clusters/products/resources/lot.form.sections.general.fields.reference-hint-tooltip')),
                                RichEditor::make('description')
                                    ->label(__('inventories::filament/clusters/products/resources/lot.form.sections.general.fields.description'))
                                    ->columnSpan(2),
                            ])
                            ->columns(2),
                    ]),
            ]);
    }
}
