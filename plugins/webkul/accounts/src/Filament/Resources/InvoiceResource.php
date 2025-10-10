<?php

namespace Webkul\Account\Filament\Resources;

use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Livewire;
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
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Webkul\Account\Enums\MoveState;
use Webkul\Account\Enums\PaymentState;
use Webkul\Account\Enums\TypeTaxUse;
use Webkul\Account\Facades\Tax;
use Webkul\Account\Filament\Resources\InvoiceResource\Pages\CreateInvoice;
use Webkul\Account\Filament\Resources\InvoiceResource\Pages\EditInvoice;
use Webkul\Account\Filament\Resources\InvoiceResource\Pages\ListInvoices;
use Webkul\Account\Filament\Resources\InvoiceResource\Pages\ViewInvoice;
use Webkul\Account\Livewire\InvoiceSummary;
use Webkul\Account\Models\Move as AccountMove;
use Webkul\Field\Filament\Forms\Components\ProgressStepper;
use Webkul\Invoice\Models\Product;
use Webkul\Invoice\Settings\ProductSettings;
use Webkul\Support\Filament\Forms\Components\Repeater;
use Webkul\Support\Filament\Forms\Components\Repeater\TableColumn;
use Webkul\Support\Models\Company;
use Webkul\Support\Models\Currency;
use Webkul\Support\Models\UOM;

class InvoiceResource extends Resource
{
    protected static ?string $model = AccountMove::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-receipt-percent';

    protected static bool $shouldRegisterNavigation = false;

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            __('accounts::filament/resources/invoice.global-search.number')           => $record?->name ?? '—',
            __('accounts::filament/resources/invoice.global-search.customer')         => $record?->invoice_partner_display_name ?? '—',
            __('accounts::filament/resources/invoice.global-search.invoice-date')     => $record?->invoice_date ?? '—',
            __('accounts::filament/resources/invoice.global-search.invoice-date-due') => $record?->invoice_date_due ?? '—',
        ];
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                ProgressStepper::make('state')
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
                Section::make(__('accounts::filament/resources/invoice.form.section.general.title'))
                    ->icon('heroicon-o-document-text')
                    ->schema([
                        Actions::make([
                            Action::make('payment_state')
                                ->icon(fn ($record) => $record->payment_state->getIcon())
                                ->color(fn ($record) => $record->payment_state->getColor())
                                ->visible(fn ($record) => $record && in_array($record->payment_state, [PaymentState::PAID, PaymentState::REVERSED]))
                                ->label(fn ($record) => $record->payment_state->getLabel())
                                ->size(Size::ExtraLarge->value),
                        ]),
                        Group::make()
                            ->schema([
                                Group::make()
                                    ->schema([
                                        Select::make('partner_id')
                                            ->label(__('accounts::filament/resources/invoice.form.section.general.fields.customer'))
                                            ->relationship(
                                                'partner',
                                                'name',
                                                fn ($query) => $query->where('sub_type', 'customer')->orderBy('id'),
                                            )
                                            ->searchable()
                                            ->preload()
                                            ->live()
                                            ->disabled(fn ($record) => $record && in_array($record->state, [MoveState::POSTED, MoveState::CANCEL])),
                                    ]),
                                DatePicker::make('invoice_date')
                                    ->label(__('accounts::filament/resources/invoice.form.section.general.fields.invoice-date'))
                                    ->default(now())
                                    ->native(false)
                                    ->disabled(fn ($record) => $record && in_array($record->state, [MoveState::POSTED, MoveState::CANCEL])),
                                DatePicker::make('invoice_date_due')
                                    ->required()
                                    ->default(now())
                                    ->native(false)
                                    ->live()
                                    ->hidden(fn (Get $get) => $get('invoice_payment_term_id') !== null)
                                    ->label(__('accounts::filament/resources/invoice.form.section.general.fields.due-date')),
                                Select::make('invoice_payment_term_id')
                                    ->relationship(
                                        'invoicePaymentTerm',
                                        'name',
                                        modifyQueryUsing: fn (Builder $query) => $query->withTrashed(),
                                    )
                                    ->getOptionLabelFromRecordUsing(function ($record): string {
                                        return $record->name.($record->trashed() ? ' (Deleted)' : '');
                                    })
                                    ->disableOptionWhen(function ($label) {
                                        return str_contains($label, ' (Deleted)');
                                    })
                                    ->required(fn (Get $get) => $get('invoice_date_due') === null)
                                    ->live()
                                    ->searchable()
                                    ->preload()
                                    ->label(__('accounts::filament/resources/invoice.form.section.general.fields.payment-term')),
                            ])->columns(2),
                    ]),
                Tabs::make()
                    ->schema([
                        Tab::make(__('accounts::filament/resources/invoice.form.tabs.invoice-lines.title'))
                            ->icon('heroicon-o-list-bullet')
                            ->schema([
                                static::getProductRepeater(),
                                Livewire::make(InvoiceSummary::class, function (Get $get) {
                                    return [
                                        'currency' => Currency::find($get('currency_id')),
                                        'products' => $get('products'),
                                    ];
                                })
                                    ->visible(fn (Get $get) => $get('currency_id') && $get('products'))
                                    ->live()
                                    ->reactive(),
                            ]),
                        Tab::make(__('accounts::filament/resources/invoice.form.tabs.other-information.title'))
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                Fieldset::make(__('accounts::filament/resources/invoice.form.tabs.other-information.fieldset.invoice.title'))
                                    ->schema([
                                        TextInput::make('reference')
                                            ->label(__('accounts::filament/resources/invoice.form.tabs.other-information.fieldset.invoice.fields.customer-reference'))
                                            ->maxLength(255),
                                        Select::make('invoice_user_id')
                                            ->relationship('invoiceUser', 'name')
                                            ->searchable()
                                            ->preload()
                                            ->label(__('accounts::filament/resources/invoice.form.tabs.other-information.fieldset.invoice.fields.sales-person')),
                                        Select::make('partner_bank_id')
                                            ->relationship('partnerBank', 'account_number')
                                            ->searchable()
                                            ->preload()
                                            ->label(__('accounts::filament/resources/invoice.form.tabs.other-information.fieldset.invoice.fields.recipient-bank'))
                                            ->createOptionForm(fn ($form) => BankAccountResource::form($form))
                                            ->disabled(fn ($record) => $record && in_array($record->state, [MoveState::POSTED, MoveState::CANCEL])),
                                        TextInput::make('payment_reference')
                                            ->label(__('accounts::filament/resources/invoice.form.tabs.other-information.fieldset.invoice.fields.payment-reference')),
                                        DatePicker::make('delivery_date')
                                            ->native(false)
                                            ->label(__('accounts::filament/resources/invoice.form.tabs.other-information.fieldset.invoice.fields.delivery-date'))
                                            ->disabled(fn ($record) => $record && in_array($record->state, [MoveState::POSTED, MoveState::CANCEL])),
                                    ]),
                                Fieldset::make(__('accounts::filament/resources/invoice.form.tabs.other-information.fieldset.accounting.title'))
                                    ->schema([
                                        Select::make('invoice_incoterm_id')
                                            ->relationship('invoiceIncoterm', 'name')
                                            ->searchable()
                                            ->preload()
                                            ->label(__('accounts::filament/resources/invoice.form.tabs.other-information.fieldset.accounting.fieldset.incoterm')),
                                        TextInput::make('incoterm_location')
                                            ->label(__('accounts::filament/resources/invoice.form.tabs.other-information.fieldset.accounting.fieldset.incoterm-location')),
                                        Select::make('preferred_payment_method_line_id')
                                            ->relationship('paymentMethodLine', 'name')
                                            ->preload()
                                            ->searchable()
                                            ->label(__('accounts::filament/resources/invoice.form.tabs.other-information.fieldset.accounting.fieldset.payment-method')),
                                        Toggle::make('auto_post')
                                            ->default(0)
                                            ->inline(false)
                                            ->label(__('accounts::filament/resources/invoice.form.tabs.other-information.fieldset.accounting.fieldset.auto-post'))
                                            ->disabled(fn ($record) => $record && in_array($record->state, [MoveState::POSTED, MoveState::CANCEL])),
                                        Toggle::make('checked')
                                            ->inline(false)
                                            ->label(__('accounts::filament/resources/invoice.form.tabs.other-information.fieldset.accounting.fieldset.checked')),
                                    ]),
                                Fieldset::make(__('accounts::filament/resources/invoice.form.tabs.other-information.fieldset.additional-information.title'))
                                    ->schema([
                                        Select::make('company_id')
                                            ->label(__('accounts::filament/resources/invoice.form.tabs.other-information.fieldset.additional-information.fields.company'))
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
                                        Select::make('currency_id')
                                            ->label(__('accounts::filament/resources/invoice.form.tabs.other-information.fieldset.additional-information.fields.currency'))
                                            ->relationship('currency', 'name')
                                            ->required()
                                            ->searchable()
                                            ->preload()
                                            ->live()
                                            ->reactive()
                                            ->default(Auth::user()->defaultCompany?->currency_id),
                                    ]),
                                Fieldset::make(__('accounts::filament/resources/invoice.form.tabs.other-information.fieldset.marketing.title'))
                                    ->schema([
                                        Select::make('campaign_id')
                                            ->relationship('campaign', 'name')
                                            ->searchable()
                                            ->preload()
                                            ->label(__('accounts::filament/resources/invoice.form.tabs.other-information.fieldset.marketing.fields.campaign')),
                                        Select::make('medium_id')
                                            ->relationship('medium', 'name')
                                            ->searchable()
                                            ->preload()
                                            ->label(__('accounts::filament/resources/invoice.form.tabs.other-information.fieldset.marketing.fields.medium')),
                                        Select::make('source_id')
                                            ->relationship('source', 'name')
                                            ->searchable()
                                            ->preload()
                                            ->label(__('accounts::filament/resources/invoice.form.tabs.other-information.fieldset.marketing.fields.source')),
                                    ]),
                            ]),
                        Tab::make(__('accounts::filament/resources/invoice.form.tabs.term-and-conditions.title'))
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
                TextColumn::make('name')
                    ->placeholder('-')
                    ->label(__('accounts::filament/resources/invoice.table.columns.number'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('state')
                    ->placeholder('-')
                    ->label(__('accounts::filament/resources/invoice.table.columns.state'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('invoice_partner_display_name')
                    ->label(__('accounts::filament/resources/invoice.table.columns.customer'))
                    ->placeholder('-')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('invoice_date')
                    ->date()
                    ->placeholder('-')
                    ->label(__('accounts::filament/resources/invoice.table.columns.invoice-date'))
                    ->sortable(),
                TextColumn::make('invoice_date_due')
                    ->date()
                    ->placeholder('-')
                    ->label(__('accounts::filament/resources/invoice.table.columns.due-date'))
                    ->sortable(),
                TextColumn::make('amount_untaxed_in_currency_signed')
                    ->label(__('accounts::filament/resources/invoice.table.columns.tax-excluded'))
                    ->searchable()
                    ->placeholder('-')
                    ->sortable()
                    ->money(fn ($record) => $record->currency?->name)
                    ->summarize(Sum::make()->label(__('accounts::filament/resources/invoice.table.total')))
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('amount_tax_signed')
                    ->label(__('accounts::filament/resources/invoice.table.columns.tax'))
                    ->searchable()
                    ->placeholder('-')
                    ->sortable()
                    ->money(fn ($record) => $record->currency?->name)
                    ->summarize(Sum::make()->label(__('accounts::filament/resources/invoice.table.total')))
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('amount_total_in_currency_signed')
                    ->label(__('accounts::filament/resources/invoice.table.columns.total'))
                    ->searchable()
                    ->placeholder('-')
                    ->sortable()
                    ->summarize(Sum::make()->label(__('accounts::filament/resources/invoice.table.total')))
                    ->money(fn ($record) => $record->currency?->name)
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('amount_residual_signed')
                    ->label(__('accounts::filament/resources/invoice.table.columns.amount-due'))
                    ->searchable()
                    ->placeholder('-')
                    ->sortable()
                    ->summarize(Sum::make()->label('Total'))
                    ->money(fn ($record) => $record->currency?->name)
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('payment_state')
                    ->label(__('Payment State'))
                    ->placeholder('-')
                    ->color(fn (PaymentState $state) => $state->getColor())
                    ->icon(fn (PaymentState $state) => $state->getIcon())
                    ->formatStateUsing(fn (PaymentState $state) => $state->getLabel())
                    ->badge()
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                IconColumn::make('checked')
                    ->boolean()
                    ->placeholder('-')
                    ->label(__('accounts::filament/resources/invoice.table.columns.checked'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('date')
                    ->date()
                    ->placeholder('-')
                    ->label(__('accounts::filament/resources/invoice.table.columns.accounting-date'))
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('invoice_origin')
                    ->placeholder('-')
                    ->label(__('accounts::filament/resources/invoice.table.columns.source-document'))
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('reference')
                    ->label(__('accounts::filament/resources/invoice.table.columns.reference'))
                    ->searchable()
                    ->placeholder('-')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('invoiceUser.name')
                    ->label(__('accounts::filament/resources/invoice.table.columns.sales-person'))
                    ->searchable()
                    ->placeholder('-')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('currency.name')
                    ->label(__('accounts::filament/resources/invoice.table.columns.invoice-currency'))
                    ->searchable()
                    ->placeholder('-')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->groups([
                Tables\Grouping\Group::make('name')
                    ->label(__('accounts::filament/resources/invoice.table.groups.name'))
                    ->collapsible(),
                Tables\Grouping\Group::make('invoice_partner_display_name')
                    ->label(__('accounts::filament/resources/invoice.table.groups.invoice-partner-display-name'))
                    ->collapsible(),
                Tables\Grouping\Group::make('invoice_date')
                    ->label(__('accounts::filament/resources/invoice.table.groups.invoice-date'))
                    ->collapsible(),
                Tables\Grouping\Group::make('checked')
                    ->label(__('accounts::filament/resources/invoice.table.groups.checked'))
                    ->collapsible(),
                Tables\Grouping\Group::make('date')
                    ->date()
                    ->label(__('accounts::filament/resources/invoice.table.groups.date'))
                    ->collapsible(),
                Tables\Grouping\Group::make('invoice_date_due')
                    ->date()
                    ->label(__('accounts::filament/resources/invoice.table.groups.invoice-due-date'))
                    ->collapsible(),
                Tables\Grouping\Group::make('invoice_origin')
                    ->label(__('accounts::filament/resources/invoice.table.groups.invoice-origin'))
                    ->collapsible(),
                Tables\Grouping\Group::make('invoiceUser.name')
                    ->date()
                    ->label(__('accounts::filament/resources/invoice.table.groups.sales-person'))
                    ->collapsible(),
                Tables\Grouping\Group::make('currency.name')
                    ->label(__('accounts::filament/resources/invoice.table.groups.currency'))
                    ->collapsible(),
                Tables\Grouping\Group::make('created_at')
                    ->label(__('accounts::filament/resources/invoice.table.groups.created-at'))
                    ->date()
                    ->collapsible(),
                Tables\Grouping\Group::make('updated_at')
                    ->label(__('accounts::filament/resources/invoice.table.groups.updated-at'))
                    ->date()
                    ->collapsible(),
            ])
            ->filtersFormColumns(2)
            ->filters([
                QueryBuilder::make()
                    ->constraintPickerColumns(2)
                    ->constraints([
                        TextConstraint::make('name')
                            ->label(__('accounts::filament/resources/invoice.table.filters.number')),
                        TextConstraint::make('invoice_origin')
                            ->label(__('accounts::filament/resources/invoice.table.filters.invoice-origin')),
                        TextConstraint::make('reference')
                            ->label(__('accounts::filament/resources/invoice.table.filters.reference')),
                        TextConstraint::make('invoice_partner_display_name')
                            ->label(__('accounts::filament/resources/invoice.table.filters.invoice-partner-display-name')),
                        DateConstraint::make('invoice_date')
                            ->label(__('accounts::filament/resources/invoice.table.filters.invoice-date')),
                        DateConstraint::make('invoice_date_due')
                            ->label(__('accounts::filament/resources/invoice.table.filters.invoice-due-date')),
                        DateConstraint::make('created_at')
                            ->label(__('accounts::filament/resources/invoice.table.filters.created-at')),
                        DateConstraint::make('updated_at')
                            ->label(__('accounts::filament/resources/invoice.table.filters.updated-at')),
                    ]),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    DeleteAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('accounts::filament/resources/invoice.table.actions.delete.notification.title'))
                                ->body(__('accounts::filament/resources/invoice.table.actions.delete.notification.body'))
                        ),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('accounts::filament/resources/invoice.table.bulk-actions.delete.notification.title'))
                                ->body(__('accounts::filament/resources/invoice.table.bulk-actions.delete.notification.body'))
                        ),
                ]),
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
                Section::make()
                    ->schema([
                        TextEntry::make('payment_state')
                            ->badge(),
                    ])
                    ->compact(),
                Section::make(__('accounts::filament/resources/invoice.infolist.section.general.title'))
                    ->icon('heroicon-o-document-text')
                    ->schema([
                        Grid::make()
                            ->schema([
                                TextEntry::make('name')
                                    ->placeholder('-')
                                    ->label(__('accounts::filament/resources/invoice.infolist.section.general.entries.customer-invoice'))
                                    ->icon('heroicon-o-document')
                                    ->weight('bold')
                                    ->size(TextSize::Large),
                            ])->columns(2),
                        Grid::make()
                            ->schema([
                                TextEntry::make('partner.name')
                                    ->placeholder('-')
                                    ->label(__('accounts::filament/resources/invoice.infolist.section.general.entries.customer'))
                                    ->visible(fn ($record) => $record->partner_id !== null)
                                    ->icon('heroicon-o-user'),
                                TextEntry::make('invoice_partner_display_name')
                                    ->placeholder('-')
                                    ->label(__('accounts::filament/resources/invoice.infolist.section.general.entries.customer'))
                                    ->visible(fn ($record) => $record->partner_id === null)
                                    ->icon('heroicon-o-user'),
                                TextEntry::make('invoice_date')
                                    ->placeholder('-')
                                    ->label(__('accounts::filament/resources/invoice.infolist.section.general.entries.invoice-date'))
                                    ->icon('heroicon-o-calendar')
                                    ->date(),
                                TextEntry::make('invoice_date_due')
                                    ->placeholder('-')
                                    ->label(__('accounts::filament/resources/invoice.infolist.section.general.entries.due-date'))
                                    ->icon('heroicon-o-clock')
                                    ->hidden(fn ($record) => $record->invoice_payment_term_id !== null)
                                    ->date(),
                                TextEntry::make('invoicePaymentTerm.name')
                                    ->placeholder('-')
                                    ->label(__('accounts::filament/resources/invoice.infolist.section.general.entries.payment-term'))
                                    ->hidden(fn ($record) => $record->invoice_payment_term_id === null)
                                    ->icon('heroicon-o-calendar-days'),
                            ])->columns(2),
                    ]),
                Tabs::make()
                    ->columnSpan('full')
                    ->tabs([
                        Tab::make(__('accounts::filament/resources/invoice.infolist.tabs.invoice-lines.title'))
                            ->icon('heroicon-o-list-bullet')
                            ->schema([
                                RepeatableEntry::make('lines')
                                    ->hiddenLabel()
                                    ->schema([
                                        TextEntry::make('name')
                                            ->placeholder('-')
                                            ->label(__('accounts::filament/resources/invoice.infolist.tabs.invoice-lines.repeater.products.entries.product'))
                                            ->icon('heroicon-o-cube'),
                                        TextEntry::make('quantity')
                                            ->placeholder('-')
                                            ->label(__('accounts::filament/resources/invoice.infolist.tabs.invoice-lines.repeater.products.entries.quantity'))
                                            ->icon('heroicon-o-hashtag'),
                                        TextEntry::make('uom.name')
                                            ->placeholder('-')
                                            ->visible(fn (ProductSettings $settings) => $settings->enable_uom)
                                            ->label(__('accounts::filament/resources/invoice.infolist.tabs.invoice-lines.repeater.products.entries.unit'))
                                            ->icon('heroicon-o-scale'),
                                        TextEntry::make('price_unit')
                                            ->placeholder('-')
                                            ->label(__('accounts::filament/resources/invoice.infolist.tabs.invoice-lines.repeater.products.entries.unit-price'))
                                            ->icon('heroicon-o-banknotes')
                                            ->money(fn ($record) => $record->currency?->name),
                                        TextEntry::make('discount')
                                            ->placeholder('-')
                                            ->label(__('accounts::filament/resources/invoice.infolist.tabs.invoice-lines.repeater.products.entries.discount-percentage'))
                                            ->icon('heroicon-o-tag')
                                            ->suffix('%'),
                                        TextEntry::make('taxes.name')
                                            ->badge()
                                            ->state(function ($record): array {
                                                return $record->taxes->map(fn ($tax) => [
                                                    'name' => $tax->name,
                                                ])->toArray();
                                            })
                                            ->icon('heroicon-o-receipt-percent')
                                            ->formatStateUsing(fn ($state) => $state['name'])
                                            ->placeholder('-')
                                            ->label(__('accounts::filament/resources/invoice.infolist.tabs.invoice-lines.repeater.products.entries.taxes'))
                                            ->weight(FontWeight::Bold),
                                        TextEntry::make('price_subtotal')
                                            ->placeholder('-')
                                            ->label(__('accounts::filament/resources/invoice.infolist.tabs.invoice-lines.repeater.products.entries.sub-total'))
                                            ->icon('heroicon-o-calculator')
                                            ->money(fn ($record) => $record->currency?->name),
                                    ])->columns(5),
                                Livewire::make(InvoiceSummary::class, function ($record) {
                                    return [
                                        'currency'   => $record->currency,
                                        'amountTax'  => $record->amount_tax ?? 0,
                                        'products'   => $record->lines->map(function ($item) {
                                            return [
                                                ...$item->toArray(),
                                                'taxes' => $item->taxes->pluck('id')->toArray() ?? [],
                                            ];
                                        })->toArray(),
                                    ];
                                }),
                            ]),
                        Tab::make(__('accounts::filament/resources/invoice.infolist.tabs.other-information.title'))
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                Section::make(__('accounts::filament/resources/invoice.infolist.tabs.other-information.fieldset.invoice.title'))
                                    ->icon('heroicon-o-document')
                                    ->schema([
                                        Grid::make()
                                            ->schema([
                                                TextEntry::make('reference')
                                                    ->placeholder('-')
                                                    ->label(__('accounts::filament/resources/invoice.infolist.tabs.other-information.fieldset.invoice.entries.customer-reference'))
                                                    ->icon('heroicon-o-hashtag'),
                                                TextEntry::make('invoiceUser.name')
                                                    ->placeholder('-')
                                                    ->label(__('accounts::filament/resources/invoice.infolist.tabs.other-information.fieldset.invoice.entries.sales-person'))
                                                    ->icon('heroicon-o-user'),
                                                TextEntry::make('partnerBank.account_number')
                                                    ->placeholder('-')
                                                    ->label(__('accounts::filament/resources/invoice.infolist.tabs.other-information.fieldset.invoice.entries.recipient-bank'))
                                                    ->icon('heroicon-o-building-library'),
                                                TextEntry::make('payment_reference')
                                                    ->placeholder('-')
                                                    ->label(__('accounts::filament/resources/invoice.infolist.tabs.other-information.fieldset.invoice.entries.payment-reference'))
                                                    ->icon('heroicon-o-identification'),
                                                TextEntry::make('delivery_date')
                                                    ->placeholder('-')
                                                    ->label(__('accounts::filament/resources/invoice.infolist.tabs.other-information.fieldset.invoice.entries.delivery-date'))
                                                    ->icon('heroicon-o-truck')
                                                    ->date(),
                                            ])->columns(2),
                                    ]),
                                Section::make(__('accounts::filament/resources/invoice.infolist.tabs.other-information.fieldset.accounting.title'))
                                    ->icon('heroicon-o-calculator')
                                    ->schema([
                                        Grid::make()
                                            ->schema([
                                                TextEntry::make('invoiceIncoterm.name')
                                                    ->placeholder('-')
                                                    ->label(__('accounts::filament/resources/invoice.infolist.tabs.other-information.fieldset.accounting.fieldset.incoterm'))
                                                    ->icon('heroicon-o-globe-alt'),
                                                TextEntry::make('incoterm_location')
                                                    ->placeholder('-')
                                                    ->label(__('accounts::filament/resources/invoice.infolist.tabs.other-information.fieldset.accounting.fieldset.incoterm-location'))
                                                    ->icon('heroicon-o-map-pin'),
                                                TextEntry::make('paymentMethodLine.name')
                                                    ->placeholder('-')
                                                    ->label(__('accounts::filament/resources/invoice.infolist.tabs.other-information.fieldset.accounting.fieldset.payment-method'))
                                                    ->icon('heroicon-o-credit-card'),
                                                IconEntry::make('auto_post')
                                                    ->boolean()
                                                    ->placeholder('-')
                                                    ->label(__('accounts::filament/resources/invoice.infolist.tabs.other-information.fieldset.accounting.fieldset.auto-post'))
                                                    ->icon('heroicon-o-arrow-path'),
                                                IconEntry::make('checked')
                                                    ->label(__('accounts::filament/resources/invoice.infolist.tabs.other-information.fieldset.accounting.fieldset.checked'))
                                                    ->icon('heroicon-o-check-circle')
                                                    ->boolean(),
                                            ])->columns(2),
                                    ]),
                                Section::make(__('accounts::filament/resources/invoice.infolist.tabs.other-information.fieldset.additional-information.title'))
                                    ->icon('heroicon-o-document')
                                    ->schema([
                                        Grid::make()
                                            ->schema([
                                                TextEntry::make('company.name')
                                                    ->placeholder('-')
                                                    ->label(__('accounts::filament/resources/invoice.infolist.tabs.other-information.fieldset.additional-information.entries.company'))
                                                    ->icon('heroicon-o-building-office'),
                                                TextEntry::make('currency.name')
                                                    ->placeholder('-')
                                                    ->label(__('accounts::filament/resources/invoice.infolist.tabs.other-information.fieldset.additional-information.entries.currency'))
                                                    ->icon('heroicon-o-banknotes'),
                                            ])->columns(2),
                                    ]),
                                Section::make(__('accounts::filament/resources/invoice.infolist.tabs.other-information.fieldset.marketing.title'))
                                    ->icon('heroicon-o-megaphone')
                                    ->schema([
                                        Grid::make()
                                            ->schema([
                                                TextEntry::make('campaign.name')
                                                    ->placeholder('-')
                                                    ->label(__('accounts::filament/resources/invoice.infolist.tabs.other-information.fieldset.marketing.entries.campaign'))
                                                    ->icon('heroicon-o-presentation-chart-line'),
                                                TextEntry::make('medium.name')
                                                    ->placeholder('-')
                                                    ->label(__('accounts::filament/resources/invoice.infolist.tabs.other-information.fieldset.marketing.entries.medium'))
                                                    ->icon('heroicon-o-device-phone-mobile'),
                                                TextEntry::make('source.name')
                                                    ->placeholder('-')
                                                    ->label(__('accounts::filament/resources/invoice.infolist.tabs.other-information.fieldset.marketing.entries.source'))
                                                    ->icon('heroicon-o-link'),
                                            ])->columns(2),
                                    ]),
                            ]),
                        Tab::make(__('accounts::filament/resources/invoice.infolist.tabs.term-and-conditions.title'))
                            ->icon('heroicon-o-clipboard-document-list')
                            ->schema([
                                TextEntry::make('narration')
                                    ->html()
                                    ->hiddenLabel(),
                            ]),
                    ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListInvoices::route('/'),
            'create' => CreateInvoice::route('/create'),
            'view'   => ViewInvoice::route('/{record}'),
            'edit'   => EditInvoice::route('/{record}/edit'),
        ];
    }

    public static function getProductRepeater(): Repeater
    {
        return Repeater::make('products')
            ->relationship('lines')
            ->hiddenLabel()
            ->live(onBlur: true)
            ->reactive()
            ->label(__('accounts::filament/resources/invoice.form.tabs.invoice-lines.repeater.products.title'))
            ->addActionLabel(__('accounts::filament/resources/invoice.form.tabs.invoice-lines.repeater.products.add-product'))
            ->collapsible()
            ->defaultItems(0)
            ->table([
                TableColumn::make('product_id')
                    ->label(__('accounts::filament/resources/invoice.form.tabs.invoice-lines.repeater.products.columns.product'))
                    ->width(250)
                    ->markAsRequired()
                    ->toggleable(),
                TableColumn::make('quantity')
                    ->label(__('accounts::filament/resources/invoice.form.tabs.invoice-lines.repeater.products.columns.quantity'))
                    ->width(150)
                    ->markAsRequired()
                    ->toggleable(),
                TableColumn::make('uom_id')
                    ->label(__('accounts::filament/resources/invoice.form.tabs.invoice-lines.repeater.products.columns.unit'))
                    ->width(150)
                    ->visible(fn () => resolve(ProductSettings::class)->enable_uom)
                    ->toggleable(),
                TableColumn::make('taxes')
                    ->label(__('accounts::filament/resources/invoice.form.tabs.invoice-lines.repeater.products.columns.taxes'))
                    ->width(250)
                    ->toggleable(),
                TableColumn::make('discount')
                    ->label(__('accounts::filament/resources/invoice.form.tabs.invoice-lines.repeater.products.columns.discount-percentage'))
                    ->width(150)
                    ->toggleable(isToggledHiddenByDefault: true),
                TableColumn::make('price_unit')
                    ->label(__('accounts::filament/resources/invoice.form.tabs.invoice-lines.repeater.products.columns.unit-price'))
                    ->width(150)
                    ->markAsRequired(),
                TableColumn::make('price_subtotal')
                    ->label(__('accounts::filament/resources/invoice.form.tabs.invoice-lines.repeater.products.columns.sub-total'))
                    ->width(150)
                    ->toggleable(),
            ])
            ->itemLabel(function ($state) {
                if (! empty($state['name'])) {
                    return $state['name'];
                }

                $product = Product::find($state['product_id']);

                return $product->name ?? null;
            })
            ->deleteAction(fn (Action $action) => $action->requiresConfirmation())
            ->schema([
                Select::make('product_id')
                    ->label(__('accounts::filament/resources/invoice.form.tabs.invoice-lines.repeater.products.fields.product'))
                    ->relationship(
                        'product',
                        'name',
                        fn (Builder $query) => $query->withTrashed()->where('is_configurable', null),
                    )
                    ->getOptionLabelFromRecordUsing(function ($record): string {
                        return $record->name.($record->trashed() ? ' (Deleted)' : '');
                    })
                    ->disableOptionWhen(function ($value, $state, $component, $label) {
                        if (str_contains($label, ' (Deleted)')) {
                            return true;
                        }

                        $repeater = $component->getParentRepeater();
                        if (! $repeater) {
                            return false;
                        }

                        return collect($repeater->getState())
                            ->pluck(
                                (string) str($component->getStatePath())
                                    ->after("{$repeater->getStatePath()}.")
                                    ->after('.'),
                            )
                            ->flatten()
                            ->diff(Arr::wrap($state))
                            ->filter(fn (mixed $siblingItemState): bool => filled($siblingItemState))
                            ->contains($value);
                    })
                    ->searchable()
                    ->preload()
                    ->live()
                    ->dehydrated()
                    ->disabled(fn ($record) => $record && in_array($record->parent_state, [MoveState::POSTED, MoveState::CANCEL]))
                    ->afterStateUpdated(fn (Set $set, Get $get) => static::afterProductUpdated($set, $get))
                    ->required(),
                TextInput::make('quantity')
                    ->label(__('accounts::filament/resources/invoice.form.tabs.invoice-lines.repeater.products.fields.quantity'))
                    ->required()
                    ->default(1)
                    ->numeric()
                    ->maxValue(99999999999)
                    ->live(onBlur: true)
                    ->dehydrated()
                    ->disabled(fn ($record) => $record && in_array($record->parent_state, [MoveState::POSTED, MoveState::CANCEL]))
                    ->afterStateUpdated(fn (Set $set, Get $get) => static::afterProductQtyUpdated($set, $get)),
                Select::make('uom_id')
                    ->label(__('accounts::filament/resources/invoice.form.tabs.invoice-lines.repeater.products.fields.unit'))
                    ->relationship(
                        'uom',
                        'name',
                        fn ($query) => $query->where('category_id', 1)->orderBy('id'),
                    )
                    ->required()
                    ->live()
                    ->selectablePlaceholder(false)
                    ->dehydrated()
                    ->disabled(fn ($record) => $record && in_array($record->parent_state, [MoveState::POSTED, MoveState::CANCEL]))
                    ->afterStateUpdated(fn (Set $set, Get $get) => static::afterUOMUpdated($set, $get))
                    ->visible(fn (ProductSettings $settings) => $settings->enable_uom),
                Select::make('taxes')
                    ->label(__('accounts::filament/resources/invoice.form.tabs.invoice-lines.repeater.products.fields.taxes'))
                    ->relationship(
                        'taxes',
                        'name',
                        function (Builder $query) {
                            return $query->where('type_tax_use', TypeTaxUse::SALE->value);
                        },
                    )
                    ->searchable()
                    ->multiple()
                    ->preload()
                    ->dehydrated()
                    ->disabled(fn ($record) => $record && in_array($record->parent_state, [MoveState::POSTED, MoveState::CANCEL]))
                    ->afterStateHydrated(fn (Get $get, Set $set) => self::calculateLineTotals($set, $get))
                    ->afterStateUpdated(fn (Get $get, Set $set, $state) => self::calculateLineTotals($set, $get))
                    ->live(),
                TextInput::make('discount')
                    ->label(__('Discount Percentage'))
                    ->label(__('accounts::filament/resources/invoice.form.tabs.invoice-lines.repeater.products.fields.discount-percentage'))
                    ->numeric()
                    ->default(0)
                    ->minValue(0)
                    ->maxValue(99999999999)
                    ->live(onBlur: true)
                    ->dehydrated()
                    ->disabled(fn ($record) => $record && in_array($record->parent_state, [MoveState::POSTED, MoveState::CANCEL]))
                    ->afterStateUpdated(fn (Set $set, Get $get) => self::calculateLineTotals($set, $get)),
                TextInput::make('price_unit')
                    ->label(__('accounts::filament/resources/invoice.form.tabs.invoice-lines.repeater.products.fields.unit-price'))
                    ->numeric()
                    ->default(0)
                    ->minValue(0)
                    ->maxValue(99999999999)
                    ->required()
                    ->live(onBlur: true)
                    ->dehydrated()
                    ->disabled(fn ($record) => $record && in_array($record->parent_state, [MoveState::POSTED, MoveState::CANCEL]))
                    ->afterStateUpdated(fn (Set $set, Get $get) => self::calculateLineTotals($set, $get)),
                TextInput::make('price_subtotal')
                    ->label(__('accounts::filament/resources/invoice.form.tabs.invoice-lines.repeater.products.fields.sub-total'))
                    ->default(0)
                    ->dehydrated()
                    ->disabled(fn ($record) => $record && in_array($record->parent_state, [MoveState::POSTED, MoveState::CANCEL])),
                Hidden::make('product_uom_qty')
                    ->default(0),
                Hidden::make('price_tax')
                    ->default(0),
                Hidden::make('price_total')
                    ->default(0),
            ])
            ->mutateRelationshipDataBeforeCreateUsing(fn (array $data, $record) => static::mutateProductRelationship($data, $record))
            ->mutateRelationshipDataBeforeSaveUsing(fn (array $data, $record) => static::mutateProductRelationship($data, $record));
    }

    public static function mutateProductRelationship(array $data, $record): array
    {
        $data['currency_id'] = $record->currency_id;

        return $data;
    }

    private static function afterProductUpdated(Set $set, Get $get): void
    {
        if (! $get('product_id')) {
            return;
        }

        $product = Product::find($get('product_id'));

        $set('uom_id', $product->uom_id);

        $priceUnit = static::calculateUnitPrice($get('uom_id'), $product->price ?? $product->cost);

        $set('price_unit', round($priceUnit, 2));

        $set('taxes', $product->productTaxes->pluck('id')->toArray());

        $uomQuantity = static::calculateUnitQuantity($get('uom_id'), $get('quantity'));

        $set('product_uom_qty', round($uomQuantity, 2));

        self::calculateLineTotals($set, $get);
    }

    private static function afterProductQtyUpdated(Set $set, Get $get): void
    {
        if (! $get('product_id')) {
            return;
        }

        $uomQuantity = static::calculateUnitQuantity($get('uom_id'), $get('quantity'));

        $set('product_uom_qty', round($uomQuantity, 2));

        self::calculateLineTotals($set, $get);
    }

    private static function afterUOMUpdated(Set $set, Get $get): void
    {
        if (! $get('product_id')) {
            return;
        }

        $uomQuantity = static::calculateUnitQuantity($get('uom_id'), $get('quantity'));

        $set('product_uom_qty', round($uomQuantity, 2));

        $product = Product::find($get('product_id'));

        $priceUnit = static::calculateUnitPrice($get('uom_id'), $product->cost ?? $product->price);

        $set('price_unit', round($priceUnit, 2));

        self::calculateLineTotals($set, $get);
    }

    private static function calculateUnitQuantity($uomId, $quantity)
    {
        if (! $uomId) {
            return $quantity;
        }

        $uom = Uom::find($uomId);

        return (float) ($quantity ?? 0) / $uom->factor;
    }

    private static function calculateUnitPrice($uomId, $price)
    {
        if (! $uomId) {
            return $price;
        }

        $uom = Uom::find($uomId);

        return (float) ($price / $uom->factor);
    }

    private static function calculateLineTotals(Set $set, Get $get): void
    {
        if (! $get('product_id')) {
            $set('price_unit', 0);

            $set('discount', 0);

            $set('price_tax', 0);

            $set('price_subtotal', 0);

            $set('price_total', 0);

            return;
        }

        $priceUnit = floatval($get('price_unit'));

        $quantity = floatval($get('quantity') ?? 1);

        $subTotal = $priceUnit * $quantity;

        $discountValue = floatval($get('discount') ?? 0);

        if ($discountValue > 0) {
            $discountAmount = $subTotal * ($discountValue / 100);

            $subTotal = $subTotal - $discountAmount;
        }

        $taxIds = $get('taxes') ?? [];

        [$subTotal, $taxAmount] = Tax::collect($taxIds, $subTotal, $quantity);

        $set('price_subtotal', round($subTotal, 4));

        $set('price_tax', $taxAmount);

        $set('price_total', $subTotal + $taxAmount);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->orderByDesc('id');
    }
}
