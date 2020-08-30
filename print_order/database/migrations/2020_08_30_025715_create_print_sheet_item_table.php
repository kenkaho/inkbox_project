<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePrintSheetItemTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('print_sheet_item', function (Blueprint $table) {
            $table->increments('psi_id');
            $table->unsignedInteger('ps_id')->index('ps_id');
            $table->unsignedBigInteger('order_item_id')->index('print_sheet_item_ibfk_2');
            $table->enum('status', ['pass', 'reject', 'complete'])->default('pass')->index('status');
            $table->string('image_url');
            $table->string('size');
            $table->integer('x_pos');
            $table->integer('y_pos');
            $table->integer('width');
            $table->integer('height');
            $table->string('identifier');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('print_sheet_item');
    }
}
