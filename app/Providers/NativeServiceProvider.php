<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class NativeServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void {}

    /**
     * @return array<int, class-string<ServiceProvider>>
     */
    public function plugins(): array
    {
        return [];
    }
}
