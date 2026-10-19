<?php

namespace Webkul\Crm\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Webkul\Security\Models\User;
use Webkul\Support\Enums\ActivityChainingType;
use Webkul\Support\Enums\ActivityDecorationType;
use Webkul\Support\Enums\ActivityDelayFrom;
use Webkul\Support\Enums\ActivityDelayUnit;
use Webkul\Support\Enums\ActivityTypeAction;

class ActivityTypeSeeder extends Seeder
{
    /**
     * Seed the default CRM activity types without duplicating them on reinstall.
     */
    public function run(): void
    {
        $creator = User::find(1);

        $activityTypes = [
            ['sort' => 1, 'name' => 'Email', 'icon' => 'heroicon-o-envelope', 'category' => ActivityTypeAction::DEFAULT, 'delay_count' => 0],
            ['sort' => 2, 'name' => 'Call', 'icon' => 'heroicon-o-phone', 'category' => ActivityTypeAction::PHONE_CALL, 'delay_count' => 2],
            ['sort' => 3, 'name' => 'Meeting', 'icon' => 'heroicon-o-users', 'category' => ActivityTypeAction::MEETING, 'delay_count' => 0],
            ['sort' => 4, 'name' => 'To-Do', 'icon' => 'heroicon-o-check-circle', 'category' => ActivityTypeAction::DEFAULT, 'delay_count' => 5],
        ];

        foreach ($activityTypes as $activityType) {
            DB::table('activity_types')->updateOrInsert(
                [
                    'plugin' => 'crm',
                    'name'   => $activityType['name'],
                ],
                [
                    'sort'                   => $activityType['sort'],
                    'delay_count'            => $activityType['delay_count'],
                    'triggered_next_type_id' => null,
                    'default_user_id'        => null,
                    'creator_id'             => $creator?->id,
                    'delay_unit'             => ActivityDelayUnit::DAYS->value,
                    'delay_from'             => ActivityDelayFrom::CURRENT_DATE->value,
                    'icon'                   => $activityType['icon'],
                    'decoration_type'        => ActivityDecorationType::ALERT->value,
                    'chaining_type'          => ActivityChainingType::SUGGEST->value,
                    'category'               => $activityType['category']->value,
                    'summary'                => $activityType['name'],
                    'is_active'              => true,
                    'created_at'             => now(),
                    'updated_at'             => now(),
                ],
            );
        }
    }
}
