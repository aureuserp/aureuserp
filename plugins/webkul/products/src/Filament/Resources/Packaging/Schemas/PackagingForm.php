<?php

namespace Webkul\Product\Filament\Resources\Packaging\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;

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
                        modifyQueryUsing: fn (Builder $query) => $query->withTrashed(),
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
                    ->preload(),
            ]);
    }
}
