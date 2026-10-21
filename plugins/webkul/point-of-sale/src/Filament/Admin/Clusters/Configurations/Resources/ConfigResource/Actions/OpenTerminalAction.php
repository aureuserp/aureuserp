<?php

namespace Webkul\PointOfSale\Filament\Admin\Clusters\Configurations\Resources\ConfigResource\Actions;

use Filament\Actions\Action;
use Webkul\PointOfSale\Filament\Pos\Pages\Register;
use Webkul\PointOfSale\Models\Config;

class OpenTerminalAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'point-of-sale.config.open-terminal';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label(__('point-of-sale::filament/admin/clusters/configurations/resources/config/actions/open-terminal.label'))
            ->icon('heroicon-o-play')
            ->action(function (Config $record) {
                return redirect()->to(Register::getUrl(
                    ['config' => $record->getKey()],
                    panel: 'pos',
                ));
            })
            ->visible(fn (Config $record): bool => (bool) $record->is_active);
    }
}
