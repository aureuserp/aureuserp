<?php

namespace Webkul\Account\Filament\Resources;

use Carbon\Carbon;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Pages\Enums\SubNavigationPosition;
use Filament\Resources\Pages\Page;
use Filament\Resources\RelationManagers\RelationGroup;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\HtmlString;
use Webkul\Account\Enums\DelayType;
use Webkul\Account\Enums\DueTermValue;
use Webkul\Account\Enums\EarlyPayDiscount;
use Webkul\Account\Filament\Resources\PaymentTermResource\Pages\CreatePaymentTerm;
use Webkul\Account\Filament\Resources\PaymentTermResource\Pages\EditPaymentTerm;
use Webkul\Account\Filament\Resources\PaymentTermResource\Pages\ListPaymentTerms;
use Webkul\Account\Filament\Resources\PaymentTermResource\Pages\ManagePaymentDueTerm;
use Webkul\Account\Filament\Resources\PaymentTermResource\Pages\ViewPaymentTerm;
use Webkul\Account\Filament\Resources\PaymentTermResource\RelationManagers\PaymentDueTermRelationManager;
use Webkul\Account\Models\PaymentTerm;
use Webkul\Support\Filament\Forms\Components\Repeater;
use Webkul\Support\Filament\Forms\Components\Repeater\TableColumn;
use Webkul\Support\Filament\Infolists\Components\RepeatableEntry;
use Webkul\Support\Filament\Infolists\Components\Repeater\TableColumn as InfolistTableColumn;

class PaymentTermResource extends Resource
{
    protected static ?string $model = PaymentTerm::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-currency-dollar';

    protected static ?\Filament\Pages\Enums\SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Start;

    protected static bool $shouldRegisterNavigation = false;

    protected static bool $isGloballySearchable = false;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        Grid::make()
                            ->schema([
                                TextInput::make('name')
                                    ->required()
                                    ->label(__('accounts::filament/resources/payment-term.form.sections.fields.payment-term'))
                                    ->maxLength(255)
                                    ->extraInputAttributes(['style' => 'font-size: 1.5rem;height: 3rem;'])
                                    ->columnSpan(1),
                            ])->columns(2),
                        Group::make()
                            ->hidden()
                            ->schema([
                                Toggle::make('early_discount')
                                    ->live()
                                    ->inline(false)
                                    ->label(__('accounts::filament/resources/payment-term.form.sections.fields.early-discount')),
                            ])->columns(2),
                        Group::make()
                            ->visible(fn (Get $get) => $get('early_discount'))
                            ->schema([
                                TextInput::make('discount_percentage')
                                    ->required()
                                    ->numeric()
                                    ->maxValue(100)
                                    ->minValue(0)
                                    ->suffix(__('%'))
                                    ->hiddenLabel(),
                                TextInput::make('discount_days')
                                    ->required()
                                    ->integer()
                                    ->minValue(0)
                                    ->prefix(__('accounts::filament/resources/payment-term.form.sections.fields.discount-days-prefix'))
                                    ->suffix(__('accounts::filament/resources/payment-term.form.sections.fields.discount-days-suffix'))
                                    ->hiddenLabel(),
                            ])->columns(4),
                        Group::make()
                            ->visible(fn (Get $get) => $get('early_discount'))
                            ->schema([
                                Select::make('early_pay_discount')
                                    ->label(__('accounts::filament/resources/payment-term.form.sections.fields.reduced-tax'))
                                    ->options(EarlyPayDiscount::class)
                                    ->default(EarlyPayDiscount::INCLUDED->value),
                            ])->columns(2),
                        RichEditor::make('note')
                            ->label(__('accounts::filament/resources/payment-term.form.sections.fields.note')),
                    ]),
                Tabs::make()
                    ->schema([
                        Tab::make(__('accounts::filament/resources/payment-term.form.tabs.due-terms.title'))
                            ->icon('heroicon-o-list-bullet')
                            ->schema([
                                Group::make()
                                    ->schema([
                                        // LEFT COLUMN: Repeater
                                        Repeater::make('dueTerms')
                                            ->hiddenLabel()
                                            ->relationship('dueTerms')
                                            ->compact()
                                            ->reactive()
                                            ->addActionLabel(__('Add Due Term'))
                                            ->table([
                                                TableColumn::make('value')
                                                    ->label(__('accounts::filament/resources/payment-term.form.tabs.due-terms.repeater.due-terms.fields.value'))
                                                    ->width(150),
                                                TableColumn::make('value_amount')
                                                    ->label(__('accounts::filament/resources/payment-term.form.tabs.due-terms.repeater.due-terms.fields.due'))
                                                    ->width(100),
                                                TableColumn::make('delay_type')
                                                    ->label(__('accounts::filament/resources/payment-term.form.tabs.due-terms.repeater.due-terms.fields.delay-type'))
                                                    ->width(150),
                                                TableColumn::make('days_next_month')
                                                    ->label(__('accounts::filament/resources/payment-term.form.tabs.due-terms.repeater.due-terms.fields.days-on-the-next-month'))
                                                    ->width(100)
                                                    ->toggleable(isToggledHiddenByDefault: true),
                                                TableColumn::make('nb_days')
                                                    ->label(__('accounts::filament/resources/payment-term.form.tabs.due-terms.repeater.due-terms.fields.days'))
                                                    ->width(80),
                                            ])
                                            ->schema([
                                                Select::make('value')
                                                    ->options(DueTermValue::class)
                                                    ->native(false)
                                                    ->required(),

                                                TextInput::make('value_amount')
                                                    ->numeric()
                                                    ->required()
                                                    ->default(0)
                                                    ->minValue(0)
                                                    ->maxValue(100),

                                                Select::make('delay_type')
                                                    ->options(DelayType::class)
                                                    ->native(false)
                                                    ->required(),

                                                TextInput::make('days_next_month')
                                                    ->default(10)
                                                    ->numeric(),

                                                TextInput::make('nb_days')
                                                    ->default(0)
                                                    ->numeric(),
                                            ])->columnSpan(1), // LEFT COLUMN

                                        // RIGHT COLUMN: Preview & toggle
                                        Group::make()
                                            ->schema([
                                                TextEntry::make('payment_term_preview')
                                                    ->state(function (Get $get) {
                                                        $dueTerms = $get('dueTerms') ?? [];
                                                        $total = 1000;
                                                        $start = Carbon::now();

                                                        $html = '';
                                                        $html .= '<div style="margin-bottom:0.75rem;font-size:0.9rem;color:#6b7280;">Example: '.number_format($total, 2).' on '.$start->format('m/d/Y').'</div>';

                                                        if (empty($dueTerms)) {
                                                            $html .= '<div style="padding:1rem;background:#f3f4f6;border-radius:4px;color:#374151;">'.__('No due terms defined to preview').'</div>';

                                                            return new HtmlString($html);
                                                        }

                                                        // compute whether values are percentages (sum <= 100) or absolute
                                                        $sum = 0;
                                                        foreach ($dueTerms as $dt) {
                                                            $sum += isset($dt['value_amount']) ? floatval($dt['value_amount']) : 0;
                                                        }

                                                        $isPercent = $sum <= 100 && $sum > 0;

                                                        $html .= '<div class="rounded-md bg-gray-100 p-4 text-gray-800 dark:bg-gray-800 dark:text-white">';

                                                        $html .= '<div style="margin-bottom:0.5rem;font-weight:600;">Payment terms preview</div>';

                                                        $i = 1;
                                                        foreach ($dueTerms as $term) {
                                                            $valueAmount = isset($term['value_amount']) ? floatval($term['value_amount']) : 0;
                                                            if ($isPercent) {
                                                                $amt = round($total * ($valueAmount / 100), 2);
                                                            } else {
                                                                $amt = round($valueAmount, 2);
                                                            }

                                                            $days = 0;
                                                            if (isset($term['nb_days']) && intval($term['nb_days']) >= 0) {
                                                                $days = intval($term['nb_days']);
                                                            } elseif (isset($term['days_next_month']) && intval($term['days_next_month']) > 0) {
                                                                $days = intval($term['days_next_month']);
                                                            }

                                                            $dueDate = $start->copy()->addDays($days)->format('m/d/Y');

                                                            $html .= '<div style="margin-bottom:0.5rem;"><strong>'.$i.'#</strong> Installment of <strong>$'.number_format($amt, 2).'</strong> due on '.$dueDate.'</div>';
                                                            $i++;
                                                        }

                                                        $html .= '</div>';

                                                        return new HtmlString($html);
                                                    }),
                                            ])->columnSpan(1), // RIGHT COLUMN
                                    ])
                                    ->columns(2),
                            ]),
                    ]),
            ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('accounts::filament/resources/payment-term.table.columns.payment-term'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('company.name')
                    ->label(__('accounts::filament/resources/payment-term.table.columns.company'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->label(__('accounts::filament/resources/payment-term.table.columns.created-at'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->label(__('accounts::filament/resources/payment-term.table.columns.updated-at'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->groups([
                Tables\Grouping\Group::make('company.name')
                    ->label(__('accounts::filament/resources/payment-term.table.groups.company-name'))
                    ->collapsible(),
                Tables\Grouping\Group::make('discount_days')
                    ->label(__('accounts::filament/resources/payment-term.table.groups.discount-days'))
                    ->collapsible(),
                Tables\Grouping\Group::make('early_pay_discount')
                    ->label(__('accounts::filament/resources/payment-term.table.groups.early-pay-discount'))
                    ->collapsible(),
                Tables\Grouping\Group::make('name')
                    ->label(__('accounts::filament/resources/payment-term.table.groups.payment-term'))
                    ->collapsible(),
                Tables\Grouping\Group::make('display_on_invoice')
                    ->label(__('accounts::filament/resources/payment-term.table.groups.display-on-invoice'))
                    ->collapsible(),
                Tables\Grouping\Group::make('early_discount')
                    ->label(__('Early Discount'))
                    ->label(__('accounts::filament/resources/payment-term.table.groups.early-discount'))
                    ->collapsible(),
                Tables\Grouping\Group::make('discount_percentage')
                    ->label(__('accounts::filament/resources/payment-term.table.groups.discount-percentage'))
                    ->collapsible(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                RestoreAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('accounts::filament/resources/payment-term.table.actions.restore.notification.title'))
                            ->body(__('accounts::filament/resources/payment-term.table.actions.restore.notification.body'))
                    ),
                DeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('accounts::filament/resources/payment-term.table.actions.delete.notification.title'))
                            ->body(__('accounts::filament/resources/payment-term.table.actions.delete.notification.body'))
                    ),
                ForceDeleteAction::make()
                    ->action(function (PaymentTerm $record, ForceDeleteAction $action) {
                        if ($record->moves()->count() > 0) {
                            $action->failure();

                            return;
                        }

                        $record->forceDelete();

                        $action->success();
                    })
                    ->failureNotification(
                        Notification::make()
                            ->danger()
                            ->title(__('accounts::filament/resources/payment-term.table.actions.force-delete.notification.error.title'))
                            ->body(__('accounts::filament/resources/payment-term.table.actions.force-delete.notification.error.body'))
                    )
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('accounts::filament/resources/payment-term.table.actions.force-delete.notification.success.title'))
                            ->body(__('accounts::filament/resources/payment-term.table.actions.force-delete.notification.success.body'))
                    ),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('accounts::filament/resources/payment-term.table.bulk-actions.delete.notification.title'))
                                ->body(__('accounts::filament/resources/payment-term.table.bulk-actions.delete.notification.body'))
                        ),
                    ForceDeleteBulkAction::make()
                        ->action(function (Collection $records, ForceDeleteBulkAction $action) {
                            $hasMoves = $records->contains(function ($record) {
                                return $record->moves()->exists();
                            });

                            if ($hasMoves) {
                                $action->failure();

                                return;
                            }

                            $records->each(fn (Model $record) => $record->forceDelete());

                            $action->success();
                        })
                        ->failureNotification(
                            Notification::make()
                                ->danger()
                                ->title(__('accounts::filament/resources/payment-term.table.bulk-actions.force-delete.notification.error.title'))
                                ->body(__('accounts::filament/resources/payment-term.table.bulk-actions.force-delete.notification.error.body'))
                        )
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('accounts::filament/resources/payment-term.table.bulk-actions.force-delete.notification.success.title'))
                                ->body(__('accounts::filament/resources/payment-term.table.bulk-actions.force-delete.notification.success.body'))
                        ),
                    RestoreBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('accounts::filament/resources/payment-term.table.bulk-actions.force-restore.notification.title'))
                                ->body(__('accounts::filament/resources/payment-term.table.bulk-actions.force-restore.notification.body'))
                        ),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        Grid::make(['default' => 3])
                            ->schema([
                                TextEntry::make('name')
                                    ->label(__('accounts::filament/resources/payment-term.infolist.sections.entries.payment-term'))
                                    ->icon('heroicon-o-briefcase')
                                    ->placeholder('—'),
                                IconEntry::make('early_discount')
                                    ->hidden()
                                    ->label(__('accounts::filament/resources/payment-term.infolist.sections.entries.early-discount'))
                                    ->boolean(),
                                Group::make()
                                    ->visible(fn (Get $get) => $get('early_discount'))
                                    ->schema([
                                        TextEntry::make('discount_percentage')
                                            ->suffix('%')
                                            ->label(__('accounts::filament/resources/payment-term.infolist.sections.entries.discount-percentage'))
                                            ->placeholder('—'),

                                        TextEntry::make('discount_days')
                                            ->label(__('accounts::filament/resources/payment-term.infolist.sections.entries.discount-days-prefix'))
                                            ->suffix(__('accounts::filament/resources/payment-term.infolist.sections.entries.discount-days-suffix'))
                                            ->placeholder('—'),
                                    ])->columns(2),
                                TextEntry::make('early_pay_discount')
                                    ->visible(fn (Get $get) => $get('early_discount'))
                                    ->label(__('accounts::filament/resources/payment-term.infolist.sections.entries.reduced-tax'))
                                    ->placeholder('—'),
                                TextEntry::make('note')
                                    ->label(__('accounts::filament/resources/payment-term.infolist.sections.entries.note'))
                                    ->columnSpanFull()
                                    ->formatStateUsing(fn ($state) => new HtmlString($state))
                                    ->placeholder('—'),
                            ]),
                    ]),
                Tabs::make()
                    ->tabs([
                        Tab::make(__('accounts::filament/resources/payment-term.infolist.tabs.due-terms.title'))
                            ->icon('heroicon-o-list-bullet')
                            ->schema([
                                RepeatableEntry::make('dueTerms')
                                    ->hiddenLabel()
                                    ->live()
                                    ->table([
                                        InfolistTableColumn::make('value')
                                            ->alignCenter()
                                            ->label(__('accounts::filament/resources/payment-term.infolist.tabs.due-terms.repeater.due-terms.entries.value')),

                                        InfolistTableColumn::make('value_amount')
                                            ->alignCenter()
                                            ->label(__('accounts::filament/resources/payment-term.infolist.tabs.due-terms.repeater.due-terms.entries.due')),

                                        InfolistTableColumn::make('delay_type')
                                            ->alignCenter()
                                            ->label(__('accounts::filament/resources/payment-term.infolist.tabs.due-terms.repeater.due-terms.entries.delay-type')),

                                        InfolistTableColumn::make('days_next_month')
                                            ->alignCenter()
                                            ->label(__('accounts::filament/resources/payment-term.infolist.tabs.due-terms.repeater.due-terms.entries.days-on-the-next-month')),

                                        InfolistTableColumn::make('nb_days')
                                            ->alignCenter()
                                            ->label(__('accounts::filament/resources/payment-term.infolist.tabs.due-terms.repeater.due-terms.entries.days')),
                                    ])
                                    ->schema([
                                        TextEntry::make('value')
                                            ->placeholder('-')
                                            ->formatStateUsing(fn ($state) => DueTermValue::options()[$state] ?? $state),

                                        TextEntry::make('value_amount')
                                            ->placeholder('-'),

                                        TextEntry::make('delay_type')
                                            ->placeholder('-')
                                            ->formatStateUsing(fn ($state) => DelayType::options()[$state] ?? $state),

                                        TextEntry::make('days_next_month')
                                            ->placeholder('-'),

                                        TextEntry::make('nb_days')
                                            ->placeholder('-'),
                                    ]),
                                TextEntry::make('payment_term_preview')
                                    ->state(function (Get $get) {
                                        $dueTerms = $get('dueTerms') ?? [];
                                        $total = 1000;
                                        $start = Carbon::now();

                                        $html = '';
                                        $html .= '<div style="margin-bottom:0.75rem;font-size:0.9rem;color:#6b7280;">Example: '.number_format($total, 2).' on '.$start->format('m/d/Y').'</div>';

                                        if (empty($dueTerms)) {
                                            $html .= '<div style="padding:1rem;background:#f3f4f6;border-radius:4px;color:#374151;">'.__('No due terms defined to preview').'</div>';

                                            return new HtmlString($html);
                                        }

                                        // compute whether values are percentages (sum <= 100) or absolute
                                        $sum = 0;
                                        foreach ($dueTerms as $dt) {
                                            $sum += isset($dt['value_amount']) ? floatval($dt['value_amount']) : 0;
                                        }

                                        $isPercent = $sum <= 100 && $sum > 0;

                                        $html .= '<div class="rounded-md bg-gray-100 p-4 text-gray-800 dark:bg-gray-800 dark:text-white">';

                                        $html .= '<div style="margin-bottom:0.5rem;font-weight:600;">Payment terms preview</div>';

                                        $i = 1;
                                        foreach ($dueTerms as $term) {
                                            $valueAmount = isset($term['value_amount']) ? floatval($term['value_amount']) : 0;
                                            if ($isPercent) {
                                                $amt = round($total * ($valueAmount / 100), 2);
                                            } else {
                                                $amt = round($valueAmount, 2);
                                            }

                                            $days = 0;
                                            if (isset($term['nb_days']) && intval($term['nb_days']) >= 0) {
                                                $days = intval($term['nb_days']);
                                            } elseif (isset($term['days_next_month']) && intval($term['days_next_month']) > 0) {
                                                $days = intval($term['days_next_month']);
                                            }

                                            $dueDate = $start->copy()->addDays($days)->format('m/d/Y');

                                            $html .= '<div style="margin-bottom:0.5rem;"><strong>'.$i.'#</strong> Installment of <strong>$'.number_format($amt, 2).'</strong> due on '.$dueDate.'</div>';
                                            $i++;
                                        }

                                        $html .= '</div>';

                                        return new HtmlString($html);
                                    }),
                            ]),
                    ]),
            ])->columns(1);
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            ViewPaymentTerm::class,
            EditPaymentTerm::class,
            ManagePaymentDueTerm::class,
        ]);
    }

    // public static function getRelations(): array
    // {
    //     $relations = [
    //         RelationGroup::make('due_terms', [
    //             PaymentDueTermRelationManager::class,
    //         ])
    //             ->icon('heroicon-o-banknotes'),
    //     ];

    //     return $relations;
    // }

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
