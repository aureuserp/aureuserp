<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('point_of_sale_account.receivable_account_id', null);
        $this->migrator->add('point_of_sale_account.stock_output_account_id', null);
        $this->migrator->add('point_of_sale_account.balancing_account_id', null);
        $this->migrator->add('point_of_sale_account.enable_cogs', false);
        $this->migrator->add('point_of_sale_account.allow_balancing_line', true);
    }

    public function down(): void
    {
        $this->migrator->deleteIfExists('point_of_sale_account.receivable_account_id');
        $this->migrator->deleteIfExists('point_of_sale_account.stock_output_account_id');
        $this->migrator->deleteIfExists('point_of_sale_account.balancing_account_id');
        $this->migrator->deleteIfExists('point_of_sale_account.enable_cogs');
        $this->migrator->deleteIfExists('point_of_sale_account.allow_balancing_line');
    }
};
