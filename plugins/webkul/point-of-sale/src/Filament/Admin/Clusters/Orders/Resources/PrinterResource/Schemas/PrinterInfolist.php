<?php

namespace Webkul\PointOfSale\Filament\Admin\Clusters\Orders\Resources\PrinterResource\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PrinterInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('point-of-sale::filament/admin/clusters/orders/resources/printer.infolist.sections.general.title'))
                    ->schema([
                        TextEntry::make('name')
                            ->label(__('point-of-sale::filament/admin/clusters/orders/resources/printer.infolist.sections.general.entries.name'))
                            ->placeholder('—'),

                        TextEntry::make('printer_type')
                            ->label(__('point-of-sale::filament/admin/clusters/orders/resources/printer.infolist.sections.general.entries.printer-type'))
                            ->placeholder('—'),

                        TextEntry::make('proxy_ip')
                            ->label(__('point-of-sale::filament/admin/clusters/orders/resources/printer.infolist.sections.general.entries.proxy-ip'))
                            ->placeholder('—'),

                        TextEntry::make('company.name')
                            ->label(__('point-of-sale::filament/admin/clusters/orders/resources/printer.infolist.sections.general.entries.company-name'))
                            ->placeholder('—'),
                    ])
                    ->columns(2),
            ]);
    }
}
