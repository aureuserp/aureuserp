<?php

namespace Webkul\Sale\Http\Controllers\API\V1;

use Webkul\Account\Http\Controllers\API\V1\ProductController as BaseProductController;
use Knuckles\Scribe\Attributes\Authenticated;
use Knuckles\Scribe\Attributes\Group;
use Knuckles\Scribe\Attributes\Subgroup;

#[Group('Sales API Management')]
#[Subgroup('Products', 'Manage products')]
#[Authenticated]
class ProductController extends BaseProductController
{
}
