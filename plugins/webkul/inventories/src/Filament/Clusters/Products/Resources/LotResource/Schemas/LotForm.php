<?php

namespace Webkul\Inventory\Filament\Clusters\Products\Resources\LotResource\Schemas;

use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Inventory\Enums\ProductTracking;
use Webkul\Inventory\Filament\Clusters\Operations\Resources\DeliveryResource\Pages\EditDelivery;
use Webkul\Inventory\Filament\Clusters\Operations\Resources\DropshipResource\Pages\EditDropship;
use Webkul\Inventory\Filament\Clusters\Operations\Resources\InternalResource\Pages\EditInternal;
use Webkul\Inventory\Filament\Clusters\Operations\Resources\ReceiptResource\Pages\EditReceipt;
use Webkul\Inventory\Filament\Clusters\Operations\Resources\ScrapResource\Pages\CreateScrap;
use Webkul\Inventory\Filament\Clusters\Operations\Resources\ScrapResource\Pages\EditScrap;
use Webkul\Inventory\Filament\Clusters\Products\Resources\ProductResource\Pages\ManageQuantities;
use Webkul\Inventory\Models\Product;

class LotForm
{
    public static function configure(Schema $schema, array $customFormFields = []): Schema
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
                                    ->relationship(
                                        name: 'product',
                                        titleAttribute: 'name',
                                        modifyQueryUsing: fn (Builder $query, Get $get) => $query
                                            ->withTrashed()
                                            ->whereIn('tracking', [ProductTracking::LOT, ProductTracking::SERIAL])
                                            ->whereNull('is_configurable')
                                            ->when(
                                                $get('company_id'),
                                                fn (Builder $scoped, $companyId) => $scoped->where(owned_by_company($companyId)),
                                            ),
                                    )
                                    ->getOptionLabelFromRecordUsing(function ($record): string {
                                        return $record->name.($record->trashed() ? ' (Deleted)' : '');
                                    })
                                    ->disableOptionWhen(function ($label) {
                                        return str_contains($label, ' (Deleted)');
                                    })
                                    ->required()
                                    ->searchable()
                                    ->preload()
                                    ->live()
                                    ->afterStateUpdated(function (Set $set, $state): void {
                                        if (blank($state)) {
                                            return;
                                        }

                                        $set('company_id', Product::query()->withTrashed()->find($state)?->company_id);
                                    })
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
                                Select::make('company_id')
                                    ->label(__('inventories::filament/clusters/products/resources/lot.form.sections.general.fields.company'))
                                    ->relationship('company', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->live()
                                    ->afterStateUpdated(fn (Set $set, Get $get, $state) => clear_foreign_company_values($set, $get, [
                                        'product_id' => Product::class,
                                    ], $state))
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
                    ])->columnSpanFull(),
                Section::make()
                    ->schema($customFormFields)
                    ->columns(2),
            ]);
    }
}
