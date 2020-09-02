<?php

namespace App\Http\Controllers;

use App\OrdersItem;
use Illuminate\Http\Request;
use App\Product;
use App\Order;

class OrdersController extends Controller
{

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
	    $productInput = $request->productList;
	    $latestOrder = Order::latest('order_id')->first();
	    $orderNumber = $latestOrder->order_number + 1;
	    $customerId = auth()->user()->id;
	    $orderTotal = $this->calculateOrderTotal($productInput);

	    if($orderTotal == 0){
			//TODO return an error
	    }

	    $orderData = ['order_number' => $orderNumber,
	                'customer_id' => $customerId,
	                'total_price' => $orderTotal,
	                'order_status' => 'done'];

	    $data = auth()->user()->orders()->create($orderData);
	    $orderId = $data->id;

	    foreach( $productInput as $productId => $qty ){
		    if($qty != 0) {

			    $orderItems = ['order_id' => $orderId,
				    'product_id' => $productId,
				    'quantity' => $qty];
			    OrdersItem::create($orderItems);
		    }
	    }
	   return view('profiles.index', ['user' => auth()->user()]);
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
}
