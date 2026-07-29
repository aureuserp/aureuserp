<?php

namespace Webkul\Website\Filament\Admin\Pages\Settings;

use UnitEnum;
use Webkul\Website\Filament\Admin\Clusters\PluginSettings;
use Webkul\Website\Filament\Admin\Clusters\Settings\Pages\ManageContacts as BaseManageContacts;

class ManageContacts extends BaseManageContacts
{
    protected static ?string $cluster = PluginSettings::class;

    protected static string|UnitEnum|null $navigationGroup = null;

    protected static ?string $slug = 'manage-contacts';
}
