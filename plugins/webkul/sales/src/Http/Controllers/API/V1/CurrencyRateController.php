<?php

namespace Webkul\Sale\Http\Controllers\API\V1;

use Knuckles\Scribe\Attributes\Authenticated;
use Knuckles\Scribe\Attributes\Group;
use Knuckles\Scribe\Attributes\Subgroup;
use Webkul\Support\Http\Controllers\API\V1\CurrencyRateController as BaseCurrencyRateController;

#[Group('Sales API Management')]
#[Subgroup('Currency Rates', 'Manage currency exchange rates')]
#[Authenticated]
class CurrencyRateController extends BaseCurrencyRateController {}
