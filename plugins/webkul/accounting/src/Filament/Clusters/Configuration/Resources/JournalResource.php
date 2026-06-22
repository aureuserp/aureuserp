<?php

namespace Webkul\Accounting\Filament\Clusters\Configuration\Resources;

use Filament\Resources\Pages\Page;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Account\Filament\Resources\JournalResource as BaseJournalResource;
use Webkul\Accounting\Filament\Clusters\Configuration;
use Webkul\Accounting\Filament\Clusters\Configuration\Resources\JournalResource\Pages\CreateJournal;
use Webkul\Accounting\Filament\Clusters\Configuration\Resources\JournalResource\Pages\EditJournal;
use Webkul\Accounting\Filament\Clusters\Configuration\Resources\JournalResource\Pages\ListJournals;
use Webkul\Accounting\Filament\Clusters\Configuration\Resources\JournalResource\Pages\ManageJournalEntries;
use Webkul\Accounting\Filament\Clusters\Configuration\Resources\JournalResource\Pages\ViewJournal;
use Webkul\Accounting\Filament\Concerns\HasCompanyScope;
use Webkul\Accounting\Models\Journal;

class JournalResource extends BaseJournalResource
{
    use HasCompanyScope;

    protected static ?string $model = Journal::class;

    protected static bool $shouldRegisterNavigation = true;

    protected static ?int $navigationSort = 4;

    protected static ?string $cluster = Configuration::class;

    public static function getModelLabel(): string
    {
        return __('accounting::filament/clusters/configurations/resources/journal.model-label');
    }

    public static function getNavigationLabel(): string
    {
        return __('accounting::filament/clusters/configurations/resources/journal.navigation.title');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('accounting::filament/clusters/configurations/resources/journal.navigation.group');
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            ViewJournal::class,
            EditJournal::class,
            ManageJournalEntries::class,
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index'           => ListJournals::route('/'),
            'create'          => CreateJournal::route('/create'),
            'edit'            => EditJournal::route('/{record}/edit'),
            'view'            => ViewJournal::route('/{record}'),
            'journal-entries' => ManageJournalEntries::route('/{record}/journal-entries'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        $companyIds = static::getAccessibleCompanyIds();

        if (empty($companyIds)) {
            return $query->whereRaw('1 = 0');
        }

        return $query->whereIn('company_id', $companyIds);
    }
}
