<?php

namespace Webkul\Accounting\Models;

use Webkul\Account\Models\Journal as BaseJournal;

class Journal extends BaseJournal
{
    public function moveLines()
    {
        return $this->hasMany(MoveLine::class, 'journal_id');
    }
}
