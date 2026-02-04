<?php

namespace Webkul\Account\Http\Controllers\API\V1;

use Webkul\Product\Http\Controllers\API\V1\AttributeOptionController as BaseAttributeOptionController;
use Knuckles\Scribe\Attributes\Authenticated;
use Knuckles\Scribe\Attributes\Group;
use Knuckles\Scribe\Attributes\Subgroup;

#[Group('Account API Management')]
#[Subgroup('Attribute Options', 'Manage attribute options')]
#[Authenticated]
class AttributeOptionController extends BaseAttributeOptionController
{
}
