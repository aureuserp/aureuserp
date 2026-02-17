<?php

namespace App\Providers;

use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\ServiceProvider;
use Webkul\Security\Models\User;
use Illuminate\Support\Facades\Route;
use Illuminate\Routing\Router;
use Illuminate\Routing\PendingResourceRegistration;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(Authenticatable::class, User::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Router::macro('softDeletableApiResource', function ($name, $controller, array $options = []) {
            $this->apiResource($name, $controller, $options);

            $segments = explode('.', $name);

            $path = collect($segments)
                ->map(function ($segment, $index) use ($segments) {
                    if ($index === 0) {
                        return $segment;
                    }

                    $parentParam = str_replace('-', '_', str($segments[$index - 1])->singular()->toString()).'_id';

                    return "{{$parentParam}}/{$segment}";
                })
                ->implode('/');

            $this->post("{$path}/{id}/restore", [$controller, 'restore'])
                ->name("{$name}.restore");

            $this->delete("{$path}/{id}/force", [$controller, 'forceDestroy'])
                ->name("{$name}.force-destroy");
        });

        Fieldset::configureUsing(fn (Fieldset $fieldset) => $fieldset
            ->columnSpanFull());

        Grid::configureUsing(fn (Grid $grid) => $grid
            ->columnSpanFull());

        Section::configureUsing(fn (Section $section) => $section
            ->columnSpanFull());
    }
}
