<?php

namespace Webkul\Accounting\Filament\Clusters\Accounting\Resources;

use Filament\Navigation\NavigationItem;
use Filament\Resources\Pages\Page;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Webkul\Accounting\Filament\Clusters\Accounting;
use Webkul\Accounting\Filament\Clusters\Accounting\Resources\JournalEntryResource\Pages\CreateJournalEntry;
use Webkul\Accounting\Filament\Clusters\Accounting\Resources\JournalEntryResource\Pages\EditJournalEntry;
use Webkul\Accounting\Filament\Clusters\Accounting\Resources\JournalEntryResource\Pages\ListJournalEntries;
use Webkul\Accounting\Filament\Clusters\Accounting\Resources\JournalEntryResource\Pages\ViewJournalEntry;
use Webkul\Accounting\Filament\Clusters\Accounting\Resources\JournalEntryResource\Schemas\JournalEntryForm;
use Webkul\Accounting\Filament\Clusters\Accounting\Resources\JournalEntryResource\Schemas\JournalEntryInfolist;
use Webkul\Accounting\Filament\Clusters\Accounting\Resources\JournalEntryResource\Tables\JournalEntriesTable;
use Webkul\Accounting\Filament\Clusters\Customers\Resources\PaymentResource\Pages\ViewPayment as CustomerViewPayment;
use Webkul\Accounting\Filament\Clusters\Vendors\Resources\PaymentResource\Pages\ViewPayment as VendorViewPayment;
use Webkul\Accounting\Models\JournalEntry;
use Webkul\Field\Filament\Traits\HasCustomFields;

class JournalEntryResource extends Resource
{
    use HasCustomFields;

    protected static ?string $model = JournalEntry::class;

    protected static bool $shouldRegisterNavigation = true;

    protected static ?string $cluster = Accounting::class;

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'name';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-receipt-percent';

    public static function getModelLabel(): string
    {
        return __('accounting::filament/clusters/accounting/resources/journal-entry.title');
    }

    public static function getNavigationLabel(): string
    {
        return __('accounting::filament/clusters/accounting/resources/journal-entry.navigation.title');
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            __('accounting::filament/clusters/accounting/resources/journal-entry.global-search.partner')  => $record?->partner?->name ?? '—',
            __('accounting::filament/clusters/accounting/resources/journal-entry.global-search.date')     => $record?->invoice_date ?? '—',
            __('accounting::filament/clusters/accounting/resources/journal-entry.global-search.due-date') => $record?->invoice_date_due ?? '—',
        ];
    }

    public static function form(Schema $schema): Schema
    {
        return JournalEntryForm::configure($schema, static::getCustomFormFields());
    }

    public static function table(Table $table): Table
    {
        return JournalEntriesTable::configure($table, static::class, static::getCustomTableColumns(), static::getCustomTableFilters());
    }

    public static function infolist(Schema $schema): Schema
    {
        return JournalEntryInfolist::configure($schema, static::getCustomInfolistEntries());
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        $navigationItems = $page->generateNavigationItems([
            ViewJournalEntry::class,
            EditJournalEntry::class,
        ]);

        if ($payment = $page->getRecord()?->originPayment) {
            $navigationItems[] = NavigationItem::make(__('accounting::filament/clusters/accounting/resources/journal-entry.record-sub-navigation.payment'))
                ->icon('heroicon-o-banknotes')
                ->url(function () use ($payment) {
                    if ($payment->partner_type === 'customer') {
                        return CustomerViewPayment::getUrl(['record' => $payment->id]);
                    } else {
                        return VendorViewPayment::getUrl(['record' => $payment->id]);
                    }
                });
        }

        return $navigationItems;
    }

    public static function getPages(): array
    {
        return [
            'index'    => ListJournalEntries::route('/'),
            'create'   => CreateJournalEntry::route('/create'),
            'view'     => ViewJournalEntry::route('/{record}'),
            'edit'     => EditJournalEntry::route('/{record}/edit'),
        ];
    }
}
