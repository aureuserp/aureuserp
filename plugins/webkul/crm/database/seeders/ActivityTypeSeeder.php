<?php

namespace Webkul\Crm\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Webkul\Security\Models\User;
use Webkul\Support\Enums\ActivityChainingType;
use Webkul\Support\Enums\ActivityDecorationType;
use Webkul\Support\Enums\ActivityDelayFrom;
use Webkul\Support\Enums\ActivityDelayUnit;
use Webkul\Support\Enums\ActivityTypeAction;

class ActivityTypeSeeder extends Seeder
{
    public function run(): void
    {
        $this->mergeCopiesOfSharedTypes();

        if (DB::table('activity_types')->where('plugin', 'support')->where('name', 'Email')->exists()) {
            return;
        }

        DB::table('activity_types')->updateOrInsert(
            [
                'plugin' => 'crm',
                'name'   => 'Email',
            ],
            [
                'sort'                   => 1,
                'delay_count'            => 0,
                'triggered_next_type_id' => null,
                'default_user_id'        => null,
                'creator_id'             => User::find(1)?->id,
                'delay_unit'             => ActivityDelayUnit::DAYS->value,
                'delay_from'             => ActivityDelayFrom::CURRENT_DATE->value,
                'icon'                   => 'heroicon-o-envelope',
                'decoration_type'        => ActivityDecorationType::ALERT->value,
                'chaining_type'          => ActivityChainingType::SUGGEST->value,
                'category'               => ActivityTypeAction::DEFAULT->value,
                'summary'                => 'Email',
                'is_active'              => true,
                'created_at'             => now(),
                'updated_at'             => now(),
            ],
        );
    }

    protected function mergeCopiesOfSharedTypes(): void
    {
        $shared = DB::table('activity_types')->where('plugin', 'support')->whereNull('deleted_at')->pluck('id', 'name');

        $copies = DB::table('activity_types')->where('plugin', 'crm')->whereIn('name', $shared->keys())->pluck('name', 'id');

        foreach ($copies as $copyId => $name) {
            $sharedId = $shared[$name];

            DB::table('chatter_messages')->where('activity_type_id', $copyId)->update(['activity_type_id' => $sharedId]);
            DB::table('activity_plan_templates')->where('activity_type_id', $copyId)->update(['activity_type_id' => $sharedId]);
            DB::table('activity_types')->where('triggered_next_type_id', $copyId)->update(['triggered_next_type_id' => $sharedId]);

            if (Schema::hasTable('crm_activity_logs')) {
                DB::table('crm_activity_logs')->where('activity_type_id', $copyId)->update(['activity_type_id' => $sharedId]);
            }

            DB::table('activity_type_suggestions')
                ->where('activity_type_id', $copyId)
                ->orWhere('suggested_activity_type_id', $copyId)
                ->delete();

            DB::table('activity_types')->where('id', $copyId)->delete();
        }
    }
}
