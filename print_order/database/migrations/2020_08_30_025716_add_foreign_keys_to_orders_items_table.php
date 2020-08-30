<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToOrdersItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders_items', function (Blueprint $table) {
            $table->foreign('order_id', 'orders_items_ibfk_1')->references('order_id')->on('orders')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign('product_id', 'orders_items_ibfk_2')->references('product_id')->on('products')->onUpdate('NO ACTION')->onDelete('NO ACTION');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders_items', function (Blueprint $table) {
            $table->dropForeign('orders_items_ibfk_1');
            $table->dropForeign('orders_items_ibfk_2');
        });
    }
}
