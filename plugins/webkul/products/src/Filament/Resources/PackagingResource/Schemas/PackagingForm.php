<?php

namespace Webkul\Product\Filament\Resources\PackagingResource\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Product\Models\Product;

class PackagingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label(__('products::filament/resources/packaging.form.name'))
                    ->required()
                    ->maxLength(255),
                TextInput::make('barcode')
                    ->label(__('products::filament/resources/packaging.form.barcode'))
                    ->maxLength(255),
                Select::make('product_id')
                    ->label(__('products::filament/resources/packaging.form.product'))
                    ->relationship(
                        'product',
                        'name',
                        modifyQueryUsing: fn (Builder $query, Get $get) => $query
                            ->withTrashed()
                            ->where('type', 'goods')
                            ->where(owned_by_company($get('company_id')))
                            ->orderBy('id'),
                    )
                    ->getOptionLabelFromRecordUsing(function ($record): string {
                        return $record->name.($record->trashed() ? ' (Deleted)' : '');
                    })
                    ->disableOptionWhen(function ($label) {
                        return str_contains($label, ' (Deleted)');
                    })
                    ->required()
                    ->searchable()
                    ->preload(),
                TextInput::make('qty')
                    ->label(__('products::filament/resources/packaging.form.qty'))
                    ->required()
                    ->numeric()
                    ->minValue(0.00)
                    ->maxValue(99999999),
                Select::make('company_id')
                    ->label(__('products::filament/resources/packaging.form.company'))
                    ->relationship('company', 'name')
                    ->searchable()
                    ->preload()
                    ->default(current_company_id())
                    ->live()
                    ->afterStateUpdated(fn (Set $set, Get $get, $state) => clear_foreign_company_values($set, $get, [
                        'product_id' => Product::class,
                    ], $state)),
            ]);
    }
}
