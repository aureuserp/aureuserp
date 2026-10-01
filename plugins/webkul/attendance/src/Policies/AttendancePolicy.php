<?php

namespace Webkul\Attendance\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Webkul\Attendance\Models\Attendance;
use Webkul\Security\Enums\PermissionType;
use Webkul\Security\Models\User;

class AttendancePolicy
{
    use HandlesAuthorization;

    /**
     * Company-level access for non-global scopes. The attendance owner is
     * an employee, and employees carry no teams, so the generic team-based
     * group check would crash here. Company membership is the correct
     * boundary; rows without a company stay visible to global users only.
     */
    private function hasCompanyAccess(User $user, Attendance $attendance): bool
    {
        if ($user->resource_permission === PermissionType::GLOBAL) {
            return true;
        }

        return $attendance->company_id !== null
            && $user->allowedCompanies->contains('id', $attendance->company_id);
    }

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_attendance_attendance');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Attendance $attendance): bool
    {
        return $user->can('view_attendance_attendance');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_attendance_attendance');
    }

    /**
     * Determine whether the user can update the model.
     *
     * Open rows (missing check-out) can be completed regardless of age or
     * source. This is deliberate: device-derived rows may stay open for
     * weeks (missed punch, stale sync), and locking them would make the
     * backlog unfixable. Closed manual rows stay editable while their work
     * day is recent (today or yesterday) to fix human errors. Closed device
     * rows are frozen: their origin must never change.
     */
    public function update(User $user, Attendance $attendance): bool
    {
        if (! $user->can('update_attendance_attendance')) {
            return false;
        }

        if (! $this->hasCompanyAccess($user, $attendance)) {
            return false;
        }

        if ($attendance->check_out === null) {
            return true;
        }

        return $attendance->source === 'manual'
            && $attendance->work_date?->gte(now()->subDay()->startOfDay());
    }

    /**
     * Determine whether the user can delete the model.
     *
     * Any manual row can be deleted (device rows never are). Older manual
     * rows are fixed via delete + recreate instead of silent edits, so the
     * correction stays visible in the activity log.
     */
    public function delete(User $user, Attendance $attendance): bool
    {
        if (! $user->can('delete_attendance_attendance')) {
            return false;
        }

        if (! $this->hasCompanyAccess($user, $attendance)) {
            return false;
        }

        return $attendance->source === 'manual';
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_attendance_attendance');
    }
}
