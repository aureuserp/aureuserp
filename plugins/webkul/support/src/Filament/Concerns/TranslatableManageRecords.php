<?php

namespace Webkul\Support\Filament\Concerns;

use LaraZeus\SpatieTranslatable\Resources\Pages\ManageRecords\Concerns\Translatable;
use Webkul\Support\Filament\TranslatableContentDriver;

trait TranslatableManageRecords
{
    use Translatable;

    public function getFilamentTranslatableContentDriver(): ?string
    {
        return TranslatableContentDriver::class;
    }
}
