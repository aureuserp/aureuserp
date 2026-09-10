<?php

namespace Webkul\Account\Filament\Resources\PaymentTermResource\Schemas;

use Carbon\Carbon;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Support\HtmlString;
use Webkul\Account\Enums\DelayType;
use Webkul\Account\Enums\DueTermValue;
use Webkul\Support\Filament\Infolists\Components\RepeatableEntry;
use Webkul\Support\Filament\Infolists\Components\Repeater\TableColumn as InfolistTableColumn;

class PaymentTermInfolist
{
    public static function configure(Schema $schema): Schema
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
}
