<?php

namespace Webkul\Account\Filament\Resources\PaymentTermResource\Schemas;

use Carbon\Carbon;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
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
use Webkul\Account\Enums\EarlyPayDiscount;
use Webkul\Support\Filament\Forms\Components\Repeater;
use Webkul\Support\Filament\Forms\Components\Repeater\TableColumn;

class PaymentTermForm
{
    public static function configure(Schema $schema): Schema
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
                                Select::make('company_id')
                                    ->label(__('accounts::filament/resources/payment-term.form.sections.fields.company'))
                                    ->relationship('company', 'name')
                                    ->placeholder(__('accounts::filament/resources/payment-term.form.sections.fields.company-placeholder'))
                                    ->searchable()
                                    ->preload()
                                    ->default(current_company_id())
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
                                                    ->resizable(),
                                                TableColumn::make('value_amount')
                                                    ->label(__('accounts::filament/resources/payment-term.form.tabs.due-terms.repeater.due-terms.fields.due'))
                                                    ->resizable(),
                                                TableColumn::make('delay_type')
                                                    ->label(__('accounts::filament/resources/payment-term.form.tabs.due-terms.repeater.due-terms.fields.delay-type'))
                                                    ->resizable(),
                                                TableColumn::make('days_next_month')
                                                    ->label(__('accounts::filament/resources/payment-term.form.tabs.due-terms.repeater.due-terms.fields.days-on-the-next-month'))
                                                    ->toggleable(isToggledHiddenByDefault: true)
                                                    ->resizable(),
                                                TableColumn::make('nb_days')
                                                    ->label(__('accounts::filament/resources/payment-term.form.tabs.due-terms.repeater.due-terms.fields.days'))
                                                    ->resizable(maxWidth: 80),
                                            ])
                                            ->schema([
                                                Select::make('value')
                                                    ->options(DueTermValue::class)
                                                    ->wrapOptionLabels(false)
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
                                                    ->wrapOptionLabels(false)
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
}
