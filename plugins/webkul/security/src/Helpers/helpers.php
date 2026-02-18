<?php

if (! function_exists('bouncer')) {
    /**
     * Get the Bouncer application instance.
     */
    function bouncer(): \Webkul\Security\Bouncer
    {
        return app('bouncer');
    }
}
