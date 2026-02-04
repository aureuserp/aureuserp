<?php

namespace Webkul\Account\Http\Controllers\API\V1;

use Webkul\Product\Http\Controllers\API\V1\TagController as BaseTagController;
use Knuckles\Scribe\Attributes\Authenticated;
use Knuckles\Scribe\Attributes\Group;
use Knuckles\Scribe\Attributes\Subgroup;

#[Group('Account API Management')]
#[Subgroup('Tags', 'Manage product tags')]
#[Authenticated]
class TagController extends BaseTagController
{
}
