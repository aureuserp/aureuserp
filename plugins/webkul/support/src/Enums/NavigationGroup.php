<?php

namespace Webkul\Support\Enums;

use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum NavigationGroup: string implements HasIcon, HasLabel
{
    case Dashboard = 'dashboard';

    case Contact = 'contact';

    case Sale = 'sale';

    case Purchase = 'purchase';

    case Maintenance = 'maintenance';

    case Manufacturing = 'manufacturing';

    case Inventory = 'inventory';

    case Invoice = 'invoice';

    case Accounting = 'accounting';

    case Project = 'project';

    case Employee = 'employee';

    case TimeOff = 'time-off';

    case Recruitment = 'recruitment';

    case Website = 'website';

    case Barcode = 'barcode';

    case Plugin = 'plugin';

    case Setting = 'setting';

    case Help = 'help';

    public function getLabel(): string
    {
        return __('admin.navigation.'.$this->value);
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::Dashboard     => 'icon-dashboard',
            self::Contact       => 'icon-contacts',
            self::Sale          => 'icon-sales',
            self::Purchase      => 'icon-purchases',
            self::Maintenance   => 'icon-maintenance',
            self::Manufacturing => 'icon-manufacturing',
            self::Inventory     => 'icon-inventories',
            self::Invoice       => 'icon-invoices',
            self::Accounting    => 'icon-accounting',
            self::Project       => 'icon-projects',
            self::Employee      => 'icon-employees',
            self::TimeOff       => 'icon-time-offs',
            self::Recruitment   => 'icon-recruitments',
            self::Website       => 'icon-website',
            self::Barcode       => 'icon-barcode',
            self::Plugin        => 'icon-plugin',
            self::Setting       => 'icon-settings',
            self::Help          => 'icon-help',
        };
    }
}
