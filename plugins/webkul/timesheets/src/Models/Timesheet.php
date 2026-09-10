<?php

namespace Webkul\Timesheet\Models;

use Webkul\Field\Traits\HasCustomFields;
use Webkul\Project\Models\Timesheet as BaseTimesheet;

class Timesheet extends BaseTimesheet
{
    use HasCustomFields;
}
