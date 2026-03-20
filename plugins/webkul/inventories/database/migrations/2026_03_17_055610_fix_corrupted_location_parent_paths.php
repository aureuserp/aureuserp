<?php

use Illuminate\Database\Migrations\Migration;
use Webkul\Inventory\Models\Location;

return new class extends Migration
{
    /**
     * Repair location rows whose parent_path was generated before the record
     * had an ID (i.e. the path is null or contains only slashes like '//').
     * Process in ascending ID order so parents are fixed before their children.
     */
    public function up(): void
    {
        Location::withTrashed()
            ->orderBy('id')
            ->each(function (Location $location): void {
                if (trim($location->parent_path ?? '', '/') === '') {
                    $location->updateParentPath();
                    $location->updateFullName();
                    $location->saveQuietly();
                }
            });
    }

    public function down(): void
    {
        // Irreversible data repair — no rollback needed.
    }
};
