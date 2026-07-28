<?php

namespace Webkul\Maintenance\Database\Seeders;

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
     * Run the database seeds.
     */
    public function run(): void
    {
        $creator = User::find(1);

        $activityTypes = [
            [
                'sort'                   => 1,
                'delay_count'            => 1,
                'triggered_next_type_id' => null,
                'default_user_id'        => null,
                'creator_id'             => $creator?->id,
                'delay_unit'             => ActivityDelayUnit::DAYS->value,
                'delay_from'             => ActivityDelayFrom::CURRENT_DATE->value,
                'icon'                   => 'heroicon-c-wrench',
                'decoration_type'        => ActivityDecorationType::ALERT->value,
                'chaining_type'          => ActivityChainingType::SUGGEST->value,
                'category'               => ActivityTypeAction::DEFAULT->value,
                'name'                   => 'Maintenance Request',
                'summary'                => 'Maintenance Request',
                'plugin'                 => 'maintenance',
                'is_active'              => true,
            ],
        ];

        DB::table('activity_types')->insert($activityTypes);
    }
}
