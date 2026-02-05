<?php

namespace Webkul\PluginManager;

use BezhanSalleh\FilamentShield\Facades\FilamentShield;
use Filament\Pages\BasePage as Page;
use Filament\Resources\Resource;
use Filament\Widgets\Widget;
use Illuminate\Support\Str;
use Webkul\Security\Filament\Resources\RoleResource;

class PermissionManager
{
    public function managePermissions(): void
    {
        FilamentShield::buildPermissionKeyUsing(function (string $entity, string $affix, string $subject, string $case, string $separator) {
            $pluginKey = null;

            if (! in_array(
                needle: $entity,
                haystack: [
                    RoleResource::class,
                ],
                strict: true
            )) {
                $pluginKey = Str::of($entity)
                    ->after('\\')
                    ->before('\\')
                    ->snake()
                    ->toString();
            }

            return match (true) {
                is_subclass_of($entity, Resource::class) => Str::of($affix)
                    ->snake()
                    ->when($pluginKey, fn ($str) => $str->append('_')->append($pluginKey))
                    ->append('_')
                    ->append(
                        Str::of($entity)
                            ->afterLast('\\')
                            ->beforeLast('Resource')
                            ->replace('\\', '')
                            ->snake()
                            ->replace('_', '::')
                    )
                    ->toString(),
                is_subclass_of($entity, Page::class) => Str::of('page')
                    ->when($pluginKey, fn ($str) => $str->append('_')->append($pluginKey))
                    ->append(class_basename($entity))
                    ->snake()
                    ->toString(),
                is_subclass_of($entity, Widget::class) => Str::of('widget')
                    ->when($pluginKey, fn ($str) => $str->append('_')->append($pluginKey))
                    ->append(class_basename($entity))
                    ->snake()
                    ->toString(),
                default => Str::of($affix)
                    ->snake()
                    ->when($pluginKey, fn ($str) => $str->append('_')->append($pluginKey))
                    ->append('_')
                    ->append($subject)
                    ->snake()
                    ->toString(),
            };
        });
    }
}
