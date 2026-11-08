<?php

namespace Webkul\Asset\Observers;

use Illuminate\Validation\ValidationException;
use Webkul\Asset\Enums\AssetStatus;
use Webkul\Asset\Models\Asset;
use Webkul\Asset\Models\AssetMovement;
use Webkul\Employee\Models\Employee;
use Webkul\PluginManager\Package;

class EmployeeObserver
{
    public function updating(Employee $employee): void
    {
        if (! Package::isPluginInstalled('assets')) {
            return;
        }

        if (! $employee->isDirty('is_active') || $employee->is_active) {
            return;
        }

        if (! $this->hasOpenCustody($employee)) {
            return;
        }

        throw ValidationException::withMessages([
            'is_active' => __('assets::observers/employee.deactivation-open-custody', ['employee' => $employee->name]),
        ]);
    }

    public function deleting(Employee $employee): void
    {
        if (! Package::isPluginInstalled('assets')) {
            return;
        }

        if ($this->hasOpenCustody($employee)) {
            throw ValidationException::withMessages([
                'employee_id' => __('assets::observers/employee.has-open-custody', ['employee' => $employee->name]),
            ]);
        }

        if (method_exists($employee, 'isForceDeleting') && $employee->isForceDeleting()) {
            $hasMovementHistory = AssetMovement::withoutGlobalScopes()
                ->where(fn ($q) => $q->where('from_employee_id', $employee->id)->orWhere('to_employee_id', $employee->id))
                ->exists();

            if ($hasMovementHistory) {
                throw ValidationException::withMessages([
                    'employee_id' => __('assets::observers/employee.has-movement-history', ['employee' => $employee->name]),
                ]);
            }
        }
    }

    protected function hasOpenCustody(Employee $employee): bool
    {
        return Asset::withoutGlobalScopes()
            ->where('status', AssetStatus::Held)
            ->where('current_employee_id', $employee->id)
            ->exists();
    }
}
