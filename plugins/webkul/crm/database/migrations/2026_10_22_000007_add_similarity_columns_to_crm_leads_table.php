<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('crm_leads', function (Blueprint $table) {
            $table->string('email_normalized')->nullable()->after('email')->index()->comment('Lower-cased email, used to find similar leads');
            $table->string('phone_sanitized', 32)->nullable()->after('phone')->index()->comment('Phone digits only, used to find similar leads');
        });

        DB::table('crm_leads')->select(['id', 'email', 'phone'])->orderBy('id')->chunkById(500, function ($leads): void {
            foreach ($leads as $lead) {
                $email = strtolower(trim((string) $lead->email));
                $phone = preg_replace('/\D+/', '', (string) $lead->phone);

                DB::table('crm_leads')->where('id', $lead->id)->update([
                    'email_normalized' => $email !== '' ? $email : null,
                    'phone_sanitized'  => strlen($phone) >= 7 ? $phone : null,
                ]);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('crm_leads', function (Blueprint $table) {
            $table->dropColumn(['email_normalized', 'phone_sanitized']);
        });
    }
};
