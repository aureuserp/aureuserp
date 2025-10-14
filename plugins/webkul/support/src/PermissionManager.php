<?php

namespace Webkul\Support;

use BezhanSalleh\FilamentShield\Facades\FilamentShield;
use Illuminate\Support\Str;

class PermissionManager
{
    public function managePermissions(): void
    {
        FilamentShield::buildPermissionKeyUsing(function (string $entity, string $affix, string $subject): string {
            $affix = Str::snake($affix);

            if (
                $entity === 'BezhanSalleh\FilamentShield\Resources\Roles\RoleResource'
                || $entity === 'App\Filament\Resources\RoleResource'
            ) {
                return $affix . '_role';
            }

            if (
                class_exists($entity) 
                && method_exists($entity, 'getModel')
            ) {
                $resourceIdentifier = Str::of($entity)
                    ->afterLast('Resources\\')
                    ->beforeLast('Resource')
                    ->replace('\\', '')
                    ->snake()
                    ->replace('_', '::')
                    ->toString();

                if (in_array(
                    needle: $entity,
                    haystack: $this->getConflictingResources(),
                    strict: true
                )) {
                    $pluginPrefix = '';

                    if (Str::contains($entity, 'Webkul\\')) {
                        $pluginPrefix = Str::of($entity)
                            ->after('Webkul\\')
                            ->before('\\')
                            ->snake()
                            ->toString();
                    }

                    if ($pluginPrefix) {
                        return "{$affix}_{$pluginPrefix}_{$resourceIdentifier}";
                    }
                }

                return "{$affix}_{$resourceIdentifier}";
            }

            if (Str::contains($entity, 'Pages\\')) {
                return 'page_' . Str::snake(class_basename($entity));
            }

            if (
                Str::contains($entity, 'Widgets\\') 
                || Str::endsWith($entity, 'Widget')
            ) {
                return 'widget_' . Str::snake(class_basename($entity));
            }

            return $affix . '_' . Str::snake($subject);
        });
    }

    protected function getConflictingResources(): array
    {
        return [
            'Webkul\Blog\Filament\Admin\Clusters\Configurations\Resources\CategoryResource',
            'Webkul\Blog\Filament\Admin\Clusters\Configurations\Resources\TagResource',
            'Webkul\Employee\Filament\Clusters\Configurations\Resources\ActivityPlanResource',
            'Webkul\Project\Filament\Clusters\Configurations\Resources\TagResource',
            'Webkul\Recruitment\Filament\Clusters\Configurations\Resources\ActivityPlanResource',
            'Webkul\Recruitment\Filament\Clusters\Configurations\Resources\ActivityTypeResource',
            'Webkul\Recruitment\Filament\Clusters\Configurations\Resources\DepartmentResource',
            'Webkul\Recruitment\Filament\Clusters\Configurations\Resources\EmploymentTypeResource',
            'Webkul\Recruitment\Filament\Clusters\Configurations\Resources\JobPositionResource',
            'Webkul\Recruitment\Filament\Clusters\Configurations\Resources\SkillTypeResource',
            'Webkul\Sale\Filament\Clusters\Configuration\Resources\ActivityPlanResource',
            'Webkul\Sale\Filament\Clusters\Configuration\Resources\ActivityTypeResource',
            'Webkul\Sale\Filament\Clusters\Products\Resources\ProductResource',
            'Webkul\Sale\Filament\Clusters\Configuration\Resources\TagResource',
            'Webkul\TimeOff\Filament\Clusters\Configurations\Resources\ActivityTypeResource',
            'Webkul\Inventory\Filament\Clusters\Configurations\Resources\ProductCategoryResource',
            'Webkul\Inventory\Filament\Clusters\Products\Resources\ProductResource',
            'Webkul\Sale\Filament\Clusters\Configuration\Resources\ProductCategoryResource',
            'Webkul\Purchase\Filament\Admin\Clusters\Configurations\Resources\ProductCategoryResource',
            'Webkul\Purchase\Filament\Admin\Clusters\Products\Resources\ProductResource',
            'Webkul\Invoice\Filament\Clusters\Configuration\Resources\ProductCategoryResource',
            'Webkul\Invoice\Filament\Clusters\Configuration\Resources\BankAccountResource',
            'Webkul\Invoice\Filament\Clusters\Vendors\Resources\ProductResource'
        ];
    }
}
