<?php

use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
	   for($i=0; $i<=50; $i++){
		   DB::table('orders')->insert([
			    'order_number' => $i,
			    'customer_id' => 1,
			    'total_price' => 0,
			    'order_status' => '',
			    'order_status' => 'done',
			    'customer_order_count' => 0,
		    ]);
	    }
    }
}