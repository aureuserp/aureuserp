<?php

namespace Webkul\Account\Filament\Resources;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Webkul\Account\Filament\Resources\JournalResource\Pages\CreateJournal;
use Webkul\Account\Filament\Resources\JournalResource\Pages\EditJournal;
use Webkul\Account\Filament\Resources\JournalResource\Pages\ListJournals;
use Webkul\Account\Filament\Resources\JournalResource\Pages\ViewJournal;
use Webkul\Account\Filament\Resources\JournalResource\Schemas\JournalForm;
use Webkul\Account\Filament\Resources\JournalResource\Schemas\JournalInfolist;
use Webkul\Account\Filament\Resources\JournalResource\Tables\JournalsTable;
use Webkul\Account\Models\Journal;

class JournalResource extends Resource
{
    protected static ?string $model = Journal::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-book-open';

    protected static bool $shouldRegisterNavigation = false;

    protected static bool $isGloballySearchable = false;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return JournalForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return JournalsTable::configure($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return JournalInfolist::configure($schema);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListJournals::route('/'),
            'create' => CreateJournal::route('/create'),
            'view'   => ViewJournal::route('/{record}'),
            'edit'   => EditJournal::route('/{record}/edit'),
        ];
    }
}
