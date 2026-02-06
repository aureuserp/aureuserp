<?php

namespace Webkul\Project\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Webkul\Analytic\Models\Record;

class Timesheet extends Record
{
    protected static function boot()
    {
        parent::boot();

        static::created(function ($timesheet) {
            $timesheet->updateTaskTimes();
        });

        static::updated(function ($timesheet) {
            $timesheet->updateTaskTimes();
        });

        static::deleted(function ($timesheet) {
            $timesheet->updateTaskTimes();
        });
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function updateTaskTimes()
    {
        if (! $this->task) {
            return;
        }

        $task = $this->task;

        $effectiveHours = $hoursSpent = $task->timesheets()->sum('unit_amount');

        if ($task->subTasks->count()) {
            $hoursSpent += $task->subTasks->reduce(function ($carry, $subTask) {
                return $carry + $subTask->timesheets()->sum('unit_amount');
            }, 0);
        }

        $task->update([
            'total_hours_spent' => $hoursSpent,
            'effective_hours'   => $effectiveHours,
            'overtime'          => $hoursSpent > $task->allocated_hours ? $hoursSpent - $task->allocated_hours : 0,
            'remaining_hours'   => $task->allocated_hours - $hoursSpent,
            'progress'          => $task->allocated_hours ? ($hoursSpent / $task->allocated_hours) * 100 : 0,
        ]);

        if ($parentTask = $task->parent) {
            $parentEffectiveHours = $parentHoursSpent = $parentTask->timesheets()->sum('unit_amount');

            $parentHoursSpent += $parentTask->subTasks->reduce(function ($carry, $subTask) {
                return $carry + $subTask->timesheets()->sum('unit_amount');
            }, 0);

            $parentTask->update([
                'total_hours_spent'       => $parentHoursSpent,
                'effective_hours'         => $parentEffectiveHours,
                'subtask_effective_hours' => $parentTask->subTasks->sum('effective_hours'),
                'overtime'                => $parentHoursSpent > $parentTask->allocated_hours ? $parentHoursSpent - $parentTask->allocated_hours : 0,
                'remaining_hours'         => $parentTask->allocated_hours - $parentHoursSpent,
                'progress'                => $parentTask->allocated_hours ? ($parentHoursSpent / $parentTask->allocated_hours) * 100 : 0,
            ]);
        }
    }

    public function updateTaskTimesOld()
    {
        if (! $this->task) {
            return;
        }

        $totalTime = $this->task->timesheets()->sum('unit_amount');

        $this->task->update([
            'total_hours_spent' => $totalTime,
            'effective_hours'   => $totalTime,
            'overtime'          => $totalTime > $this->task->allocated_hours ? $totalTime - $this->task->allocated_hours : 0,
            'remaining_hours'   => $this->task->allocated_hours - $totalTime,
            'progress'          => $this->task->allocated_hours ? ($totalTime / $this->task->allocated_hours) * 100 : 0,
        ]);
    }
}
