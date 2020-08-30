<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->bigIncrements('order_id');
            $table->unsignedBigInteger('order_number')->index('order_number');
            $table->unsignedBigInteger('customer_id')->nullable()->index('customer_id');
            $table->float('total_price', 10, 0)->nullable()->default(0);
            $table->string('fulfillment_status', 25)->nullable()->index('fulfillment_status');
            $table->timestamp('fulfilled_date')->nullable();
            $table->enum('order_status', ['pending', 'active', 'done', 'cancelled', 'resend'])->nullable();
            $table->integer('customer_order_count')->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent()->index('created_at');
            $table->timestamp('updated_at')->useCurrent()->index('updated_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
}
