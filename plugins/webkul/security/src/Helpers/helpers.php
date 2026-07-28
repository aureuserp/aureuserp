<?php

use Webkul\Security\Bouncer;

if (! function_exists('bouncer')) {
    /**
     * Get the Bouncer application instance.
     */
    function bouncer(): Bouncer
    {
        return app('bouncer');
    }
}
