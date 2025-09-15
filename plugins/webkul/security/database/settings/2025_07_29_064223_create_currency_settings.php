<?php

use Illuminate\Support\Facades\Schema;
use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $this->migrator->add('currency.default_currency_id', null);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('settings', 'default_currency_id')) {
            $this->migrator->delete('currency.default_currency_id');
        }
    }
};
