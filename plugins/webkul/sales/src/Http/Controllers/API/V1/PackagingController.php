<?php

namespace Webkul\Sale\Http\Controllers\API\V1;

use Knuckles\Scribe\Attributes\Authenticated;
use Knuckles\Scribe\Attributes\Group;
use Knuckles\Scribe\Attributes\Subgroup;
use Webkul\Product\Http\Controllers\API\V1\PackagingController as BasePackagingController;

#[Group('Sales API Management')]
#[Subgroup('Packagings', 'Manage packagings')]
#[Authenticated]
class PackagingController extends BasePackagingController {}
