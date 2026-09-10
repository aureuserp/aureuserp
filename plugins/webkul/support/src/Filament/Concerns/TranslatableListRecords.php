<?php

namespace Webkul\Support\Filament\Concerns;

use LaraZeus\SpatieTranslatable\Resources\Pages\ListRecords\Concerns\Translatable;
use Webkul\Support\Filament\TranslatableContentDriver;

trait TranslatableListRecords
{
    use Translatable;

    public function getFilamentTranslatableContentDriver(): ?string
    {
        return TranslatableContentDriver::class;
    }
}
