<?php

namespace Webkul\Accounting\Filament\Clusters\Configuration\Resources\JournalResource\Pages;

use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Tables\Table;
use Webkul\Accounting\Filament\Clusters\Accounting\Resources\JournalItemResource;
use Webkul\Accounting\Filament\Clusters\Configuration\Resources\JournalResource;
use Webkul\Support\Traits\HasRecordNavigationTabs;

class ManageJournalEntries extends ManageRelatedRecords
{
    use HasRecordNavigationTabs;

    protected static string $resource = JournalResource::class;

    protected static string $relationship = 'moveLines';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-receipt-percent';

    public static function getNavigationLabel(): string
    {
        return __('accounting::filament/clusters/configurations/resources/journal/pages/manage-journal-entries.navigation.title');
    }

    public function table(Table $table): Table
    {
        return JournalItemResource::table($table)
            ->defaultGroup('move.name');
    }
}
