<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToPrintSheetItemTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('print_sheet_item', function (Blueprint $table) {
            $table->foreign('ps_id', 'print_sheet_item_ibfk_1')->references('ps_id')->on('print_sheet')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign('order_item_id', 'print_sheet_item_ibfk_2')->references('order_item_id')->on('orders_items')->onUpdate('NO ACTION')->onDelete('NO ACTION');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('print_sheet_item', function (Blueprint $table) {
            $table->dropForeign('print_sheet_item_ibfk_1');
            $table->dropForeign('print_sheet_item_ibfk_2');
        });
    }
}
