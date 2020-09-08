<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Order;
use App\OrdersItem;
use App\Product;
use DB;


class OrdersController extends Controller
{

	public function __construct()
	{
		$this->middleware('auth');
	}

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
	    $customerId = $user->id;
	    $orderTotal = $this->calculateOrderTotal($productInput);
	    $orderItemData = $this->buildOrderItemsData($productInput);

	    try {

		    if(count(Order::all()) === 0){
			    $order_number = '000001';
		    }
		    else{
			    $latestOrder = Order::latest('order_id')->first();
			    $order_number = $latestOrder->order_id + 1;
		    }

	    }
	    catch (\Exception $ex){
		    dd('Exception block', $ex);
	    }

	    try {
		    $orders = Order::where('customer_id', $customerId)->get();
	    }
	    catch (\Exception $ex){
		    dd('Exception block', $ex);
	    }

	    if($orderTotal == 0){
			//TODO return an error
	    }

	    $orderData = ['order_number' => $order_number,
	                'customer_id' => $customerId,
	                'total_price' => $orderTotal,
	                'order_status' => 'done'];
	    try {
		    $data = $user->orders()->create($orderData);
	    }
	    catch (\Exception $ex){
		    dd('Exception block', $ex);
	    }

	    $orderId = $data->id;

		if($orderId && count($orderItemData) !== 0){
			try {
				$this->saveOrderItems($orderItemData, $orderId);
			}
			catch (\Exception $ex){
				dd('Exception block', $ex);
			}
		}

	    $orderList = [];

	    foreach( $orders as $order ) {

		    try {
			    $products = DB::table('orders_items')
				    ->join('products', 'products.product_id', '=', 'orders_items.product_id')
				    ->where('orders_items.order_id', $order->order_id)
				    ->get();

			    $orderList[] = [$order, $products];
		    }
		    catch (\Exception $ex){
			    dd('Exception block', $ex);
		    }
	    }

	    return redirect('profiles');
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

		try {
			$productPrice = $this->getAllproductPriceWithId();
		}
		catch (\Exception $ex){
			dd('Exception block', $ex);
		}

		$orderTotal = 0;
		foreach( $data as $productId => $productQty){
			$orderTotal += $productPrice[$productId] * $productQty;
		}

		return $orderTotal;
	}

	private function getAllproductPriceWithId(){

		try {
			$products = Product::all();
		}
		catch (\Exception $ex){
			dd('Exception block', $ex);
		}

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
			try {
				$orderItem['order_id'] = $order_id;
				OrdersItem::create($orderItem);
			}
			catch (\Exception $ex){
				dd('Exception block', $ex);
			}
		}
	}
}
