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
        DB::statement("
            UPDATE partners_partners p
            LEFT JOIN (
                SELECT
                    partner_id,
                    COUNT(*) AS total
                FROM accounts_account_moves
                WHERE partner_id IS NOT NULL
                  AND move_type IN ('out_invoice', 'out_refund')
                GROUP BY partner_id
            ) m ON m.partner_id = p.id
            SET p.customer_rank = COALESCE(m.total, 0)
        ");

        DB::statement("
            UPDATE partners_partners p
            LEFT JOIN (
                SELECT
                    partner_id,
                    COUNT(*) AS total
                FROM accounts_account_moves
                WHERE partner_id IS NOT NULL
                  AND move_type IN ('in_invoice', 'in_refund')
                GROUP BY partner_id
            ) m ON m.partner_id = p.id
            SET p.supplier_rank = COALESCE(m.total, 0)
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("
            UPDATE partners_partners
            SET customer_rank = NULL,
                supplier_rank = NULL
        ");
    }
};
