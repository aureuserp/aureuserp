<?php

namespace Webkul\Product\Filament\Resources\Packaging;

use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Webkul\Product\Filament\Resources\Packaging\Schemas\PackagingForm;
use Webkul\Product\Filament\Resources\Packaging\Schemas\PackagingInfolist;
use Webkul\Product\Filament\Resources\Packaging\Tables\PackagingTable;
use Webkul\Product\Models\Packaging;

class PackagingResource extends Resource
{
    protected static ?string $model = Packaging::class;

    protected static bool $shouldRegisterNavigation = false;

    public static function form(Schema $schema): Schema
    {
        return PackagingForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PackagingTable::configure($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return PackagingInfolist::configure($schema);
    }
}
