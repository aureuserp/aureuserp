<?php

namespace Webkul\Accounting\Filament\Clusters\Configuration\Resources;

use Filament\Pages\Page;
use Webkul\Account\Filament\Resources\FiscalPositionResource as BaseFiscalPositionResource;
use Webkul\Accounting\Filament\Clusters\Configuration;
use Webkul\Accounting\Filament\Clusters\Configuration\Resources\FiscalPositionResource\Pages\CreateFiscalPosition;
use Webkul\Accounting\Filament\Clusters\Configuration\Resources\FiscalPositionResource\Pages\EditFiscalPosition;
use Webkul\Accounting\Filament\Clusters\Configuration\Resources\FiscalPositionResource\Pages\ListFiscalPositions;
use Webkul\Accounting\Filament\Clusters\Configuration\Resources\FiscalPositionResource\Pages\ViewFiscalPosition;
use Webkul\Accounting\Models\FiscalPosition;

class FiscalPositionResource extends BaseFiscalPositionResource
{
    protected static ?string $model = FiscalPosition::class;

    protected static bool $shouldRegisterNavigation = true;

    protected static ?int $navigationSort = 6;

    protected static ?string $cluster = Configuration::class;

    public static function getModelLabel(): string
    {
        return __('accounting::filament/clusters/configurations/resources/fiscal-position.model-label');
    }

    public static function getNavigationLabel(): string
    {
        return __('accounting::filament/clusters/configurations/resources/fiscal-position.navigation.title');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('accounting::filament/clusters/configurations/resources/fiscal-position.navigation.group');
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            ViewFiscalPosition::class,
            EditFiscalPosition::class,
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListFiscalPositions::route('/'),
            'create' => CreateFiscalPosition::route('/create'),
            'edit'   => EditFiscalPosition::route('/{record}/edit'),
            'view'   => ViewFiscalPosition::route('/{record}'),
        ];
    }
}
