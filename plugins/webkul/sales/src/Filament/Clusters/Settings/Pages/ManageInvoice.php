<?php

namespace Webkul\Sale\Filament\Clusters\Settings\Pages;

use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;
use Webkul\Sale\Settings\InvoiceSettings;
use Webkul\Support\Filament\Clusters\Settings;

class ManageInvoice extends SettingsPage
{
    use HasPageShield;

    protected static ?string $navigationIcon = 'heroicon-o-cube';

    protected static ?string $slug = 'sale/manage-invoicing';

    protected static ?string $navigationGroup = 'Sales';

    protected static ?int $navigationSort = 2;

    protected static string $settings = InvoiceSettings::class;

    protected static ?string $cluster = Settings::class;

    public function getBreadcrumbs(): array
    {
        return [
            __('Manage Invoice'),
        ];
    }

    public function getTitle(): string
    {
        return __('Manage Invoice');
    }

    public static function getNavigationLabel(): string
    {
        return __('Manage Invoice');
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Radio::make('invoice_policy')
                    ->options([
                        'order' => 'Invoice what is ordered',
                        'delivery' => 'Invoice what is delivered',
                    ])
                    ->default('delivery')
                    ->label(__('Invoice Policy'))
                    ->helperText(__('Quantities to invoice from sales orders.')),
            ]);
    }
}
