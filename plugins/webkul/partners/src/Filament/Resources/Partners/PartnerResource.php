<?php

namespace Webkul\Partner\Filament\Resources\Partners;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Webkul\Partner\Filament\Resources\Partners\Schemas\PartnerForm;
use Webkul\Partner\Filament\Resources\Partners\Schemas\PartnerInfolist;
use Webkul\Partner\Filament\Resources\Partners\Tables\PartnersTable;
use Webkul\Partner\Models\Partner;

class PartnerResource extends Resource
{
    protected static ?string $model = Partner::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-users';

    protected static bool $shouldRegisterNavigation = false;

    public static function form(Schema $schema): Schema
    {
        return PartnerForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PartnersTable::configure($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return PartnerInfolist::configure($schema);
    }
}
