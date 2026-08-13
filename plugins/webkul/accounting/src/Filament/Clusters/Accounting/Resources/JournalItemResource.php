<?php

namespace Webkul\Accounting\Filament\Clusters\Accounting\Resources;

use Filament\Resources\Resource;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Webkul\Accounting\Filament\Clusters\Accounting;
use Webkul\Accounting\Filament\Clusters\Accounting\Resources\JournalItemResource\Pages\ListJournalItems;
use Webkul\Accounting\Filament\Clusters\Accounting\Resources\JournalItemResource\Tables\JournalItemsTable;
use Webkul\Accounting\Models\JournalItem;

class JournalItemResource extends Resource
{
    protected static ?string $model = JournalItem::class;

    protected static bool $shouldRegisterNavigation = true;

    protected static ?string $cluster = Accounting::class;

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'name';

    protected static bool $isGloballySearchable = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-queue-list';

    public static function getModelLabel(): string
    {
        return __('accounting::filament/clusters/accounting/resources/journal-item.title');
    }

    public static function getNavigationLabel(): string
    {
        return __('accounting::filament/clusters/accounting/resources/journal-item.navigation.title');
    }

    public static function table(Table $table): Table
    {
        return JournalItemsTable::configure($table);
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(Model $record): bool
    {
        return false;
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }

    public static function canDeleteAny(): bool
    {
        return false;
    }

    public static function getPages(): array
    {
        return [
            'index' => ListJournalItems::route('/'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->orderByDesc('date');
    }
}
