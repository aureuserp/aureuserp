<?php

namespace Webkul\Account\Filament\Resources\Journals;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Webkul\Account\Filament\Resources\Journals\Pages\CreateJournal;
use Webkul\Account\Filament\Resources\Journals\Pages\EditJournal;
use Webkul\Account\Filament\Resources\Journals\Pages\ListJournals;
use Webkul\Account\Filament\Resources\Journals\Pages\ViewJournal;
use Webkul\Account\Filament\Resources\Journals\Schemas\JournalForm;
use Webkul\Account\Filament\Resources\Journals\Schemas\JournalInfolist;
use Webkul\Account\Filament\Resources\Journals\Tables\JournalsTable;
use Webkul\Account\Models\Journal;

class JournalResource extends Resource
{
    protected static ?string $model = Journal::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-book-open';

    protected static bool $shouldRegisterNavigation = false;

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
