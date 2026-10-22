<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $duplicates = DB::table('products_products as duplicates')
            ->whereNotNull('duplicates.barcode')
            ->where('duplicates.barcode', '<>', '')
            ->whereExists(function ($query) {
                $query
                    ->selectRaw('1')
                    ->from('products_products as originals')
                    ->whereRaw('LOWER(originals.barcode) = LOWER(duplicates.barcode)')
                    ->where(function ($companyQuery) {
                        $companyQuery
                            ->whereColumn('originals.company_id', 'duplicates.company_id')
                            ->orWhere(function ($nullQuery) {
                                $nullQuery
                                    ->whereNull('originals.company_id')
                                    ->whereNull('duplicates.company_id');
                            });
                    })
                    ->whereColumn('originals.id', '<', 'duplicates.id');
            })
            ->orderBy('duplicates.id')
            ->get(['duplicates.id', 'duplicates.company_id', 'duplicates.barcode']);

        foreach ($duplicates->groupBy(fn ($row) => $row->company_id.'|'.strtolower((string) $row->barcode)) as $rows) {
            Log::warning('Cleared duplicate product barcodes to apply the unique (company_id, barcode) constraint.', [
                'company_id'  => $rows->first()->company_id,
                'barcode'     => $rows->first()->barcode,
                'cleared_ids' => $rows->pluck('id')->all(),
            ]);
        }

        DB::table('products_products')
            ->where(function ($query) use ($duplicates) {
                $query
                    ->where('barcode', '')
                    ->orWhereIn('id', $duplicates->pluck('id')->all());
            })
            ->update(['barcode' => null]);

        Schema::table('products_products', function (Blueprint $table) {
            $table->unique(['company_id', 'barcode']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (in_array(DB::getDriverName(), ['mysql', 'mariadb'], true)) {
            Schema::table('products_products', function (Blueprint $table) {
                $table->index('company_id', 'products_products_company_id_foreign');
            });
        }

        Schema::table('products_products', function (Blueprint $table) {
            $table->dropUnique(['company_id', 'barcode']);
        });
    }
};
