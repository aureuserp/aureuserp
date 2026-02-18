<?php

namespace Webkul\Sale\Http\Controllers\API\V1;

use Webkul\Account\Http\Controllers\API\V1\ProductVariantController as BaseProductVariantController;
use Knuckles\Scribe\Attributes\Authenticated;
use Knuckles\Scribe\Attributes\Group;
use Knuckles\Scribe\Attributes\Subgroup;

#[Group('Sales API Management')]
#[Subgroup('Product variants', 'Manage product variants')]
#[Authenticated]
class ProductVariantController extends BaseProductVariantController
{
}
