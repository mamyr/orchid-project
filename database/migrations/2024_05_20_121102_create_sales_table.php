<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->increments('id');
            $table->string('g_number');
            $table->date('date');
            $table->date('last_change_date');
            $table->string('supplier_article');
            $table->string('tech_size');
            $table->string('barcode');
            $table->integer('total_price');
            $table->integer('discount_percent');
            $table->boolean('is_supply');
            $table->boolean('is_realization');
            $table->string('promo_code_discount')->nullable($value = true);
            $table->string('warehouse_name');
            $table->string('country_name');
            $table->string('oblast_okrug_name');
            $table->string('region_name');
            $table->integer('income_id');
            $table->string('sale_id');
            $table->string('odid')->nullable($value = true);
            $table->integer('spp');
            $table->float('for_pay');
            $table->integer('finished_price');
            $table->integer('price_with_disc');
            $table->integer('nm_id');
            $table->string('subject');
            $table->string('category');
            $table->string('brand');
            $table->boolean('is_storno')->nullable($value = true);
            $table->timestamps();
        });

        Schema::create('orders', function (Blueprint $table) {
            $table->increments('id');
            $table->string('g_number');
            $table->date('date');
            $table->date('last_change_date');
            $table->string('supplier_article');
            $table->string('tech_size');
            $table->string('barcode');
            $table->integer('total_price');
            $table->integer('discount_percent');
            $table->string('warehouse_name');
            $table->string('oblast');
            $table->integer('income_id');
            $table->string('odid')->nullable($value = true);
            $table->integer('nm_id');
            $table->string('subject');
            $table->string('category');
            $table->string('brand');
            $table->boolean('is_cancel');
            $table->string('cancel_dt')->nullable($value = true);
            $table->timestamps();
        });

        Schema::create('stocks', function (Blueprint $table) {
            $table->increments('id');
            $table->date('date');
            $table->date('last_change_date');
            $table->string('supplier_article');
            $table->string('tech_size');
            $table->string('barcode');
            $table->integer('quantity');
            $table->boolean('is_supply');
            $table->boolean('is_realization');
            $table->integer('quantity_full');
            $table->string('warehouse_name');
            $table->integer('in_way_to_client');
            $table->integer('in_way_from_client');
            $table->integer('nm_id');
            $table->string('subject');
            $table->string('category');
            $table->string('brand');
            $table->string('sc_code');
            $table->integer('price');
            $table->integer('discount');
            $table->timestamps();
        });

        Schema::create('incomes', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('income_id');
            $table->string('number');
            $table->date('date');
            $table->date('last_change_date');
            $table->string('supplier_article');
            $table->string('tech_size');
            $table->string('barcode');
            $table->integer('quantity');
            $table->integer('total_price');
            $table->date('date_close');
            $table->string('warehouse_name');
            $table->integer('nm_id');
            $table->string('status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sales');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('stocks');
        Schema::dropIfExists('incomes');
    }
}
