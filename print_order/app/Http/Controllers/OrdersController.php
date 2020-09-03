<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Order;
use App\OrdersItem;
use App\Product;
use DB;


class OrdersController extends Controller
{



    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
		//
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
	    $products = Product::all();
        return view('orders.create',['products' => $products]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
	    $user = auth()->user();
	    $productInput = $request->productList;
	    $latestOrder = Order::latest('order_id')->first();
	    $orderNumber = $latestOrder->order_number + 1;
	    $customerId = auth()->user()->id;
	    $orderTotal = $this->calculateOrderTotal($productInput);
	    $orderItemData = $this->buildOrderItemsData($productInput);
	    $orders = Order::where('customer_id', $user->id)->get();

	    if($orderTotal == 0){
			//TODO return an error
	    }

	    $orderData = ['order_number' => $orderNumber,
	                'customer_id' => $customerId,
	                'total_price' => $orderTotal,
	                'order_status' => 'done'];

	    $data = auth()->user()->orders()->create($orderData);
	    $orderId = $data->id;

		if($orderId && count($orderItemData) !== 0){
			$this->saveOrderItems($orderItemData, $orderId);
		}

	    $orderList = [];

	    foreach( $orders as $order ) {

		    $products = DB::table('orders_items')
			    ->join('products', 'products.product_id', '=', 'orders_items.product_id')
			    ->where('orders_items.order_id', $order->order_id)
			    ->get();

		    $orderList[] = [$order,$products];
	    }

	    return redirect('profiles/'.$user->id);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

	private function calculateOrderTotal($data){

		$productPrice = $this->getAllproductPriceWithId();
		$orderTotal = 0;
		foreach( $data as $productId => $productQty){
			$orderTotal += $productPrice[$productId] * $productQty;
		}

		return $orderTotal;
	}

	private function getAllproductPriceWithId(){
		$products = Product::all();

		foreach($products as $key => $product){
			$productPrices[$products[$key]->product_id] = $products[$key]->price;
		}

		return $productPrices;
	}


	private function buildOrderItemsData($data){

		$orderItems = [];

		foreach( $data as $productId => $qty ){
			if($qty != 0) {

				$orderItems[] = [
					'product_id' => $productId,
					'quantity' => $qty
				];
			}
		}

		return $orderItems;
	}

	private function saveOrderItems($data, $order_id){

		foreach($data as $orderItem){
			$orderItem['order_id'] = $order_id;
			OrdersItem::create($orderItem);
		}
	}
}
