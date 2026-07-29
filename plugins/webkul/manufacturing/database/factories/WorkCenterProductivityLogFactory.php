<?php

namespace Webkul\Manufacturing\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Manufacturing\Models\WorkCenter;
use Webkul\Manufacturing\Models\WorkCenterProductivityLog;
use Webkul\Manufacturing\Models\WorkCenterProductivityLoss;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;

/**
 * @extends Factory<WorkCenterProductivityLog>
 */
class WorkCenterProductivityLogFactory extends Factory
{
    protected $model = WorkCenterProductivityLog::class;

    public function definition(): array
    {
        return [
            'loss_type'        => 'productive',
            'description'      => null,
            'started_at'       => now(),
            'finished_at'      => now(),
            'duration'         => 0,
            'work_center_id'   => WorkCenter::factory(),
            'company_id'       => Company::query()->value('id') ?? Company::factory(),
            'work_order_id'    => null,
            'assigned_user_id' => User::query()->value('id'),
            'loss_id'          => fn () => WorkCenterProductivityLoss::query()->where('loss_type', 'productive')->value('id'),
            'creator_id'       => User::query()->value('id') ?? User::factory(),
        ];
    }

    public function productive(float $duration): static
    {
        return $this->state(fn () => [
            'loss_type'   => 'productive',
            'started_at'  => now()->subMinutes($duration),
            'finished_at' => now(),
            'duration'    => $duration,
            'loss_id'     => WorkCenterProductivityLoss::query()->where('loss_type', 'productive')->value('id'),
        ]);
    }

    public function performance(float $duration): static
    {
        return $this->state(fn () => [
            'loss_type'   => 'performance',
            'started_at'  => now()->subMinutes($duration),
            'finished_at' => now(),
            'duration'    => $duration,
            'loss_id'     => WorkCenterProductivityLoss::query()->where('loss_type', 'performance')->value('id'),
        ]);
    }
}
