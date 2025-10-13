<?php

namespace Webkul\Partner\Filament\Resources\PartnerResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Webkul\Partner\Filament\Resources\AddressResource;

class AddressesRelationManager extends RelationManager
{
    protected static string $relationship = 'addresses';

    public function form(Schema $schema): Schema
    {
        return AddressResource::form($schema);
    }

    public function table(Table $table): Table
    {
        return AddressResource::table($table);
    }
}
