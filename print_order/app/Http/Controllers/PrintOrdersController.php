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
	    try {
		    $orderItems = OrdersItem::where('order_id', $request->order_id)->get();
	    }
	    catch (\Exception $ex){
		    dd('Exception block', $ex);
	    }

	    $orderNumber = $request->order_number;

	    $orderItemsIds = $orderItems->pluck('order_item_id');

	    try {
		    $printSheetItems = DB::table('print_sheet_item')->whereIn('order_item_id', $orderItemsIds)->get();
	    }
	    catch (\Exception $ex){
		    dd('Exception block', $ex);
	    }

	    $sheetItems = [];

	    //Save record if no print sheet item is found
	    if(count($printSheetItems) === 0){

		    try {
			    $psid = DB::table('print_sheet')->insertGetId(
				    array('type' => 'ecom',
					    'sheet_url' => '')
			    );
		    }
		    catch (\Exception $ex){
			    dd('Exception block', $ex);
		    }

		    $orderItemData = $this->buildOrderItemsData($orderItems);

		    $sheets = $this->buildPrintSheetData($orderItemData);
		    $identifier = 0;

		    foreach ($sheets as $key => $sheet) {

			    foreach ($sheet as $printSheetItem) {

				    $productTitle = $this->getProductTitleByOrderItemId($printSheetItem['order_item_id']);

				    $tempSheet = $printSheetItem;
				    $tempSheet['size'] = $printSheetItem['width'] . "x" . $printSheetItem['height'];
				    $tempSheet['productTitle'] = $productTitle;

				    $sheetItems[] = $tempSheet;

				    $printSheetItem['ps_id'] = $psid;
				    $printSheetItem['image_url'] = '';
				    $printSheetItem['identifier'] = $identifier;
				    $printSheetItem['size'] = $tempSheet['size'];

				    try {
					    DB::table('print_sheet_item')->insertGetId($printSheetItem);
				    }
				    catch (\Exception $ex){
					    dd('Exception block', $ex);
				    }
			    }
			    $identifier++;
			    $sheetsResult[] = $sheetItems;
		    }

		    return view('prints.index',['sheets' => $sheetsResult, 'orderNumber' => $orderNumber]);
	    }
	    else {

			try {
				$printSheetItems = DB::table('print_sheet_item')->join('orders_items', 'print_sheet_item.order_item_id', '=', 'orders_items.order_item_id')->whereIn('print_sheet_item.order_item_id', $orderItemsIds)->get();

			}
			catch (\Exception $ex){
				dd('Exception block', $ex);
			}

		    foreach($printSheetItems as $printSheetItem){

			    $productTitle = $this->getProductTitleById($printSheetItem->product_id);

			    $Sheets[$printSheetItem->identifier][] = ['x_pos' => $printSheetItem->x_pos,
				    'y_pos' => $printSheetItem->y_pos,
				    'width' => $printSheetItem->width,
				    'height' => $printSheetItem->height,
				    'size' => $printSheetItem->size,
			        'productTitle' => $productTitle];
		    }

		    return view('prints.index',['sheets' => $Sheets, 'orderNumber' => $orderNumber]);
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
		try {
			$products = Product::all();
		}
		catch (\Exception $ex){
			dd('Exception block', $ex);
		}

		foreach($products as $key => $product){
			$productSizes[$products[$key]->product_id] = $products[$key]->size;
		}

		return $productSizes;
	}

	private function getProductTitleById($id){

		try {
			$product = Product::where('product_id', '=', $id)->firstOrFail();
		}
		catch (\Exception $ex){
			dd('Exception block', $ex);
		}

		$productTitle = $product->title;

		return $productTitle;
	}

	/**
	 *
	 * This function will construct the print sheet items data with the x_pos, y_pos, width and height for each
	 * print sheet item to be insert into the print_sheet_item table
	 *
	 * The idea: To insert the largest boxes first then work all the way down to the smaller boxes since the smaller
	 * boxes will be easier to find a spot to fit in.  This function will also automatically put the remaining boxes
	 * into a new sheet if the previous sheet can not fit the rest of the boxes.
	 *
	 * This algorithm can be improve more to have less loops and code, but with the time I have just come up with
	 * this naive solution but it seems it will cover most of the cases.
	 *
	 * @param $data
	 * @return array
	 */

	private function orderSheetProductPosition($data){
		$tempGrid = array_fill(0, 10, array_fill(0, 15, ''));
		$result = [];
		$remainingBox = $data;


		foreach ($data as $key => $box){
			for ($i=0; $i <10; $i++){
				for ($j=0; $j<15; $j++) {

					if($box['size'] === '2x5') {

						if (isset($tempGrid[$i][$j]) && $tempGrid[$i][$j] == '' ){
							if(!isset($tempGrid[$i][$j+4]) ||
								!isset($tempGrid[$i+1][$j]) ||
								!isset($tempGrid[$i+4][$j+4])){
								$i = $i + 1;
							}
							else if (
								$tempGrid[$i + 1][$j] === '' &&
								$tempGrid[$i + 2][$j] === '' &&
								$tempGrid[$i + 3][$j] === '' &&
								$tempGrid[$i + 4][$j] === '' &&
								$tempGrid[$i][$j + 1] === '' &&
								$tempGrid[$i + 1][$j + 1] === '' &&
								$tempGrid[$i + 2][$j + 1] === '' &&
								$tempGrid[$i + 3][$j + 1] === '' &&
								$tempGrid[$i + 4][$j + 1] === ''){

								$tempGrid[$i][$j] = 'x';
								$tempGrid[$i + 1][$j] = 'x';
								$tempGrid[$i + 2][$j] = 'x';
								$tempGrid[$i + 3][$j] = 'x';
								$tempGrid[$i + 4][$j] = 'x';
								$tempGrid[$i][$j + 1] = 'x';
								$tempGrid[$i + 1][$j + 1] = 'x';
								$tempGrid[$i + 2][$j + 1] = 'x';
								$tempGrid[$i + 3][$j + 1] = 'x';
								$tempGrid[$i + 4][$j + 1] = 'x';

								$result[] = ['x_pos' => $i + 1,
									'y_pos' => $j + 1,
									'width' => 5,
									'height' => 2,
									'order_item_id' => $box['order_item_id']];
								unset($remainingBox[$key]);
								break 2;
							}
						}
					}
				}
			}
		}

		foreach ($data as $key => $box){
			for ($i=0; $i <10; $i++){
				for ($j=0; $j<15; $j++) {

					if($box['size'] === '5x2') {

						if (isset($tempGrid[$i][$j]) && $tempGrid[$i][$j] == ''){
							if(!isset($tempGrid[$i + 4][$j]) ||
								!isset($tempGrid[$i][$j+1]) ||
								!isset($tempGrid[$i+4][$j+4])){
								$i = $i + 1;
							}
							else if (
								$tempGrid[$i][$j + 1] === '' &&
								$tempGrid[$i][$j + 2] === '' &&
								$tempGrid[$i][$j + 3] === '' &&
								$tempGrid[$i][$j + 4] === '' &&
								$tempGrid[$i + 1][$j] === '' &&
								$tempGrid[$i + 1][$j + 1] === '' &&
								$tempGrid[$i + 1][$j + 2] === '' &&
								$tempGrid[$i + 1][$j + 3] === '' &&
								$tempGrid[$i + 1][$j + 4] === ''){

								$tempGrid[$i][$j] = 'x';
								$tempGrid[$i][$j + 1] = 'x';
								$tempGrid[$i][$j + 2] = 'x';
								$tempGrid[$i][$j + 3] = 'x';
								$tempGrid[$i][$j + 4] = 'x';
								$tempGrid[$i + 1][$j] = 'x';
								$tempGrid[$i + 1][$j + 1] = 'x';
								$tempGrid[$i + 1][$j + 2] = 'x';
								$tempGrid[$i + 1][$j + 3] = 'x';
								$tempGrid[$i + 1][$j + 4] = 'x';

								$result[] = ['x_pos' => $i + 1,
									'y_pos' => $j + 1,
									'width' => 2,
									'height' => 5,
									'order_item_id' => $box['order_item_id']];
								unset($remainingBox[$key]);
								break 2;
							}
						}
					}
				}
			}
		}

		foreach ($data as $key => $box){
			for ($i=0; $i <10; $i++){
				for ($j=0; $j<15; $j++) {

					if($box['size'] === '4x4') {

						if (isset($tempGrid[$i][$j]) && $tempGrid[$i][$j] == '' ){
							if(!isset($tempGrid[$i + 3][$j]) ||
								!isset($tempGrid[$i][$j+3]) ||
								!isset($tempGrid[$i + 3][$j + 3])){
								$i = $i + 1;
							}
							else if (
								$tempGrid[$i][$j + 1] === '' &&
								$tempGrid[$i][$j + 2] === '' &&
								$tempGrid[$i][$j + 3] === '' &&
								$tempGrid[$i + 1][$j] === '' &&
								$tempGrid[$i + 1][$j + 1] === '' &&
								$tempGrid[$i + 1][$j + 2] === '' &&
								$tempGrid[$i + 1][$j + 3] === '' &&
								$tempGrid[$i + 2][$j] === '' &&
								$tempGrid[$i + 2][$j + 1] === '' &&
								$tempGrid[$i + 2][$j + 2] === '' &&
								$tempGrid[$i + 2][$j + 3] === '' &&
								$tempGrid[$i + 3][$j] === '' &&
								$tempGrid[$i + 3][$j + 1] === '' &&
								$tempGrid[$i + 3][$j + 2] === '' &&
								$tempGrid[$i + 3][$j + 3] === ''){

								$tempGrid[$i][$j] = 'x';
								$tempGrid[$i][$j + 1] = 'x';
								$tempGrid[$i][$j + 2] = 'x';
								$tempGrid[$i][$j + 3] = 'x';
								$tempGrid[$i + 1][$j] = 'x';
								$tempGrid[$i + 1][$j + 1] = 'x';
								$tempGrid[$i + 1][$j + 2] = 'x';
								$tempGrid[$i + 1][$j + 3] = 'x';
								$tempGrid[$i + 2][$j] = 'x';
								$tempGrid[$i + 2][$j + 1] = 'x';
								$tempGrid[$i + 2][$j + 2] = 'x';
								$tempGrid[$i + 2][$j + 3] = 'x';
								$tempGrid[$i + 3][$j] = 'x';
								$tempGrid[$i + 3][$j + 1] = 'x';
								$tempGrid[$i + 3][$j + 2] = 'x';
								$tempGrid[$i + 3][$j + 3] = 'x';

								$result[] = ['x_pos' => $i + 1,
									'y_pos' => $j + 1,
									'width' => 4,
									'height' => 4,
									'order_item_id' => $box['order_item_id']];
								unset($remainingBox[$key]);
								break 2;
							}
						}
					}
				}
			}
		}

		foreach ($data as $key => $box){
			for ($i=0; $i <10; $i++){
				for ($j=0; $j<15; $j++) {

					if($box['size'] === '3x3') {

						if (isset($tempGrid[$i][$j]) && $tempGrid[$i][$j] == ''){
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
								unset($remainingBox[$key]);
								break 2;
							}
						}
					}
				}
			}
		}

		foreach ($data as $key => $box){
			for ($i=0; $i <10; $i++){
				for ($j=0; $j<15; $j++) {

					if($box['size'] === '2x2') {
						if (isset($tempGrid[$i][$j]) && $tempGrid[$i][$j] == '' ){
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
								unset($remainingBox[$key]);
								break 2;
							}
						}
					}
				}
			}
		}

		foreach ($data as $key => $box){
			for ($i=0; $i <10; $i++){
				for ($j=0; $j<15; $j++) {

					if($box['size'] === '1x1'){
						if(isset($tempGrid[$i][$j]) && $tempGrid[$i][$j] == '' ) {
							$tempGrid[$i][$j] = 'x';

							$result[] = ['x_pos' => $i + 1,
								'y_pos' => $j + 1,
								'width' => 1,
								'height' => 1,
								'order_item_id' => $box['order_item_id']];
							unset($remainingBox[$key]);
							break 2;
						}
					}
				}
			}
		}
		$resultObject = ['result'=>$result,'remainingBox' => $remainingBox];

		return $resultObject;
	}

	/**
	 *
	 * @param $items
	 * @return array
	 */

	private function buildPrintSheetData($items){

		try {
			$productSizes = $this->getAllproductSizeWithId();
		}
		catch (\Exception $ex){
			dd('Exception block', $ex);
		}

		$boxes = [];

		foreach($items as $item) {
			$qty = $item['quantity'];
			for ($i=0; $i < $qty; $i++){
				$boxes[] = ['size' => $productSizes[$item['product_id']],
							'order_item_id' => $item['order_item_id']];
			}
		}

		$remainingBox = $boxes;

		while(count($remainingBox) != 0) {

			try {
				$result = $this->orderSheetProductPosition($remainingBox);
			}
			catch (\Exception $ex){
				dd('Exception block', $ex);
			}

			$sheet = $result['result'];
			$sheets[] = $sheet;
			$remainingBox = $result['remainingBox'];
		}

		return $sheets;
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

	private function getProductTitleByOrderItemId($id){
		try {
			$productData = DB::table('orders_items')->join('products', 'orders_items.product_id', '=', 'products.product_id')->where('orders_items.order_item_id', $id)->get();

		}
		catch (\Exception $ex){
			dd('Exception block', $ex);
		}

		return $productData[0]->title;
	}
}