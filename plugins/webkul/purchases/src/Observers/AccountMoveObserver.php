<?php

namespace Webkul\Purchase\Observers;
 
use Webkul\Account\Models\Move;
use Webkul\Purchase\Models\AccountMove;
 
class AccountMoveObserver
{
    /**
     * Handle the User "updated" event.
     */
    public function updated(Move $move): void
    {
        if ($move->isDirty('state')) {
            $accountMove = AccountMove::find($move->id);

            $oldValue = $move->getOriginal('state');
            $newValue = $move->state;

            dd($accountMove, $oldValue, $newValue);
        }
    }
}