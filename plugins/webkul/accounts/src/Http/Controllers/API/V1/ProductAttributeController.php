<?php

namespace Webkul\Account\Http\Controllers\API\V1;

use Webkul\Product\Http\Controllers\API\V1\ProductAttributeController as BaseProductAttributeController;
use Knuckles\Scribe\Attributes\Authenticated;
use Knuckles\Scribe\Attributes\Group;
use Knuckles\Scribe\Attributes\Subgroup;

#[Group('Account API Management')]
#[Subgroup('Product Attributes', 'Manage product attributes')]
#[Authenticated]
class ProductAttributeController extends BaseProductAttributeController
{
}
