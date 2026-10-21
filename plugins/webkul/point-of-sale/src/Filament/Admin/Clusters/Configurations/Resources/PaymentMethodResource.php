<?php

namespace Webkul\PointOfSale\Filament\Admin\Clusters\Configurations\Resources;

use BackedEnum;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Webkul\PointOfSale\Exceptions\PosConfigurationException;
use Webkul\PointOfSale\Filament\Admin\Clusters\Configurations;
use Webkul\PointOfSale\Filament\Admin\Clusters\Configurations\Resources\PaymentMethodResource\Pages\CreatePaymentMethod;
use Webkul\PointOfSale\Filament\Admin\Clusters\Configurations\Resources\PaymentMethodResource\Pages\EditPaymentMethod;
use Webkul\PointOfSale\Filament\Admin\Clusters\Configurations\Resources\PaymentMethodResource\Pages\ListPaymentMethods;
use Webkul\PointOfSale\Filament\Admin\Clusters\Configurations\Resources\PaymentMethodResource\Pages\ViewPaymentMethod;
use Webkul\PointOfSale\Filament\Admin\Clusters\Configurations\Resources\PaymentMethodResource\Schemas\PaymentMethodForm;
use Webkul\PointOfSale\Filament\Admin\Clusters\Configurations\Resources\PaymentMethodResource\Schemas\PaymentMethodInfolist;
use Webkul\PointOfSale\Filament\Admin\Clusters\Configurations\Resources\PaymentMethodResource\Tables\PaymentMethodsTable;
use Webkul\PointOfSale\Models\PaymentMethod;

class PaymentMethodResource extends Resource
{
    protected static ?string $model = PaymentMethod::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-banknotes';

    protected static ?int $navigationSort = 1;

    protected static ?string $cluster = Configurations::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getModelLabel(): string
    {
        return __('point-of-sale::models/payment-method.title');
    }

    public static function getPluralModelLabel(): string
    {
        return __('point-of-sale::models/payment-method.plural-title');
    }

    public static function getNavigationGroup(): ?string
    {
        return null;
    }

    public static function getNavigationLabel(): string
    {
        return __('point-of-sale::filament/admin/clusters/configurations/resources/payment-method.navigation.title');
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name'];
    }

    public static function form(Schema $schema): Schema
    {
        return PaymentMethodForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PaymentMethodsTable::configure($table);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withoutGlobalScopes([
            SoftDeletingScope::class,
        ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return PaymentMethodInfolist::configure($schema);
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            ViewPaymentMethod::class,
            EditPaymentMethod::class,
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListPaymentMethods::route('/'),
            'create' => CreatePaymentMethod::route('/create'),
            'view'   => ViewPaymentMethod::route('/{record}'),
            'edit'   => EditPaymentMethod::route('/{record}/edit'),
        ];
    }

    public static function haltWhenInOpenSession(Action $action, iterable $records): void
    {
        foreach ($records as $record) {
            try {
                $record->assertNotInOpenSession();
            } catch (PosConfigurationException $exception) {
                Notification::make()
                    ->danger()
                    ->title(__('point-of-sale::filament/admin/clusters/configurations/resources/payment-method.in-open-session.title'))
                    ->body($exception->getMessage())
                    ->send();

                $action->halt();
            }
        }
    }
}
