<?php

namespace Webkul\PointOfSale\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Webkul\PointOfSale\Models\Session;

class SessionClosed
{
    use Dispatchable, SerializesModels;

    public function __construct(public Session $session) {}
}
