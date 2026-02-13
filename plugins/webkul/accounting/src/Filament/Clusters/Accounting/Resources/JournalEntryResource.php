<?php

namespace Webkul\Accounting\Filament\Clusters\Accounting\Resources;

use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ExportAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Navigation\NavigationItem;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\Size;
use Filament\Support\Enums\TextSize;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\QueryBuilder;
use Filament\Tables\Filters\QueryBuilder\Constraints\DateConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\TextConstraint;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Webkul\Account\Enums\JournalType;
use Webkul\Account\Enums\MoveState;
use Webkul\Account\Enums\MoveType;
use Webkul\Account\Enums\PaymentState;
use Webkul\Account\Facades\Account as AccountFacade;
use Webkul\Account\Facades\Tax as TaxFacade;
use Webkul\Account\Filament\Resources\JournalResource;
use Webkul\Account\Models\Account;
use Webkul\Account\Models\Journal;
use Webkul\Account\Models\Move as AccountMove;
use Webkul\Account\Models\MoveLine;
use Webkul\Account\Models\Tax;
use Webkul\Account\Settings\DefaultAccountSettings;
use Webkul\Accounting\Filament\Clusters\Accounting;
use Webkul\Accounting\Filament\Clusters\Accounting\Resources\JournalEntryResource\Pages\CreateJournalEntry;
use Webkul\Accounting\Filament\Clusters\Accounting\Resources\JournalEntryResource\Pages\EditJournalEntry;
use Webkul\Accounting\Filament\Clusters\Accounting\Resources\JournalEntryResource\Pages\ListJournalEntries;
use Webkul\Accounting\Filament\Clusters\Accounting\Resources\JournalEntryResource\Pages\ViewJournalEntry;
use Webkul\Accounting\Filament\Clusters\Customers\Resources\InvoiceResource;
use Webkul\Accounting\Filament\Clusters\Customers\Resources\PaymentResource\Pages\ViewPayment as CustomerViewPayment;
use Webkul\Accounting\Filament\Clusters\Vendors\Resources\BillResource;
use Webkul\Accounting\Filament\Clusters\Vendors\Resources\PaymentResource\Pages\ViewPayment as VendorViewPayment;
use Webkul\Accounting\Filament\Exports\JournalEntryExporter;
use Webkul\Accounting\Models\JournalEntry;
use Webkul\Field\Filament\Forms\Components\ProgressStepper as FormProgressStepper;
use Webkul\Field\Filament\Infolists\Components\ProgressStepper as InfolistProgressStepper;
use Webkul\Partner\Models\Partner;
use Webkul\Support\Filament\Forms\Components\Repeater;
use Webkul\Support\Filament\Forms\Components\Repeater\TableColumn;
use Webkul\Support\Filament\Infolists\Components\RepeatableEntry;
use Webkul\Support\Filament\Infolists\Components\Repeater\TableColumn as InfolistTableColumn;
use Webkul\Support\Models\Company;
use Webkul\Support\Models\Currency;

class JournalEntryResource extends Resource
{
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
        return $schema
            ->components([
                FormProgressStepper::make('state')
                    ->hiddenLabel()
                    ->inline()
                    ->options(function ($record) {
                        $options = MoveState::options();

                        if (
                            $record
                            && $record->state != MoveState::CANCEL->value
                        ) {
                            unset($options[MoveState::CANCEL->value]);
                        }

                        if ($record == null) {
                            unset($options[MoveState::CANCEL->value]);
                        }

                        return $options;
                    })
                    ->default(MoveState::DRAFT->value)
                    ->columnSpan('full')
                    ->disabled()
                    ->live()
                    ->reactive(),

                Section::make(__('accounting::filament/clusters/accounting/resources/journal-entry.form.section.general.title'))
                    ->icon('heroicon-o-document-text')
                    ->schema([
                        Actions::make([
                            Action::make('payment_state')
                                ->icon(fn ($record) => $record->payment_state->getIcon())
                                ->color(fn ($record) => $record->payment_state->getColor())
                                ->visible(fn ($record) => in_array($record?->payment_state, [PaymentState::PAID, PaymentState::REVERSED]))
                                ->label(fn ($record) => $record->payment_state->getLabel())
                                ->size(Size::ExtraLarge->value),
                        ]),

                        Group::make()
                            ->schema([
                                Group::make()
                                    ->schema([
                                        TextInput::make('reference')
                                            ->label(__('accounting::filament/clusters/accounting/resources/journal-entry.form.section.general.fields.reference'))
                                            ->disabled(fn ($record) => in_array($record?->state, [MoveState::POSTED, MoveState::CANCEL])),
                                    ]),

                                Group::make()
                                    ->schema([
                                        DatePicker::make('date')
                                            ->label(__('accounting::filament/clusters/accounting/resources/journal-entry.form.section.general.fields.accounting-date'))
                                            ->default(now())
                                            ->native(false)
                                            ->disabled(fn ($record) => in_array($record?->state, [MoveState::POSTED, MoveState::CANCEL])),

                                        Select::make('journal_id')
                                            ->relationship(
                                                'journal',
                                                'name',
                                                modifyQueryUsing: fn (Builder $query) => $query->where('type', JournalType::GENERAL),
                                            )
                                            ->searchable()
                                            ->preload()
                                            ->required()
                                            ->label(__('accounting::filament/clusters/accounting/resources/journal-entry.form.section.general.fields.journal'))
                                            ->createOptionForm(fn ($form) => JournalResource::form($form))
                                            ->disabled(fn ($record) => in_array($record?->state, [MoveState::POSTED, MoveState::CANCEL])),
                                    ]),
                            ])->columns(2),
                    ]),

                Tabs::make()
                    ->schema([
                        Tab::make(__('accounting::filament/clusters/accounting/resources/journal-entry.form.tabs.lines.title'))
                            ->icon('heroicon-o-list-bullet')
                            ->schema([
                                static::getLineRepeater(),
                            ]),

                        Tab::make(__('accounting::filament/clusters/accounting/resources/journal-entry.form.tabs.other-information.title'))
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                Select::make('company_id')
                                    ->label(__('accounting::filament/clusters/accounting/resources/journal-entry.form.tabs.other-information.fields.company'))
                                    ->relationship('company', 'name', modifyQueryUsing: fn (Builder $query) => $query->withTrashed())
                                    ->getOptionLabelFromRecordUsing(function ($record): string {
                                        return $record->name.($record->trashed() ? ' (Deleted)' : '');
                                    })
                                    ->disableOptionWhen(function ($label) {
                                        return str_contains($label, ' (Deleted)');
                                    })
                                    ->searchable()
                                    ->preload()
                                    ->reactive()
                                    ->afterStateUpdated(fn (callable $set, $state) => $set('currency_id', Company::find($state)?->currency_id))
                                    ->default(Auth::user()->default_company_id)
                                    ->live()
                                    ->afterStateUpdated(function (Get $get, Set $set) {
                                        $company = Company::find($get('company_id'));

                                        if ($company?->currency_id) {
                                            $set('currency_id', $company->currency_id);
                                        }
                                    }),
                                Toggle::make('checked')
                                    ->inline(false)
                                    ->label(__('accounting::filament/clusters/accounting/resources/journal-entry.form.tabs.other-information.fields.checked')),
                                Select::make('fiscal_position_id')
                                    ->relationship('fiscalPosition', 'name')
                                    ->preload()
                                    ->searchable()
                                    ->label(__('accounting::filament/clusters/accounting/resources/journal-entry.form.tabs.other-information.fields.fiscal-position'))
                                    ->hintIcon('heroicon-o-question-mark-circle', tooltip: __('accounting::filament/clusters/accounting/resources/journal-entry.form.tabs.other-information.fields.fiscal-position-tooltip'))
                                    ->disabled(fn ($record) => in_array($record?->state, [MoveState::POSTED, MoveState::CANCEL])),
                            ])
                            ->columns(2),
                        Tab::make(__('accounting::filament/clusters/accounting/resources/journal-entry.form.tabs.term-and-conditions.title'))
                            ->icon('heroicon-o-clipboard-document-list')
                            ->schema([
                                RichEditor::make('narration')
                                    ->hiddenLabel(),
                            ]),
                    ]),
            ])
            ->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->reorderableColumns()
            ->columnManagerColumns(2)
            ->columns([
                TextColumn::make('invoice_date')
                    ->date()
                    ->placeholder('-')
                    ->label(__('accounting::filament/clusters/accounting/resources/journal-entry.table.columns.invoice-date'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('date')
                    ->date()
                    ->placeholder('-')
                    ->label(__('accounting::filament/clusters/accounting/resources/journal-entry.table.columns.date'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('name')
                    ->placeholder('-')
                    ->label(__('accounting::filament/clusters/accounting/resources/journal-entry.table.columns.number'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('invoice_partner_display_name')
                    ->label(__('accounting::filament/clusters/accounting/resources/journal-entry.table.columns.partner'))
                    ->placeholder('-')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('reference')
                    ->label(__('accounting::filament/clusters/accounting/resources/journal-entry.table.columns.reference'))
                    ->searchable()
                    ->placeholder('-')
                    ->sortable(),
                TextColumn::make('journal.name')
                    ->label(__('accounting::filament/clusters/accounting/resources/journal-entry.table.columns.journal'))
                    ->searchable()
                    ->placeholder('-')
                    ->sortable(),
                TextColumn::make('company.name')
                    ->label(__('accounting::filament/clusters/accounting/resources/journal-entry.table.columns.company'))
                    ->searchable()
                    ->placeholder('-')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('amount_total_in_currency_signed')
                    ->label(__('accounting::filament/clusters/accounting/resources/journal-entry.table.columns.total'))
                    ->searchable()
                    ->placeholder('-')
                    ->sortable()
                    ->summarize(Sum::make()->label(__('accounting::filament/clusters/accounting/resources/journal-entry.table.total')))
                    ->money(fn ($record) => $record->company->currency?->name),
                TextColumn::make('state')
                    ->placeholder('-')
                    ->label(__('accounting::filament/clusters/accounting/resources/journal-entry.table.columns.state'))
                    ->sortable(),
                IconColumn::make('checked')
                    ->boolean()
                    ->placeholder('-')
                    ->label(__('accounting::filament/clusters/accounting/resources/journal-entry.table.columns.checked'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->groups([
                Tables\Grouping\Group::make('invoice_partner_display_name')
                    ->label(__('accounting::filament/clusters/accounting/resources/journal-entry.table.groups.partner'))
                    ->collapsible(),
                Tables\Grouping\Group::make('journal.name')
                    ->label(__('accounting::filament/clusters/accounting/resources/journal-entry.table.groups.journal'))
                    ->collapsible(),
                Tables\Grouping\Group::make('state')
                    ->label(__('accounting::filament/clusters/accounting/resources/journal-entry.table.groups.state'))
                    ->collapsible(),
                Tables\Grouping\Group::make('paymentMethodLine.name')
                    ->label(__('accounting::filament/clusters/accounting/resources/journal-entry.table.groups.payment-method'))
                    ->collapsible(),
                Tables\Grouping\Group::make('date')
                    ->date()
                    ->label(__('accounting::filament/clusters/accounting/resources/journal-entry.table.groups.date'))
                    ->collapsible(),
                Tables\Grouping\Group::make('invoice_date')
                    ->label(__('accounting::filament/clusters/accounting/resources/journal-entry.table.groups.invoice-date'))
                    ->collapsible(),
                Tables\Grouping\Group::make('company.name')
                    ->label(__('accounting::filament/clusters/accounting/resources/journal-entry.table.groups.company'))
                    ->collapsible(),
            ])
            ->filtersFormColumns(2)
            ->filters([
                QueryBuilder::make()
                    ->constraintPickerColumns(2)
                    ->constraints([
                        TextConstraint::make('name')
                            ->label(__('accounting::filament/clusters/accounting/resources/journal-entry.table.filters.number')),
                        TextConstraint::make('invoice_origin')
                            ->label(__('accounting::filament/clusters/accounting/resources/journal-entry.table.filters.invoice-origin')),
                        TextConstraint::make('reference')
                            ->label(__('accounting::filament/clusters/accounting/resources/journal-entry.table.filters.reference')),
                        TextConstraint::make('invoice_partner_display_name')
                            ->label(__('accounting::filament/clusters/accounting/resources/journal-entry.table.filters.invoice-partner-display-name')),
                        DateConstraint::make('invoice_date')
                            ->label(__('accounting::filament/clusters/accounting/resources/journal-entry.table.filters.invoice-date')),
                        DateConstraint::make('invoice_date_due')
                            ->label(__('accounting::filament/clusters/accounting/resources/journal-entry.table.filters.invoice-due-date')),
                        DateConstraint::make('created_at')
                            ->label(__('accounting::filament/clusters/accounting/resources/journal-entry.table.filters.created-at')),
                        DateConstraint::make('updated_at')
                            ->label(__('accounting::filament/clusters/accounting/resources/journal-entry.table.filters.updated-at')),
                    ]),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make()
                        ->url(function (Model $record): string {
                            if (in_array($record->move_type, [MoveType::OUT_INVOICE, MoveType::OUT_REFUND])) {
                                return InvoiceResource::getUrl('view', ['record' => $record]);
                            }

                            if (in_array($record->move_type, [MoveType::IN_INVOICE, MoveType::IN_REFUND])) {
                                return BillResource::getUrl('view', ['record' => $record]);
                            }

                            return static::getUrl('view', ['record' => $record]);
                        }),
                    EditAction::make()
                        ->url(function (Model $record): string {
                            if (in_array($record->move_type, [MoveType::OUT_INVOICE, MoveType::OUT_REFUND])) {
                                return InvoiceResource::getUrl('edit', ['record' => $record]);
                            }

                            if (in_array($record->move_type, [MoveType::IN_INVOICE, MoveType::IN_REFUND])) {
                                return BillResource::getUrl('edit', ['record' => $record]);
                            }

                            return static::getUrl('edit', ['record' => $record]);
                        }),
                    DeleteAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('accounting::filament/clusters/accounting/resources/journal-entry.table.actions.delete.notification.title'))
                                ->body(__('accounting::filament/clusters/accounting/resources/journal-entry.table.actions.delete.notification.body'))
                        ),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('accounting::filament/clusters/accounting/resources/journal-entry.table.bulk-actions.delete.notification.title'))
                                ->body(__('accounting::filament/clusters/accounting/resources/journal-entry.table.bulk-actions.delete.notification.body'))
                        ),
                ]),
                ExportAction::make()
                    ->label(__('accounting::filament/clusters/accounting/resources/journal-entry.table.toolbar-actions.export.label'))
                    ->icon('heroicon-o-arrow-up-tray')
                    ->exporter(JournalEntryExporter::class),
            ])
            ->modifyQueryUsing(function (Builder $query) {
                $query->with('currency');
            });
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                InfolistProgressStepper::make('state')
                    ->hiddenLabel()
                    ->inline()
                    ->options(function ($record) {
                        $options = MoveState::options();

                        if ($record->state != MoveState::CANCEL->value) {
                            unset($options[MoveState::CANCEL->value]);
                        }

                        if ($record == null) {
                            unset($options[MoveState::CANCEL->value]);
                        }

                        return $options;
                    })
                    ->default(MoveState::DRAFT->value)
                    ->columnSpan('full'),
                Section::make()
                    ->schema([
                        TextEntry::make('payment_state')
                            ->badge(),
                    ])
                    ->compact()
                    ->visible(fn ($record) => in_array($record?->payment_state, [PaymentState::PAID, PaymentState::REVERSED])),

                Section::make(__('accounting::filament/clusters/accounting/resources/journal-entry.infolist.section.general.title'))
                    ->icon('heroicon-o-document-text')
                    ->schema([
                        Grid::make()
                            ->schema([
                                TextEntry::make('name')
                                    ->placeholder('-')
                                    ->label(__('accounting::filament/clusters/accounting/resources/journal-entry.infolist.section.general.entries.number'))
                                    ->icon('heroicon-o-document')
                                    ->weight('bold')
                                    ->size(TextSize::Large),
                            ])
                            ->columns(2),

                        Grid::make()
                            ->schema([
                                Grid::make()
                                    ->schema([
                                        TextEntry::make('reference')
                                            ->placeholder('-')
                                            ->label(__('accounting::filament/clusters/accounting/resources/journal-entry.infolist.section.general.entries.reference'))
                                            ->icon('heroicon-o-document-text'),
                                    ]),

                                Grid::make()
                                    ->schema([
                                        TextEntry::make('date')
                                            ->placeholder('-')
                                            ->label(__('accounting::filament/clusters/accounting/resources/journal-entry.infolist.section.general.entries.accounting-date'))
                                            ->icon('heroicon-o-calendar')
                                            ->date(),
                                        TextEntry::make('journal.name')
                                            ->placeholder('-')
                                            ->label(__('accounting::filament/clusters/accounting/resources/journal-entry.infolist.section.general.entries.journal'))
                                            ->icon('heroicon-o-book-open'),
                                    ])
                                    ->columns(1),
                            ])
                            ->columns(2),
                    ]),

                Tabs::make()
                    ->columnSpan('full')
                    ->tabs([
                        Tab::make(__('accounting::filament/clusters/accounting/resources/journal-entry.infolist.tabs.lines.title'))
                            ->icon('heroicon-o-list-bullet')
                            ->schema([
                                RepeatableEntry::make('lines')
                                    ->hiddenLabel()
                                    ->columnManager()
                                    ->live()
                                    ->table([
                                        InfolistTableColumn::make('account')
                                            ->alignCenter()
                                            ->toggleable()
                                            ->label(__('accounting::filament/clusters/accounting/resources/journal-entry.infolist.tabs.lines.repeater.entries.account')),
                                        InfolistTableColumn::make('partner')
                                            ->alignCenter()
                                            ->toggleable()
                                            ->label(__('accounting::filament/clusters/accounting/resources/journal-entry.infolist.tabs.lines.repeater.entries.partner')),
                                        InfolistTableColumn::make('name')
                                            ->alignCenter()
                                            ->toggleable()
                                            ->label(__('accounting::filament/clusters/accounting/resources/journal-entry.infolist.tabs.lines.repeater.entries.label')),
                                        InfolistTableColumn::make('currency')
                                            ->alignCenter()
                                            ->toggleable(isToggledHiddenByDefault: true)
                                            ->label(__('accounting::filament/clusters/accounting/resources/journal-entry.infolist.tabs.lines.repeater.entries.currency')),
                                        InfolistTableColumn::make('taxes')
                                            ->alignCenter()
                                            ->toggleable()
                                            ->label(__('accounting::filament/clusters/accounting/resources/journal-entry.infolist.tabs.lines.repeater.entries.taxes')),
                                        InfolistTableColumn::make('debit')
                                            ->alignCenter()
                                            ->toggleable()
                                            ->label(__('accounting::filament/clusters/accounting/resources/journal-entry.infolist.tabs.lines.repeater.entries.debit')),
                                        InfolistTableColumn::make('credit')
                                            ->alignCenter()
                                            ->toggleable()
                                            ->label(__('accounting::filament/clusters/accounting/resources/journal-entry.infolist.tabs.lines.repeater.entries.credit')),
                                    ])
                                    ->schema([
                                        TextEntry::make('account')
                                            ->placeholder('-')
                                            ->formatStateUsing(fn ($state) => $state['name'] ?? '-'),
                                        TextEntry::make('partner')
                                            ->placeholder('-')
                                            ->formatStateUsing(fn ($state) => $state ? ($state['name'] ?? '-') : '-'),
                                        TextEntry::make('name')
                                            ->placeholder('-'),
                                        TextEntry::make('currency')
                                            ->placeholder('-')
                                            ->formatStateUsing(fn ($state) => $state['name'] ?? '-'),
                                        TextEntry::make('taxes')
                                            ->badge()
                                            ->state(function ($record): array {
                                                return $record->taxes->map(fn ($tax) => [
                                                    'name' => $tax->name,
                                                ])->toArray();
                                            })
                                            ->formatStateUsing(fn ($state) => $state['name'] ?? '-')
                                            ->placeholder('-')
                                            ->weight(FontWeight::Bold),
                                        TextEntry::make('debit')
                                            ->placeholder('-')
                                            ->money(fn ($record) => $record->currency?->name),
                                        TextEntry::make('credit')
                                            ->placeholder('-')
                                            ->money(fn ($record) => $record->currency?->name),
                                    ])->columns(5),
                            ]),

                        Tab::make(__('accounting::filament/clusters/accounting/resources/journal-entry.infolist.tabs.other-information.title'))
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                Fieldset::make(__('accounting::filament/clusters/accounting/resources/journal-entry.infolist.tabs.other-information.fieldset.accounting.title'))
                                    ->schema([
                                        TextEntry::make('company.name')
                                            ->placeholder('-')
                                            ->label(__('accounting::filament/clusters/accounting/resources/journal-entry.infolist.tabs.other-information.fieldset.accounting.entries.company')),
                                        TextEntry::make('fiscalPosition.name')
                                            ->placeholder('-')
                                            ->label(__('accounting::filament/clusters/accounting/resources/journal-entry.infolist.tabs.other-information.fieldset.accounting.entries.fiscal-position')),
                                        IconEntry::make('checked')
                                            ->label(__('accounting::filament/clusters/accounting/resources/journal-entry.infolist.tabs.other-information.fieldset.accounting.entries.checked'))
                                            ->boolean(),
                                    ])
                                    ->columns(1),
                            ])
                            ->columns(1),

                        Tab::make(__('accounting::filament/clusters/accounting/resources/journal-entry.infolist.tabs.term-and-conditions.title'))
                            ->icon('heroicon-o-clipboard-document-list')
                            ->schema([
                                TextEntry::make('narration')
                                    ->placeholder('-')
                                    ->html()
                                    ->hiddenLabel(),
                            ]),
                    ]),
            ]);
    }

    public static function getLineRepeater(): Repeater
    {
        return Repeater::make('lines')
            ->relationship('lines')
            ->hiddenLabel()
            ->compact()
            ->live()
            ->label(__('accounting::filament/clusters/accounting/resources/journal-entry.form.tabs.lines.repeater.title'))
            ->addActionLabel(__('accounting::filament/clusters/accounting/resources/journal-entry.form.tabs.lines.repeater.add-item'))
            ->collapsible()
            ->defaultItems(0)
            ->deleteAction(function (Action $action) {
                $action->requiresConfirmation();
            })
            ->addable(fn ($record): bool => ! in_array($record?->state, [MoveState::POSTED, MoveState::CANCEL]))
            ->deletable(fn ($record): bool => ! in_array($record?->state, [MoveState::POSTED, MoveState::CANCEL]))
            ->reorderable(false)
            ->table([
                TableColumn::make('account_id')
                    ->label(__('accounting::filament/clusters/accounting/resources/journal-entry.form.tabs.lines.repeater.columns.account'))
                    ->width(200)
                    ->markAsRequired(),
                TableColumn::make('partner_id')
                    ->label(__('accounting::filament/clusters/accounting/resources/journal-entry.form.tabs.lines.repeater.columns.partner'))
                    ->width(160)
                    ->toggleable(),
                TableColumn::make('name')
                    ->label(__('accounting::filament/clusters/accounting/resources/journal-entry.form.tabs.lines.repeater.columns.label'))
                    ->width(160)
                    ->toggleable(),
                TableColumn::make('amount_currency')
                    ->label(__('accounting::filament/clusters/accounting/resources/journal-entry.form.tabs.lines.repeater.columns.amount-currency'))
                    ->width(160)
                    ->toggleable(isToggledHiddenByDefault: true),
                TableColumn::make('currency_id')
                    ->label(__('accounting::filament/clusters/accounting/resources/journal-entry.form.tabs.lines.repeater.columns.currency'))
                    ->width(100)
                    ->markAsRequired()
                    ->toggleable(isToggledHiddenByDefault: true),
                TableColumn::make('taxes')
                    ->label(__('accounting::filament/clusters/accounting/resources/journal-entry.form.tabs.lines.repeater.columns.taxes'))
                    ->width(150)
                    ->toggleable(),
                TableColumn::make('debit')
                    ->label(__('accounting::filament/clusters/accounting/resources/journal-entry.form.tabs.lines.repeater.columns.debit'))
                    ->width(100)
                    ->summarize(\Webkul\Support\Filament\Summarizers\Sum::make())
                    ->markAsRequired(),
                TableColumn::make('credit')
                    ->label(__('accounting::filament/clusters/accounting/resources/journal-entry.form.tabs.lines.repeater.columns.credit'))
                    ->width(100)
                    ->summarize(\Webkul\Support\Filament\Summarizers\Sum::make())
                    ->markAsRequired(),
                TableColumn::make('discount_amount_currency')
                    ->label(__('accounting::filament/clusters/accounting/resources/journal-entry.form.tabs.lines.repeater.columns.discount-amount-currency'))
                    ->width(200)
                    ->wrapHeader()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->schema([
                Hidden::make('display_type'),
                Select::make('account_id')
                    ->label(__('accounting::filament/clusters/accounting/resources/journal-entry.form.tabs.lines.repeater.fields.account'))
                    ->relationship('account', 'name')
                    ->searchable()
                    ->required()
                    ->preload()
                    ->live()
                    ->selectablePlaceholder(false)
                    ->dehydrated()
                    ->disabled(fn ($record) => in_array($record?->parent_state, [MoveState::POSTED, MoveState::CANCEL])),
                Select::make('partner_id')
                    ->label(__('accounting::filament/clusters/accounting/resources/journal-entry.form.tabs.lines.repeater.fields.partner'))
                    ->relationship('partner', 'name')
                    ->searchable()
                    ->preload()
                    ->selectablePlaceholder(false)
                    ->dehydrated()
                    ->disabled(fn ($record) => in_array($record?->parent_state, [MoveState::POSTED, MoveState::CANCEL])),
                TextInput::make('name')
                    ->label(__('accounting::filament/clusters/accounting/resources/journal-entry.form.tabs.lines.repeater.fields.label'))
                    ->dehydrated()
                    ->disabled(fn ($record) => in_array($record?->parent_state, [MoveState::POSTED, MoveState::CANCEL])),
                TextInput::make('amount_currency')
                    ->label(__('accounting::filament/clusters/accounting/resources/journal-entry.form.tabs.lines.repeater.fields.amount-currency'))
                    ->numeric()
                    ->default(0)
                    ->maxValue(99999999999)
                    ->live(onBlur: true)
                    ->dehydrated()
                    ->afterStateUpdated(fn (Set $set, Get $get) => self::amountCurrencyUpdated($set, $get))
                    ->disabled(fn ($record) => in_array($record?->parent_state, [MoveState::POSTED, MoveState::CANCEL])),
                Select::make('currency_id')
                    ->label(__('accounting::filament/clusters/accounting/resources/journal-entry.form.tabs.lines.repeater.fields.currency'))
                    ->relationship(
                        'currency',
                        'name',
                        modifyQueryUsing: fn (Builder $query) => $query->where('active', 1),
                    )
                    ->default(Auth::user()->defaultCompany?->currency_id)
                    ->required()
                    ->live()
                    ->selectablePlaceholder(false)
                    ->dehydrated()
                    ->afterStateUpdated(fn (Set $set, Get $get) => self::currencyUpdated($set, $get))
                    ->disabled(fn ($record) => in_array($record?->parent_state, [MoveState::POSTED, MoveState::CANCEL])),
                Select::make('taxes')
                    ->label(__('accounting::filament/clusters/accounting/resources/journal-entry.form.tabs.lines.repeater.fields.taxes'))
                    ->relationship('taxes', 'name')
                    ->getOptionLabelFromRecordUsing(function ($record): string {
                        return $record->name.' ('.$record->type_tax_use->getLabel().')';
                    })
                    ->searchable()
                    ->multiple()
                    ->preload()
                    ->dehydrated()
                    ->live()
                    ->afterStateUpdated(fn (Get $get, Set $set) => self::taxesUpdated($set, $get))
                    ->disabled(fn ($record) => in_array($record?->parent_state, [MoveState::POSTED, MoveState::CANCEL])),
                TextInput::make('debit')
                    ->label(__('accounting::filament/clusters/accounting/resources/journal-entry.form.tabs.lines.repeater.fields.debit'))
                    ->required()
                    ->numeric()
                    ->default(0)
                    ->minValue(0)
                    ->maxValue(99999999999)
                    ->live(onBlur: true)
                    ->dehydrated()
                    ->disabled(fn ($record) => in_array($record?->parent_state, [MoveState::POSTED, MoveState::CANCEL]))
                    ->afterStateUpdated(fn (Set $set, Get $get) => self::debitUpdated($set, $get)),
                TextInput::make('credit')
                    ->label(__('accounting::filament/clusters/accounting/resources/journal-entry.form.tabs.lines.repeater.fields.credit'))
                    ->required()
                    ->numeric()
                    ->default(0)
                    ->minValue(0)
                    ->maxValue(99999999999)
                    ->live(onBlur: true)
                    ->dehydrated()
                    ->disabled(fn ($record) => in_array($record?->parent_state, [MoveState::POSTED, MoveState::CANCEL]))
                    ->afterStateUpdated(fn (Set $set, Get $get) => self::creditUpdated($set, $get)),
                TextInput::make('discount_amount_currency')
                    ->label(__('accounting::filament/clusters/accounting/resources/journal-entry.form.tabs.lines.repeater.fields.discount-amount-currency'))
                    ->numeric()
                    ->default(0)
                    ->minValue(0)
                    ->maxValue(99999999999)
                    ->live(onBlur: true)
                    ->dehydrated()
                    ->disabled(fn ($record) => in_array($record?->parent_state, [MoveState::POSTED, MoveState::CANCEL]))
                    ->afterStateUpdated(fn (Set $set, Get $get) => self::discountAmountCurrencyUpdated($set, $get)),
            ]);
    }

    private static function currencyUpdated(Set $set, Get $get): void {}

    private static function taxesUpdated(Set $set, Get $get): void
    {
        self::recalculateJournalTaxLines($get, $set);
    }

    private static function amountCurrencyUpdated(Set $set, Get $get): void
    {
        if ($get('amount_currency') >= 0) {
            $set('debit', $get('amount_currency'));
            $set('credit', 0);
        } else {
            $set('debit', 0);
            $set('credit', abs($get('amount_currency')));
        }

        self::recalculateJournalTaxLines($get, $set);
    }

    private static function debitUpdated(Set $set, Get $get): void
    {
        $set('credit', 0);

        $set('amount_currency', $get('debit'));

        self::recalculateJournalTaxLines($get, $set);
    }

    private static function creditUpdated(Set $set, Get $get): void
    {
        $set('debit', 0);

        $set('amount_currency', -1 * $get('credit'));

        self::recalculateJournalTaxLines($get, $set);
    }

    private static function discountAmountCurrencyUpdated(Set $set, Get $get): void {}

    private static function recalculateJournalTaxLines(Get $get, Set $set): void
    {
        $lines = $get('../../lines') ?? [];

        if (empty($lines)) {
            return;
        }

        $currencyId = $get('currency_id');
        $companyId = $get('../../company_id');
        $journalId = $get('../../journal_id');

        if (! $currencyId || ! $companyId) {
            return;
        }

        $currency = Currency::find($currencyId);
        $company = Company::find($companyId);

        if (! $currency || ! $company) {
            return;
        }

        $journal = $journalId ? Journal::find($journalId) : null;
        $suspenseAccountId = $journal?->suspense_account_id ?? (new DefaultAccountSettings)->account_journal_suspense_account_id;

        $lines = collect($lines)
            ->reject(function ($line) {
                $name = $line['name'] ?? '';

                return str_starts_with($name, 'Tax:') || $name === 'Automatic Balancing';
            })
            ->values()
            ->all();

        if (empty($lines)) {
            $set('../../lines', []);

            return;
        }

        $mockMove = new AccountMove([
            'currency_id' => $currency->id,
            'company_id'  => $company->id,
            'journal_id'  => $journalId,
        ]);

        $mockMove->setRelation('currency', $currency);
        $mockMove->setRelation('company', $company);

        $totalDebit = collect($lines)->sum(fn ($lineData) => (float) ($lineData['debit'] ?? 0));
        $totalCredit = collect($lines)->sum(fn ($lineData) => (float) ($lineData['credit'] ?? 0));

        $baseLines = [];

        foreach ($lines as $lineData) {
            if (empty($lineData['taxes'])) {
                continue;
            }

            $mockLine = self::createMockMoveLine($lineData, $mockMove, $currency, $company);

            $baseLine = AccountFacade::prepareProductBaseLineForTaxesComputation($mockLine);

            $baseLine = TaxFacade::addTaxDetailsInBaseLine($baseLine, $company);

            $baseLine = TaxFacade::addAccountingDataToBaseLineTaxDetails($baseLine, $company);

            $baseLines[] = $baseLine;
        }

        $taxLinesMap = [];

        foreach ($baseLines as $baseLine) {
            $taxesData = $baseLine['tax_details']['taxes_data'] ?? [];

            foreach ($taxesData as $taxData) {
                if (! isset($taxData['tax_reps_data']) || empty($taxData['tax_reps_data'])) {
                    continue;
                }

                foreach ($taxData['tax_reps_data'] as $taxRepData) {
                    $accountId = $taxRepData['account']?->id ?? null;

                    if (! $accountId) {
                        continue;
                    }

                    $partnerId = $baseLine['partner']->id ?? null;

                    $key = $accountId.'_'.($partnerId ?? 'null');

                    if (! isset($taxLinesMap[$key])) {
                        $taxName = $taxData['tax']->name ?? 'Tax';

                        $taxLinesMap[$key] = [
                            'display_type'             => 'tax',
                            'account_id'               => $accountId,
                            'partner_id'               => $partnerId,
                            'name'                     => 'Tax: '.$taxName,
                            'amount_currency'          => 0,
                            'currency_id'              => $currency->id,
                            'taxes'                    => [],
                            'debit'                    => 0,
                            'credit'                   => 0,
                            'discount_amount_currency' => 0,
                        ];
                    }

                    $taxAmount = $taxRepData['tax_amount'] ?? $taxRepData['tax_amount_currency'] ?? 0;

                    $taxLinesMap[$key]['amount_currency'] += $taxAmount;

                    if ($taxAmount < 0) {
                        $taxLinesMap[$key]['credit'] += abs($taxAmount);
                    } else {
                        $taxLinesMap[$key]['debit'] += abs($taxAmount);
                    }
                }
            }
        }

        $lines = array_values($lines);

        foreach ($taxLinesMap as $key => $taxLine) {
            $taxLine['debit'] = round($taxLine['debit'], 2);
            $taxLine['credit'] = round($taxLine['credit'], 2);
            $taxLine['amount_currency'] = round($taxLine['amount_currency'], 2);

            $taxLinesMap[$key] = $taxLine;

            $lines[] = $taxLine;

            $totalDebit += $taxLine['debit'];
            $totalCredit += $taxLine['credit'];
        }

        $balancingLine = self::calculateBalancingLine($totalDebit, $totalCredit, $company, $suspenseAccountId);

        if ($balancingLine) {
            $lines[] = $balancingLine;
        }

        $set('../../lines', $lines);
    }

    private static function createMockMoveLine(array $lineData, AccountMove $mockMove, Currency $currency, Company $company): MoveLine
    {
        $partner = isset($lineData['partner_id']) ? Partner::find($lineData['partner_id']) : null;
        $account = isset($lineData['account_id']) ? Account::find($lineData['account_id']) : null;

        $amountCurrency = ($lineData['debit'] ?? 0) - ($lineData['credit'] ?? 0);

        $mockLine = new MoveLine([
            'amount_currency' => $amountCurrency,
            'balance'         => $amountCurrency,
            'name'            => $lineData['name'] ?? '',
        ]);

        $taxIds = $lineData['taxes'] ?? [];
        $mockLine->setRelation('taxes', Tax::whereIn('id', $taxIds)->get());
        $mockLine->setRelation('currency', $currency);
        $mockLine->setRelation('company', $company);
        $mockLine->setRelation('move', $mockMove);

        if ($partner) {
            $mockLine->setRelation('partner', $partner);
        }

        if ($account) {
            $mockLine->setRelation('account', $account);
        }

        return $mockLine;
    }

    private static function recalculateBalancingLineOnly(Get $get, Set $set): void
    {
        $lines = $get('../../lines') ?? [];

        if (empty($lines)) {
            return;
        }

        $companyId = $get('../../company_id');
        $journalId = $get('../../journal_id');

        if (! $companyId) {
            return;
        }

        $company = Company::find($companyId);

        if (! $company) {
            return;
        }

        $journal = $journalId ? Journal::find($journalId) : null;

        $suspenseAccountId = $journal?->suspense_account_id ?? (new DefaultAccountSettings)->account_journal_suspense_account_id;

        $linesWithoutBalancing = collect($lines)
            ->reject(fn ($line) => ($line['is_auto_generated'] ?? false) && ($line['auto_type'] ?? null) === 'balancing')
            ->values()
            ->all();

        $totalDebit = collect($linesWithoutBalancing)->sum(fn ($lineData) => data_get($lineData, 'debit', 0));
        $totalCredit = collect($linesWithoutBalancing)->sum(fn ($lineData) => data_get($lineData, 'credit', 0));

        $balancingLine = self::calculateBalancingLine($totalDebit, $totalCredit, $company, $suspenseAccountId);

        if ($balancingLine) {
            $linesWithoutBalancing[] = $balancingLine;
        }

        $set('../../lines', $linesWithoutBalancing);
    }

    private static function calculateBalancingLine(float $totalDebit, float $totalCredit, Company $company, ?int $accountId = null): ?array
    {
        $difference = round($totalDebit - $totalCredit, 2);

        if (abs($difference) < 0.01) {
            return null;
        }

        if (! $accountId) {
            return null;
        }

        return [
            'account_id'               => $accountId,
            'partner_id'               => null,
            'name'                     => 'Automatic Balancing',
            'amount_currency'          => -$difference,
            'currency_id'              => $company->currency_id,
            'taxes'                    => [],
            'debit'                    => $difference < 0 ? abs($difference) : 0,
            'credit'                   => $difference > 0 ? $difference : 0,
            'discount_amount_currency' => 0,
            'is_auto_generated'        => true,
            'auto_type'                => 'balancing',
        ];
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
