<?php

namespace Webkul\Partner\Filament\Resources\Titles;

use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Webkul\Partner\Filament\Resources\Titles\Schemas\TitleForm;
use Webkul\Partner\Filament\Resources\Titles\Tables\TitlesTable;
use Webkul\Partner\Models\Title;

class TitleResource extends Resource
{
    protected static ?string $model = Title::class;

    protected static bool $shouldRegisterNavigation = false;

    public static function form(Schema $schema): Schema
    {
        return TitleForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TitlesTable::configure($table);
    }
}
