<?php

namespace Webkul\Barcode\Support;

class Navigation
{
    /**
     * @return array<int, array{
     *     id: string,
     *     label: string,
     *     icon: string,
     *     native_icon: string,
     *     href: string|null,
     *     active: bool,
     *     disabled: bool
     * }>
     */
    public static function items(): array
    {
        $currentRoute = (string) request()->route()?->getName();

        return [
            [
                'id'          => 'inventory-operations',
                'label'       => 'Inventory Operations',
                'icon'        => 'heroicon-m-arrows-right-left',
                'native_icon' => 'swap_horiz',
                'href'        => NativeApp::navigationUrl('barcode.dashboard'),
                'active'      => str_starts_with($currentRoute, 'barcode.dashboard')
                    || str_starts_with($currentRoute, 'barcode.transfers')
                    || str_starts_with($currentRoute, 'barcode.operation'),
                'disabled' => false,
            ],
            [
                'id'          => 'manufacturing-orders',
                'label'       => 'Manufacturing Orders',
                'icon'        => 'heroicon-m-wrench-screwdriver',
                'native_icon' => 'build',
                'href'        => Manufacturing::isAvailable() ? NativeApp::navigationUrl('barcode.manufacturing-orders') : null,
                'active'      => str_starts_with($currentRoute, 'barcode.manufacturing-order'),
                'disabled'    => ! Manufacturing::isAvailable(),
            ],
            [
                'id'          => 'inventory-adjustments',
                'label'       => 'Inventory Adjustments',
                'icon'        => 'heroicon-m-clipboard-document-list',
                'native_icon' => 'inventory_2',
                'href'        => NativeApp::navigationUrl('barcode.adjustments'),
                'active'      => str_starts_with($currentRoute, 'barcode.adjustments'),
                'disabled'    => false,
            ],
        ];
    }
}
