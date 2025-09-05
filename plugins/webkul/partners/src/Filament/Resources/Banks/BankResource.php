<?php

namespace Webkul\Partner\Filament\Resources\Banks;

use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Webkul\Partner\Filament\Resources\Banks\Schemas\BankForm;
use Webkul\Partner\Filament\Resources\Banks\Tables\BanksTable;
use Webkul\Support\Models\Bank;

class BankResource extends Resource
{
    protected static ?string $model = Bank::class;

    protected static bool $shouldRegisterNavigation = false;

    public static function getNavigationGroup(): string
    {
        return __('partners::filament/resources/bank.navigation.group');
    }

    public static function getNavigationLabel(): string
    {
        return __('partners::filament/resources/bank.navigation.title');
    }

    public static function form(Schema $schema): Schema
    {
        return BankForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BanksTable::configure($table);
    }
}
