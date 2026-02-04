<?php

namespace Webkul\Sale\Http\Controllers\API\V1;

use Knuckles\Scribe\Attributes\Authenticated;
use Knuckles\Scribe\Attributes\Group;
use Knuckles\Scribe\Attributes\Subgroup;
use Webkul\Support\Http\Controllers\API\V1\CurrencyController as BaseCurrencyController;

#[Group('Sales API Management')]
#[Subgroup('Currencies', 'Manage currencies')]
#[Authenticated]
class CurrencyController extends BaseCurrencyController {}
