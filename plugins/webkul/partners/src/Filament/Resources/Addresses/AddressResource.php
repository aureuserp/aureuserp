<?php

namespace Webkul\Partner\Filament\Resources\Addresses;

use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Webkul\Partner\Filament\Resources\Addresses\Schemas\AddressForm;
use Webkul\Partner\Filament\Resources\Addresses\Tables\AddressesTable;
use Webkul\Partner\Models\Partner;

class AddressResource extends Resource
{
    protected static ?string $model = Partner::class;

    protected static bool $shouldRegisterNavigation = false;

    public static function form(Schema $schema): Schema
    {
        return AddressForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AddressesTable::configure($table);
    }
}
