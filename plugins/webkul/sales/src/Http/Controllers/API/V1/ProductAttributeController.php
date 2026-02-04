<?php

namespace Webkul\Sale\Http\Controllers\API\V1;

use Webkul\Account\Http\Controllers\API\V1\ProductAttributeController as BaseProductAttributeController;
use Knuckles\Scribe\Attributes\Authenticated;
use Knuckles\Scribe\Attributes\Group;
use Knuckles\Scribe\Attributes\Subgroup;

#[Group('Sales API Management')]
#[Subgroup('Product attributes', 'Manage product attributes')]
#[Authenticated]
class ProductAttributeController extends BaseProductAttributeController
{
}
