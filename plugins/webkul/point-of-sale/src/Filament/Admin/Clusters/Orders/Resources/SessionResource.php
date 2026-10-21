<?php

namespace Webkul\PointOfSale\Filament\Admin\Clusters\Orders\Resources;

use BackedEnum;
use Filament\Resources\Pages\Page;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Webkul\PointOfSale\Filament\Admin\Clusters\Orders;
use Webkul\PointOfSale\Filament\Admin\Clusters\Orders\Resources\SessionResource\Pages\ListSessions;
use Webkul\PointOfSale\Filament\Admin\Clusters\Orders\Resources\SessionResource\Pages\ManageOrders;
use Webkul\PointOfSale\Filament\Admin\Clusters\Orders\Resources\SessionResource\Pages\ManagePayments;
use Webkul\PointOfSale\Filament\Admin\Clusters\Orders\Resources\SessionResource\Pages\ManagePickings;
use Webkul\PointOfSale\Filament\Admin\Clusters\Orders\Resources\SessionResource\Pages\ViewSession;
use Webkul\PointOfSale\Filament\Admin\Clusters\Orders\Resources\SessionResource\Schemas\SessionInfolist;
use Webkul\PointOfSale\Filament\Admin\Clusters\Orders\Resources\SessionResource\Tables\SessionsTable;
use Webkul\PointOfSale\Models\Session;

class SessionResource extends Resource
{
    protected static ?string $model = Session::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-clock';

    protected static ?int $navigationSort = 2;

    protected static ?string $cluster = Orders::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getModelLabel(): string
    {
        return __('point-of-sale::models/session.title');
    }

    public static function getPluralModelLabel(): string
    {
        return __('point-of-sale::models/session.plural-title');
    }

    public static function getNavigationGroup(): ?string
    {
        return null;
    }

    public static function getNavigationLabel(): string
    {
        return __('point-of-sale::filament/admin/clusters/orders/resources/session.navigation.title');
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name'];
    }

    public static function infolist(Schema $schema): Schema
    {
        return SessionInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SessionsTable::configure($table);
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            ViewSession::class,
            ManageOrders::class,
            ManagePickings::class,
            ManagePayments::class,
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index'    => ListSessions::route('/'),
            'view'     => ViewSession::route('/{record}'),
            'orders'   => ManageOrders::route('/{record}/orders'),
            'pickings' => ManagePickings::route('/{record}/pickings'),
            'payments' => ManagePayments::route('/{record}/payments'),
        ];
    }
}
