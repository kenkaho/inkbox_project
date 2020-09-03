<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\OrdersItem;
use App\Product;
use DB;

class PrintOrdersController extends Controller
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
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
	    $userId = auth()->user()->id;

	    $orderItems = OrdersItem::where('order_id', $request->order_id)->get();

	    $orderItemsIds = $orderItems->pluck('order_item_id');

	    $printSheetItems = DB::table('print_sheet_item')->whereIn('order_item_id', $orderItemsIds)->get();

	    $sheetItems = [];

	    //Save if no print shee item found
	    if(count($printSheetItems) === 0){
		    $psid = DB::table('print_sheet')->insertGetId(
			    array('type' => 'ecom',
				    'sheet_url' => '')
		    );
		    $orderItemData = $this->buildOrderItemsData($orderItems);

		    $storeOrderItemData = $this->getProductPosition($orderItemData);

		    foreach($storeOrderItemData as $item){
			    $tempSheet = $item;
			    $tempSheet['size'] = $item['width'] ."x" . $item['width'];
			    $sheetItems[] = $tempSheet;
			    $item['ps_id'] = $psid;
			    $item['image_url'] = '';
			    $item['identifier'] = '0';
			    $item['size'] = $item['width'] ."x" . $item['height'];

			    DB::table('print_sheet_item')->insertGetId($item);
		    }

		    $sheets[0] = $sheetItems;


		    return view('prints.index',['sheets' => $sheets]);
	    }
	    else {

		    $printSheetItems = DB::table('print_sheet_item')->whereIn('order_item_id', $orderItemsIds)->get();

		    foreach($printSheetItems as $printSheetItem){
			    $SheetItems[] = ['x_pos' => $printSheetItem->x_pos,
				    'y_pos' => $printSheetItem->y_pos,
				    'width' => $printSheetItem->width,
				    'height' => $printSheetItem->height,
				    'size' => $printSheetItem->size];
		    }

		    $Sheets[0] = $SheetItems;

		    return view('prints.index',['sheets' => $Sheets]);

	    }
	    
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

	private function getAllproductSizeWithId(){
		$products = Product::all();

		foreach($products as $key => $product){
			$productSizes[$products[$key]->product_id] = $products[$key]->size;
		}

		return $productSizes;
	}

	private function orderSheetProductPosition($data){
		$tempGrid = array_fill(0, 10, array_fill(0, 15, ''));
		$result = [];

		foreach ($data as $box){
			for ($i=0; $i <10; $i++){
				for ($j=0; $j<15; $j++) {

					if($box['size'] === '3x3') {

						if ($tempGrid[$i][$j] == ''){
							if(!isset($tempGrid[$i + 2][$j]) ||
								!isset($tempGrid[$i][$j+2]) ||
								!isset($tempGrid[$i + 2][$j + 2])){
								$i = $i + 1;
							}
							else if (
								$tempGrid[$i][$j + 1] === '' &&
								$tempGrid[$i][$j + 2] === '' &&
								$tempGrid[$i + 1][$j] === '' &&
								$tempGrid[$i + 1][$j + 1] === '' &&
								$tempGrid[$i + 1][$j + 2] === '' &&
								$tempGrid[$i + 2][$j] === '' &&
								$tempGrid[$i + 2][$j + 1] === '' &&
								$tempGrid[$i + 2][$j + 2] === ''){

								$tempGrid[$i][$j] = 'x';
								$tempGrid[$i][$j + 1] = 'x';
								$tempGrid[$i][$j + 2] = 'x';
								$tempGrid[$i + 1][$j] = 'x';
								$tempGrid[$i + 1][$j + 1] = 'x';
								$tempGrid[$i + 1][$j + 2] = 'x';
								$tempGrid[$i + 2][$j] = 'x';
								$tempGrid[$i + 2][$j + 1] = 'x';
								$tempGrid[$i + 2][$j + 2] = 'x';

								$result[] = ['x_pos' => $i + 1,
										'y_pos' => $j + 1,
										'width' => 3,
										'height' => 3,
										'order_item_id' => $box['order_item_id']];
								break 2;
							}
						}
					}
				}
			}
		}

		foreach ($data as $box){
			for ($i=0; $i <10; $i++){
				for ($j=0; $j<15; $j++) {

					if($box['size'] === '2x2') {
						if ($tempGrid[$i][$j] == ''){
							if(!isset($tempGrid[$i + 1][$j]) ||
								!isset($tempGrid[$i][$j+1]) ||
								!isset($tempGrid[$i + 1][$j + 1])){
								$i = $i + 1;
							}
							else if ($tempGrid[$i + 1][$j] === '' &&
								$tempGrid[$i][$j + 1] === '' &&
								$tempGrid[$i + 1][$j + 1] === ''){

								$tempGrid[$i][$j] = 'x';
								$tempGrid[$i + 1][$j] = 'x';
								$tempGrid[$i][$j + 1] = 'x';
								$tempGrid[$i + 1][$j + 1] = 'x';

								$result[] = ['x_pos' => $i + 1,
									'y_pos' => $j + 1,
									'width' => 2,
									'height' => 2,
									'order_item_id' => $box['order_item_id']];
								break 2;
							}
						}
					}
				}
			}
		}

		foreach ($data as $box){
			for ($i=0; $i <10; $i++){
				for ($j=0; $j<15; $j++) {

					if($box['size'] === '1x1'){
						if($tempGrid[$i][$j] == '') {
							$tempGrid[$i][$j] = 'x';

							$result[] = ['x_pos' => $i + 1,
								'y_pos' => $j + 1,
								'width' => 1,
								'height' => 1,
								'order_item_id' => $box['order_item_id']];
							break 2;
						}
					}
				}
			}
		}

		return $result;
	}

	private function getProductPosition($items){

		$productSizes = $this->getAllproductSizeWithId();

		$boxes = [];

		foreach($items as $item) {
			$qty = $item['quantity'];
			for ($i=0; $i < $qty; $i++){
				$boxes[] = ['size' => $productSizes[$item['product_id']],
							'order_item_id' => $item['order_item_id']];
			}
		}
		$result = $this->orderSheetProductPosition($boxes);
		return $result;
	}

	private function buildOrderItemsData($data){

		$orderItems = [];

		foreach( $data as $product ){
			if($product->quantity != 0) {

				$orderItems[] = [
					'product_id' => $product->product_id,
					'quantity' => $product->quantity,
					'order_item_id' => $product->order_item_id
				];
			}
		}

		return $orderItems;
	}

}
