<?php

namespace Webkul\Chatter\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Webkul\Chatter\Models\Attachment;

class ChatterCleanupService
{
    /**
     * Chatter tables mapped to the column holding the morph type of the record they belong to.
     */
    protected const MORPH_TYPE_COLUMNS = [
        'chatter_attachments' => 'messageable_type',
        'chatter_messages'    => 'messageable_type',
        'chatter_followers'   => 'followable_type',
    ];

    /**
     * Delete every chatter record (message, activity, follower, attachment) attached to the
     * given models.
     *
     * Called on plugin uninstall: a model's chatter records live in the core chatter tables,
     * so without this they outlive the plugin and resurface against reused ids on reinstall.
     *
     * @param  array<int, class-string>  $models
     */
    public static function purgeForModels(array $models): void
    {
        $types = collect($models)
            ->filter(fn ($model) => is_subclass_of($model, Model::class))
            ->map(fn ($model) => (new $model)->getMorphClass())
            ->unique()
            ->values();

        if ($types->isEmpty()) {
            return;
        }

        foreach (self::MORPH_TYPE_COLUMNS as $table => $typeColumn) {
            if ($table === 'chatter_attachments') {
                // Deleted through Eloquent so the model's `deleted` hook removes the stored files.
                Attachment::whereIn($typeColumn, $types)
                    ->chunkById(100, fn ($attachments) => $attachments->each->delete());

                continue;
            }

            DB::table($table)->whereIn($typeColumn, $types)->delete();
        }
    }
}
