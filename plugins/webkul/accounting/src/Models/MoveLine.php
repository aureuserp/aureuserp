<?php

namespace Webkul\Accounting\Models;

use Webkul\Account\Models\MoveLine as BaseMoveLine;

class MoveLine extends BaseMoveLine
{
    public function move()
    {
        return $this->belongsTo(Invoice::class);
    }
}
