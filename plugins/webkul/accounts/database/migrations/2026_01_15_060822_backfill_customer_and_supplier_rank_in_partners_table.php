<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Uses a correlated subquery (rather than MySQL's UPDATE...JOIN...SET) so the
        // same statement is portable across MySQL and PostgreSQL.
        DB::statement("
            UPDATE partners_partners p
            SET customer_rank = COALESCE((
                SELECT COUNT(*)
                FROM accounts_account_moves m
                WHERE m.partner_id = p.id
                  AND m.move_type IN ('out_invoice', 'out_refund')
            ), 0)
        ");

        DB::statement("
            UPDATE partners_partners p
            SET supplier_rank = COALESCE((
                SELECT COUNT(*)
                FROM accounts_account_moves m
                WHERE m.partner_id = p.id
                  AND m.move_type IN ('in_invoice', 'in_refund')
            ), 0)
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('
            UPDATE partners_partners
            SET customer_rank = NULL,
                supplier_rank = NULL
        ');
    }
};
