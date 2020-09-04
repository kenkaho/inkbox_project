<?php

use Illuminate\Database\Seeder;
use App\Product;

class OrderItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
	    for($i=0; $i<=50; $i++){
		    $orderId = $i+1;

			$pickRandProduct = rand(1,6);
		    $totalOrderPrice = 0;
		    $selectedProductIds = [];

		    //Make sure no duplicate product id is selected
		    $productIds =[1,2,3,4,5,6];
		    $combineProductIds = array_combine($productIds, $productIds);

		    $j=0;

		    while($j != $pickRandProduct) {

			    $pickRandQty = rand(1,20);


			    $selectProductId = array_rand($combineProductIds);
			    unset($combineProductIds[$selectProductId]);

			    DB::table('orders_items')->insert([
				    'order_id' => $orderId,
				    'product_id' => $selectProductId,
				    'quantity' => $pickRandQty,
				    'refund' => 0,
				    'resend_amount' => 0
			    ]);
			    $selectedProductIds[] = ['pid'=>$selectProductId,'qty'=>$pickRandQty];
			    $j++;
		    }

		    $totalOrderPrice = $this->getTotalOrderPrice($selectedProductIds);

		    App\Order::where('order_id', $orderId)->update(['total_price' => $totalOrderPrice]);

	    }
    }

	/**
	 * Get The total price of the order
	 * @param $productData
	 * @return int
	 */
	private function getTotalOrderPrice($productData){

		$totalPrice = 0;
		try {
			$products = Product::all();

		}
		catch (\Exception $ex){
			dd('Exception block', $ex);
		}

		foreach($products as $key => $product){
			$productPrices[$product->product_id] = $product->price;
		}

		foreach($productData as $product){
			$totalPrice += $productPrices[$product['pid']] * $product['qty'];

		}

		return $totalPrice;
	}
}
