<?php

namespace Webkul\Sale\Http\Controllers\API\V1;

use Webkul\Account\Http\Controllers\API\V1\AttributeController as BaseAttributeController;
use Knuckles\Scribe\Attributes\Authenticated;
use Knuckles\Scribe\Attributes\Group;
use Knuckles\Scribe\Attributes\Subgroup;

#[Group('Sales API Management')]
#[Subgroup('Attributes', 'Manage attributes')]
#[Authenticated]
class AttributeController extends BaseAttributeController
{
}
