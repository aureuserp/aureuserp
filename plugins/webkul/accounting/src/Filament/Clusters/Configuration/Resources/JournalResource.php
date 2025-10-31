<?php

namespace Webkul\Accounting\Filament\Clusters\Configuration\Resources;

use Webkul\Accounting\Filament\Clusters\Configuration;
use Webkul\Accounting\Filament\Clusters\Configuration\Resources\JournalResource\Pages\ListJournals;
use Webkul\Accounting\Filament\Clusters\Configuration\Resources\JournalResource\Pages\CreateJournal;
use Webkul\Accounting\Filament\Clusters\Configuration\Resources\JournalResource\Pages\EditJournal;
use Webkul\Accounting\Filament\Clusters\Configuration\Resources\JournalResource\Pages\ViewJournal;
use Webkul\Account\Filament\Resources\JournalResource as BaseJournalResource;

class JournalResource extends BaseJournalResource
{
    protected static bool $shouldRegisterNavigation = true;

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

    public static function getPages(): array
    {
        return [
            'index' => ListJournals::route('/'),
            'create' => CreateJournal::route('/create'),
            'edit' => EditJournal::route('/{record}/edit'),
            'view' => ViewJournal::route('/{record}'),
        ];
    }
}
