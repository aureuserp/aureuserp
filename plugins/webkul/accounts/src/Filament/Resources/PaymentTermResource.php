<?php

namespace Webkul\Account\Filament\Resources;

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
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;
use Webkul\Account\Enums\EarlyPayDiscount;
use Webkul\Account\Filament\Resources\PaymentTermResource\Pages\CreatePaymentTerm;
use Webkul\Account\Filament\Resources\PaymentTermResource\Pages\EditPaymentTerm;
use Webkul\Account\Filament\Resources\PaymentTermResource\Pages\ListPaymentTerms;
use Webkul\Account\Filament\Resources\PaymentTermResource\Pages\ManagePaymentDueTerm;
use Webkul\Account\Filament\Resources\PaymentTermResource\Pages\ViewPaymentTerm;
use Webkul\Account\Filament\Resources\PaymentTermResource\RelationManagers\PaymentDueTermRelationManager;
use Webkul\Account\Models\PaymentTerm;

class PaymentTermResource extends Resource
{
    protected static ?string $model = PaymentTerm::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-currency-dollar';

    protected static ?SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Start;

    protected static bool $shouldRegisterNavigation = false;

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
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('accounts::filament/resources/payment-term.table.actions.force-delete.notification.title'))
                            ->body(__('accounts::filament/resources/payment-term.table.actions.force-delete.notification.body'))
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
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('accounts::filament/resources/payment-term.table.bulk-actions.force-delete.notification.title'))
                                ->body(__('accounts::filament/resources/payment-term.table.bulk-actions.force-delete.notification.body'))
                        ),
                    RestoreBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('accounts::filament/resources/payment-term.table.bulk-actions.force-restore.notification.title'))
                                ->body(__('accounts::filament/resources/payment-term.table.bulk-actions.force-restore.notification.body'))
                        ),
                ]),
            ]);
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
                                    ->label(__('accounts::filament/resources/payment-term.infolist.sections.entries.early-discount'))
                                    ->boolean(),
                                Group::make()
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
                                    ->label(__('accounts::filament/resources/payment-term.infolist.sections.entries.reduced-tax'))
                                    ->placeholder('—'),
                                TextEntry::make('note')
                                    ->label(__('accounts::filament/resources/payment-term.infolist.sections.entries.note'))
                                    ->columnSpanFull()
                                    ->formatStateUsing(fn ($state) => new HtmlString($state))
                                    ->placeholder('—'),
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

    public static function getRelations(): array
    {
        $relations = [
            RelationGroup::make('due_terms', [
                PaymentDueTermRelationManager::class,
            ])
                ->icon('heroicon-o-banknotes'),
        ];

        return $relations;
    }

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
