<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->bigIncrements('product_id', 20);
            $table->string('title', 100)->nullable(false);
            $table->string('vendor', 50)->default(NULL);
            $table->string('type', 25)->default(NULL);
            $table->string('size', 20)->default(NULL);
            $table->float('price')->default(0);
            $table->string('handle', 75)->default(NULL);
            $table->integer('inventory_quantity')->unsigned();
            $table->string('sku', 30)->default(NULL);
            $table->string('design_url')->default(NULL);
            $table->enum('published_state', ['inactive', 'active'])->default('active');
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
        Schema::dropIfExists('products');
    }
}
