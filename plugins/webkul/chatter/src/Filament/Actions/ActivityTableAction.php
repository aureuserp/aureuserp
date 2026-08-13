<?php

namespace Webkul\Chatter\Filament\Actions;

use Webkul\Chatter\Filament\Actions\Chatter\ActivityAction;

class ActivityTableAction extends ActivityAction
{
    public static function getDefaultName(): ?string
    {
        return 'chatter.schedule-activity';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->iconButton()
            ->hiddenLabel()
            ->color('gray')
            ->icon('heroicon-o-clock');
    }
}
