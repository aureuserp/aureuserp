<?php

namespace Webkul\Account\Filament\Resources\PaymentTerms;

use BackedEnum;
use Filament\Pages\Enums\SubNavigationPosition;
use Filament\Resources\Pages\Page;
use Filament\Resources\RelationManagers\RelationGroup;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Webkul\Account\Filament\Resources\PaymentTerms\Pages\CreatePaymentTerm;
use Webkul\Account\Filament\Resources\PaymentTerms\Pages\EditPaymentTerm;
use Webkul\Account\Filament\Resources\PaymentTerms\Pages\ListPaymentTerms;
use Webkul\Account\Filament\Resources\PaymentTerms\Pages\ManagePaymentDueTerm;
use Webkul\Account\Filament\Resources\PaymentTerms\Pages\ViewPaymentTerm;
use Webkul\Account\Filament\Resources\PaymentTerms\RelationManagers\PaymentDueTermRelationManager;
use Webkul\Account\Filament\Resources\PaymentTerms\Schemas\PaymentTermForm;
use Webkul\Account\Filament\Resources\PaymentTerms\Schemas\PaymentTermInfolist;
use Webkul\Account\Filament\Resources\PaymentTerms\Tables\PaymentTermsTable;
use Webkul\Account\Models\PaymentTerm;

class PaymentTermResource extends Resource
{
    protected static ?string $model = PaymentTerm::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-currency-dollar';

    protected static ?SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Start;

    protected static bool $shouldRegisterNavigation = false;

    public static function form(Schema $schema): Schema
    {
        return PaymentTermForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PaymentTermsTable::configure($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return PaymentTermInfolist::configure($schema);
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            ViewPaymentTerm::class,
            EditPaymentTerm::class,
            ManagePaymentDueTerm::class,
        ]);
    }

    public static function getRelations(): array
    {
        $relations = [
            RelationGroup::make('due_terms', [
                PaymentDueTermRelationManager::class,
            ])
                ->icon('heroicon-o-banknotes'),
        ];

        return $relations;
    }

    public static function getPages(): array
    {
        return [
            'index'             => ListPaymentTerms::route('/'),
            'create'            => CreatePaymentTerm::route('/create'),
            'view'              => ViewPaymentTerm::route('/{record}'),
            'edit'              => EditPaymentTerm::route('/{record}/edit'),
            'payment-due-terms' => ManagePaymentDueTerm::route('/{record}/payment-due-terms'),
        ];
    }
}
