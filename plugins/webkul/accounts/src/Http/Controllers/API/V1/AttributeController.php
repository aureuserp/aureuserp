<?php

namespace Webkul\Account\Http\Controllers\API\V1;

use Webkul\Product\Http\Controllers\API\V1\AttributeController as BaseAttributeController;
use Knuckles\Scribe\Attributes\Authenticated;
use Knuckles\Scribe\Attributes\Group;
use Knuckles\Scribe\Attributes\Subgroup;

#[Group('Account API Management')]
#[Subgroup('Attributes', 'Manage product attributes')]
#[Authenticated]
class AttributeController extends BaseAttributeController
{
}
