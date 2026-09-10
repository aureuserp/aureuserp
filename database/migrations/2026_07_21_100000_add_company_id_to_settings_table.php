<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected array $companyScopedGroups = [
        'accounts_accounts',
        'accounts_taxes',
    ];

    public function up(): void
    {
        if (Schema::hasColumn('settings', 'company_id')) {
            return;
        }

        Schema::table('settings', function (Blueprint $table) {
            $table->foreignId('company_id')
                ->nullable()
                ->after('name')
                ->comment('Company')
                ->constrained('companies')
                ->restrictOnDelete();
        });

        Schema::table('settings', function (Blueprint $table) {
            $table->dropUnique('settings_group_name_unique');
            $table->unique(['group', 'name', 'company_id'], 'settings_group_name_company_unique');
        });

        $defaultCompanyId = $this->defaultCompanyId();

        if ($defaultCompanyId !== null) {
            DB::table('settings')
                ->whereIn('group', $this->companyScopedGroups)
                ->update(['company_id' => $defaultCompanyId]);
        }
    }

    public function down(): void
    {
        if (! Schema::hasColumn('settings', 'company_id')) {
            return;
        }

        DB::table('settings')
            ->whereIn('group', $this->companyScopedGroups)
            ->whereNotIn('company_id', [$this->defaultCompanyId()])
            ->delete();

        Schema::table('settings', function (Blueprint $table) {
            $table->dropUnique('settings_group_name_company_unique');
            $table->dropConstrainedForeignId('company_id');
        });

        Schema::table('settings', function (Blueprint $table) {
            $table->unique(['group', 'name'], 'settings_group_name_unique');
        });
    }

    protected function defaultCompanyId(): ?int
    {
        $payload = DB::table('settings')
            ->where('group', 'general')
            ->where('name', 'default_company_id')
            ->value('payload');

        $decoded = $payload !== null ? json_decode($payload, true) : null;

        if (is_int($decoded) && $decoded > 0) {
            return $decoded;
        }

        return DB::table('companies')->min('id');
    }
};
