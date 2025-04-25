<?php

namespace Webkul\Account\Filament\Resources;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Pages\SubNavigationPosition;
use Filament\Resources\Pages\Page;
use Filament\Resources\RelationManagers\RelationGroup;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;
use Webkul\Account\Enums\EarlyPayDiscount;
use Webkul\Account\Filament\Resources\PaymentTermResource\Pages;
use Webkul\Account\Filament\Resources\PaymentTermResource\RelationManagers;
use Webkul\Account\Models\PaymentTerm;

class PaymentTermResource extends Resource
{
    protected static ?string $model = PaymentTerm::class;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';

    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Start;

    protected static bool $shouldRegisterNavigation = false;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\Grid::make()
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->required()
                                    ->label(__('accounts::filament/resources/payment-term.form.sections.fields.payment-term'))
                                    ->maxLength(255)
                                    ->extraInputAttributes(['style' => 'font-size: 1.5rem;height: 3rem;'])
                                    ->columnSpan(1),
                            ])->columns(2),
                        Forms\Components\Group::make()
                            ->schema([
                                Forms\Components\Toggle::make('early_discount')
                                    ->live()
                                    ->inline(false)
                                    ->label(__('accounts::filament/resources/payment-term.form.sections.fields.early-discount')),
                            ])->columns(2),
                        Forms\Components\Group::make()
                            ->visible(fn (Get $get) => $get('early_discount'))
                            ->schema([
                                Forms\Components\TextInput::make('discount_percentage')
                                    ->required()
                                    ->numeric()
                                    ->maxValue(100)
                                    ->minValue(0)
                                    ->suffix(__('%'))
                                    ->hiddenLabel(),
                                Forms\Components\TextInput::make('discount_days')
                                    ->required()
                                    ->integer()
                                    ->minValue(0)
                                    ->prefix(__('accounts::filament/resources/payment-term.form.sections.fields.discount-days-prefix'))
                                    ->suffix(__('accounts::filament/resources/payment-term.form.sections.fields.discount-days-suffix'))
                                    ->hiddenLabel(),
                            ])->columns(4),
                        Forms\Components\Group::make()
                            ->visible(fn (Get $get) => $get('early_discount'))
                            ->schema([
                                Forms\Components\Select::make('early_pay_discount')
                                    ->label(__('accounts::filament/resources/payment-term.form.sections.fields.reduced-tax'))
                                    ->options(EarlyPayDiscount::class)
                                    ->default(EarlyPayDiscount::INCLUDED->value),
                            ])->columns(2),
                        Forms\Components\RichEditor::make('note')
                            ->label(__('accounts::filament/resources/payment-term.form.sections.fields.note')),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('accounts::filament/resources/payment-term.table.columns.payment-term'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('company.name')
                    ->label(__('accounts::filament/resources/payment-term.table.columns.company'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->label(__('accounts::filament/resources/payment-term.table.columns.created-at'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
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
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\RestoreAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('accounts::filament/resources/payment-term.table.actions.restore.notification.title'))
                            ->body(__('accounts::filament/resources/payment-term.table.actions.restore.notification.body'))
                    ),
                Tables\Actions\DeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('accounts::filament/resources/payment-term.table.actions.delete.notification.title'))
                            ->body(__('accounts::filament/resources/payment-term.table.actions.delete.notification.body'))
                    ),
                Tables\Actions\ForceDeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('accounts::filament/resources/payment-term.table.actions.force-delete.notification.title'))
                            ->body(__('accounts::filament/resources/payment-term.table.actions.force-delete.notification.body'))
                    ),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('accounts::filament/resources/payment-term.table.bulk-actions.delete.notification.title'))
                                ->body(__('accounts::filament/resources/payment-term.table.bulk-actions.delete.notification.body'))
                        ),
                    Tables\Actions\ForceDeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('accounts::filament/resources/payment-term.table.bulk-actions.force-delete.notification.title'))
                                ->body(__('accounts::filament/resources/payment-term.table.bulk-actions.force-delete.notification.body'))
                        ),
                    Tables\Actions\RestoreBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('accounts::filament/resources/payment-term.table.bulk-actions.force-restore.notification.title'))
                                ->body(__('accounts::filament/resources/payment-term.table.bulk-actions.force-restore.notification.body'))
                        ),
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make()
                    ->schema([
                        Infolists\Components\Grid::make(['default' => 3])
                            ->schema([
                                Infolists\Components\TextEntry::make('name')
                                    ->label(__('accounts::filament/resources/payment-term.infolist.sections.entries.payment-term'))
                                    ->icon('heroicon-o-briefcase')
                                    ->placeholder('—'),
                                Infolists\Components\IconEntry::make('early_discount')
                                    ->label(__('accounts::filament/resources/payment-term.infolist.sections.entries.early-discount'))
                                    ->boolean(),
                                Infolists\Components\Group::make()
                                    ->schema([
                                        Infolists\Components\TextEntry::make('discount_percentage')
                                            ->suffix('%')
                                            ->label(__('accounts::filament/resources/payment-term.infolist.sections.entries.discount-percentage'))
                                            ->placeholder('—'),

                                        Infolists\Components\TextEntry::make('discount_days')
                                            ->label(__('accounts::filament/resources/payment-term.infolist.sections.entries.discount-days-prefix'))
                                            ->suffix(__('accounts::filament/resources/payment-term.infolist.sections.entries.discount-days-suffix'))
                                            ->placeholder('—'),
                                    ])->columns(2),
                                Infolists\Components\TextEntry::make('early_pay_discount')
                                    ->label(__('accounts::filament/resources/payment-term.infolist.sections.entries.reduced-tax'))
                                    ->placeholder('—'),
                                Infolists\Components\TextEntry::make('note')
                                    ->label(__('accounts::filament/resources/payment-term.infolist.sections.entries.note'))
                                    ->columnSpanFull()
                                    ->formatStateUsing(fn ($state) => new HtmlString($state))
                                    ->placeholder('—'),
                            ]),
                    ]),
            ]);
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            Pages\ViewPaymentTerm::class,
            Pages\EditPaymentTerm::class,
            Pages\ManagePaymentDueTerm::class,
        ]);
    }

    public static function getRelations(): array
    {
        $relations = [
            RelationGroup::make('due_terms', [
                RelationManagers\PaymentDueTermRelationManager::class,
            ])
                ->icon('heroicon-o-banknotes'),
        ];

        return $relations;
    }

    public static function getPages(): array
    {
        return [
            'index'             => Pages\ListPaymentTerms::route('/'),
            'create'            => Pages\CreatePaymentTerm::route('/create'),
            'view'              => Pages\ViewPaymentTerm::route('/{record}'),
            'edit'              => Pages\EditPaymentTerm::route('/{record}/edit'),
            'payment-due-terms' => Pages\ManagePaymentDueTerm::route('/{record}/payment-due-terms'),
        ];
    }
}
