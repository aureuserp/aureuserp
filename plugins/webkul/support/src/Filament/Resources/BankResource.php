<?php

namespace Webkul\Support\Filament\Resources;

use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Webkul\Support\Filament\Resources\BankResource\Schemas\BankForm;
use Webkul\Support\Filament\Resources\BankResource\Tables\BanksTable;
use Webkul\Support\Models\Bank;

class BankResource extends Resource
{
    protected static ?string $model = Bank::class;

    protected static bool $shouldRegisterNavigation = false;

    public static function getNavigationGroup(): string
    {
        return __('support::filament/resources/bank.navigation.group');
    }

    public static function getNavigationLabel(): string
    {
        return __('support::filament/resources/bank.navigation.title');
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
