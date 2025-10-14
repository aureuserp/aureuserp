<?php

namespace Webkul\Purchase\Filament\Customer\Clusters\Account\Resources;

use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ViewEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Livewire;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Webkul\Purchase\Enums\OrderState;
use Webkul\Purchase\Models\Order;
use Webkul\Website\Filament\Customer\Clusters\Account;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $cluster = Account::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('purchases::filament/customer/clusters/account/resources/order.table.columns.reference'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('approved_at')
                    ->label(__('purchases::filament/customer/clusters/account/resources/order.table.columns.confirmation-date'))
                    ->sortable()
                    ->placeholder('—'),
                TextColumn::make('invoice_status')
                    ->label(__('purchases::filament/customer/clusters/account/resources/order.table.columns.status'))
                    ->sortable()
                    ->badge()
                    ->toggleable(),
                TextColumn::make('total_amount')
                    ->label(__('purchases::filament/customer/clusters/account/resources/order.table.columns.total-amount'))
                    ->sortable()
                    ->money(fn (Order $record) => $record->currency->code),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
            ])
            ->modifyQueryUsing(function (Builder $query) {
                $query->where('partner_id', Auth::guard('customer')->id());
            });
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make()
                    ->schema([
                        Section::make()
                            ->schema([
                                TextEntry::make('total_amount')
                                    ->hiddenLabel()
                                    ->size('text-3xl')
                                    ->weight(FontWeight::Bold)
                                    ->money(fn (Order $record) => $record->currency->code),

                                Actions::make([
                                    Action::make('accept')
                                        ->label(__('purchases::filament/customer/clusters/account/resources/order.infolist.settings.actions.accept.label'))
                                        ->color('success')
                                        ->icon('heroicon-o-check-circle')
                                        ->disabled(fn (Order $record): bool => $record->mail_reception_confirmed)
                                        ->action(function (Order $record) {
                                            $record->update([
                                                'mail_reception_confirmed' => true,
                                            ]);

                                            $record->addMessage([
                                                'body' => __('purchases::filament/customer/clusters/account/resources/order.infolist.settings.actions.accept.message.body'),
                                                'type' => 'comment',
                                            ]);

                                            Notification::make()
                                                ->title(__('purchases::filament/customer/clusters/account/resources/order.infolist.settings.actions.accept.notification.title'))
                                                ->body(__('purchases::filament/customer/clusters/account/resources/order.infolist.settings.actions.accept.notification.body'))
                                                ->success()
                                                ->send();
                                        }),
                                    Action::make('decline')
                                        ->label(__('purchases::filament/customer/clusters/account/resources/order.infolist.settings.actions.decline.label'))
                                        ->color('danger')
                                        ->icon('heroicon-o-x-circle')
                                        ->disabled(fn (Order $record): bool => $record->mail_reception_declined)
                                        ->action(function (Order $record) {
                                            $record->update([
                                                'mail_reception_declined' => true,
                                            ]);

                                            $record->addMessage([
                                                'body' => __('purchases::filament/customer/clusters/account/resources/order.infolist.settings.actions.accept.decline.message.body'),
                                                'type' => 'comment',
                                            ]);

                                            Notification::make()
                                                ->title(__('purchases::filament/customer/clusters/account/resources/order.infolist.settings.actions.decline.notification.title'))
                                                ->body(__('purchases::filament/customer/clusters/account/resources/order.infolist.settings.actions.decline.notification.body'))
                                                ->success()
                                                ->send();
                                        }),
                                ])
                                    ->visible(fn (Order $record): bool => $record->state === OrderState::SENT)
                                    ->fullWidth(),

                                Actions::make([
                                    Action::make('print')
                                        ->label(__('purchases::filament/customer/clusters/account/resources/order.infolist.settings.actions.print.label'))
                                        ->icon('heroicon-o-printer')
                                        ->action(function (Order $record) {
                                            if ($record->state == OrderState::SENT) {
                                                $pdf = PDF::loadView('purchases::filament.admin.clusters.orders.orders.actions.print-quotation', [
                                                    'records'  => [$record],
                                                ]);

                                                $pdf->setPaper('a4', 'portrait');

                                                return response()->streamDownload(function () use ($pdf) {
                                                    echo $pdf->output();
                                                }, 'Quotation-'.str_replace('/', '_', $record->name).'.pdf');
                                            }

                                            $pdf = PDF::loadView('purchases::filament.admin.clusters.orders.orders.actions.print-purchase-order', [
                                                'records'  => [$record],
                                            ]);

                                            $pdf->setPaper('a4', 'portrait');

                                            return response()->streamDownload(function () use ($pdf) {
                                                echo $pdf->output();
                                            }, 'Purchase Order-'.str_replace('/', '_', $record->name).'.pdf');
                                        }),
                                ])
                                    ->fullWidth(),

                                ViewEntry::make('user')
                                    ->label(__('purchases::filament/customer/clusters/account/resources/order.infolist.settings.entries.buyer'))
                                    ->view('purchases::filament.customer.clusters.account.order.pages.view-record.buyer-card')
                                    ->visible(fn (Order $record): bool => (bool) $record->user_id),
                            ]),
                    ])
                    ->columnSpan(['lg' => 1]),

                Group::make()
                    ->schema([
                        Section::make()
                            ->schema([
                                /**
                                 * Order details
                                 */
                                Group::make()
                                    ->schema([
                                        TextEntry::make('name')
                                            ->hiddenLabel()
                                            ->size('text-3xl')
                                            ->weight(FontWeight::Bold)
                                            ->formatStateUsing(function (Order $record) {
                                                if ($record->state == OrderState::SENT) {
                                                    return __('purchases::filament/customer/clusters/account/resources/order.infolist.general.entries.quotation', ['id' => $record->name]);
                                                }

                                                return __('purchases::filament/customer/clusters/account/resources/order.infolist.general.entries.purchase-order', ['id' => $record->name]);
                                            }),
                                        TextEntry::make('ordered_at')
                                            ->label(__('purchases::filament/customer/clusters/account/resources/order.infolist.general.entries.order-date')),
                                        ViewEntry::make('company')
                                            ->label(__('purchases::filament/customer/clusters/account/resources/order.infolist.general.entries.from'))
                                            ->view('purchases::filament.customer.clusters.account.order.pages.view-record.from'),
                                        TextEntry::make('approved_at')
                                            ->label(__('purchases::filament/customer/clusters/account/resources/order.infolist.general.entries.confirmation-date'))
                                            ->visible(fn (Order $record): bool => (bool) $record->approved_at),
                                        TextEntry::make('ordered_at')
                                            ->label(__('purchases::filament/customer/clusters/account/resources/order.infolist.general.entries.receipt-date'))
                                            ->visible(fn (Order $record): bool => (bool) $record->ordered_at),
                                    ]),

                                /**
                                 * Order items
                                 */
                                Group::make()
                                    ->extraAttributes(['class' => 'mt-8'])
                                    ->schema([
                                        TextEntry::make('name')
                                            ->hiddenLabel()
                                            ->size('text-2xl')
                                            ->weight(FontWeight::Bold)
                                            ->formatStateUsing(function (Order $record) {
                                                return __('purchases::filament/customer/clusters/account/resources/order.infolist.general.entries.products');
                                            }),

                                        Livewire::make('list-products', function (Order $record) {
                                            return [
                                                'record' => $record,
                                            ];
                                        }),

                                        /**
                                         * Order totals
                                         */
                                        Group::make()
                                            ->extraAttributes(['class' => 'flex justify-end'])
                                            ->schema([
                                                TextEntry::make('untaxed_amount')
                                                    ->label(__('purchases::filament/customer/clusters/account/resources/order.infolist.general.entries.untaxed-amount'))
                                                    ->extraAttributes(['class' => 'flex justify-end'])
                                                    ->inlineLabel()
                                                    ->money(fn ($record) => $record->currency->code),

                                                TextEntry::make('tax_amount')
                                                    ->label(__('purchases::filament/customer/clusters/account/resources/order.infolist.general.entries.tax-amount'))
                                                    ->extraAttributes(['class' => 'flex justify-end'])
                                                    ->inlineLabel()
                                                    ->money(fn ($record) => $record->currency->code),

                                                Group::make()
                                                    ->extraAttributes(['class' => 'border-t pt-4 font-bold'])
                                                    ->schema([
                                                        TextEntry::make('total_amount')
                                                            ->label(__('purchases::filament/customer/clusters/account/resources/order.infolist.general.entries.total'))
                                                            ->extraAttributes(['class' => 'flex justify-end'])
                                                            ->inlineLabel()
                                                            ->money(fn ($record) => $record->currency->code),
                                                    ]),
                                            ])
                                            ->visible(fn (Order $record): bool => in_array($record->state, [OrderState::PURCHASE, OrderState::DONE])),
                                    ]),

                                /**
                                 * Communication history
                                 */
                                Group::make()
                                    ->extraAttributes(['class' => 'mt-8'])
                                    ->schema([
                                        TextEntry::make('name')
                                            ->hiddenLabel()
                                            ->size('text-2xl')
                                            ->weight(FontWeight::Bold)
                                            ->formatStateUsing(function (Order $record) {
                                                return __('purchases::filament/customer/clusters/account/resources/order.infolist.general.entries.communication-history');
                                            }),

                                        Livewire::make('chatter-panel', function (Order $record) {
                                            $record = Order::findOrFail($record->id);

                                            return [
                                                'record'             => $record,
                                                'showMessageAction'  => true,
                                                'showActivityAction' => false,
                                                'showFollowerAction' => false,
                                                'showLogAction'      => false,
                                                'showFileAction'     => false,
                                                'filters'            => [
                                                    'type' => [
                                                        'comment',
                                                    ],
                                                ],
                                            ];
                                        }),
                                    ]),
                            ]),
                    ])
                    ->columnSpan(['lg' => 2]),
            ])
            ->columns(3);
    }
}
