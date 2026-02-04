<?php

namespace Webkul\Sale\Http\Controllers\API\V1;

use Webkul\Account\Http\Controllers\API\V1\AttributeOptionController as BaseAttributeOptionController;
use Knuckles\Scribe\Attributes\Authenticated;
use Knuckles\Scribe\Attributes\Group;
use Knuckles\Scribe\Attributes\Subgroup;

#[Group('Sales API Management')]
#[Subgroup('Attribute options', 'Manage attribute options')]
#[Authenticated]
class AttributeOptionController extends BaseAttributeOptionController
{
}
