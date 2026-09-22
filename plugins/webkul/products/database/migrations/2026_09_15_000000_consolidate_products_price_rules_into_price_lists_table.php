<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products_price_rule_items', function (Blueprint $table) {
            $table->dropForeign(['price_rule_id']);
            $table->dropColumn('price_rule_id');

            $table->dropForeign(['base_price_rule_id']);
            $table->dropColumn('base_price_rule_id');

            $table->dropForeign(['product_id']);
            $table->dropForeign(['category_id']);
        });

        Schema::table('products_price_rule_items', function (Blueprint $table) {
            $table->foreignId('product_id')->nullable()->change();
            $table->foreignId('category_id')->nullable()->change();
        });

        Schema::table('products_price_rule_items', function (Blueprint $table) {
            $table->foreign('product_id')->references('id')->on('products_products')->cascadeOnDelete();
            $table->foreign('category_id')->references('id')->on('products_categories')->cascadeOnDelete();

            $table->foreignId('price_list_id')
                ->after('id')
                ->constrained('products_product_price_lists')
                ->cascadeOnDelete();

            $table->foreignId('base_price_list_id')
                ->nullable()
                ->after('price_list_id')
                ->constrained('products_product_price_lists')
                ->nullOnDelete();

            $table->decimal('price_max_margin', 15, 4)->nullable()->default(0)->after('price_min_margin');
        });

        Schema::dropIfExists('products_price_rules');
    }

    public function down(): void
    {
        Schema::create('products_price_rules', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('sort')->nullable();
            $table->foreignId('currency_id')->constrained('currencies')->restrictOnDelete();
            $table->foreignId('company_id')->nullable()->constrained('companies')->nullOnDelete();
            $table->foreignId('creator_id')->nullable()->constrained('users')->nullOnDelete();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::table('products_price_rule_items', function (Blueprint $table) {
            $table->dropForeign(['price_list_id']);
            $table->dropColumn('price_list_id');

            $table->dropForeign(['base_price_list_id']);
            $table->dropColumn('base_price_list_id');

            $table->dropColumn('price_max_margin');

            $table->dropForeign(['product_id']);
            $table->dropForeign(['category_id']);
        });

        Schema::table('products_price_rule_items', function (Blueprint $table) {
            $table->foreign('product_id')->references('id')->on('products_products')->cascadeOnDelete();
            $table->foreign('category_id')->references('id')->on('products_categories')->cascadeOnDelete();

            $table->foreignId('price_rule_id')
                ->nullable()
                ->constrained('products_price_rules')
                ->cascadeOnDelete();

            $table->foreignId('base_price_rule_id')
                ->nullable()
                ->constrained('products_price_rules')
                ->nullOnDelete();
        });
    }
};
