<?php

namespace Webkul\Support\Filament\Resources;

use Filament\Resources\Resource;
use Webkul\Support\Models\Country;

class CountryResource extends Resource
{
    protected static ?string $model = Country::class;

    protected static bool $shouldRegisterNavigation = false;

    protected static bool $isGloballySearchable = false;

    protected static ?string $recordTitleAttribute = 'name';
}
