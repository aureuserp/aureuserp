<?php

namespace Webkul\Sale\Filament\Clusters\Settings\Pages;

use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;
use Webkul\Sale\Settings\QuotationAndOrderSettings;
use Webkul\Support\Filament\Clusters\Settings;

class ManageQuotationAndOrder extends SettingsPage
{
    use HasPageShield;

    protected static ?string $navigationIcon = 'heroicon-o-cube';

    protected static ?string $slug = 'sale/manage-quotation-and-order';

    protected static ?string $navigationGroup = 'Sales';

    protected static ?int $navigationSort = 2;

    protected static string $settings = QuotationAndOrderSettings::class;

    protected static ?string $cluster = Settings::class;

    public function getBreadcrumbs(): array
    {
        return [
            __('Manage Quotation & Order'),
        ];
    }

    public function getTitle(): string
    {
        return __('Manage Quotation & Order');
    }

    public static function getNavigationLabel(): string
    {
        return __('Manage Quotation & Order');
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('default_quotation_validity')
                    ->suffix('Days')
                    ->default(30)
                    ->label(__('Default Quotation Validity'))
                    ->helperText(__('Default period during which the quote is valid and can still be accepted by the customer. The default can be changed per order or template.')),
                Forms\Components\Toggle::make('enable_lock_confirm_sales')
                    ->label(__('Lock Confirmed Sales'))
                    ->helperText(__('No longer edit orders once confirmed.')),
            ]);
    }
}
