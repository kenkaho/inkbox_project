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

		    $sheets = $this->buildPrintSheetData($orderItemData);
		    $identifier = 0;

		    foreach ($sheets as $key => $sheet) {

			    foreach ($sheet as $printSheetItem) {
				    $tempSheet = $printSheetItem;
				    $tempSheet['size'] = $printSheetItem['width'] . "x" . $printSheetItem['height'];
				    $sheetItems[] = $tempSheet;

				    $printSheetItem['ps_id'] = $psid;
				    $printSheetItem['image_url'] = '';
				    $printSheetItem['identifier'] = $identifier;
				    $printSheetItem['size'] = $tempSheet['size'];

				    DB::table('print_sheet_item')->insertGetId($printSheetItem);
			    }
			    $identifier++;
			    $sheetsResult[] = $sheetItems;
		    }

		    return view('prints.index',['sheets' => $sheetsResult]);
	    }
	    else {

		    $printSheetItems = DB::table('print_sheet_item')->whereIn('order_item_id', $orderItemsIds)->get();

		    foreach($printSheetItems as $printSheetItem){
			    $Sheets[$printSheetItem->identifier][] = ['x_pos' => $printSheetItem->x_pos,
				    'y_pos' => $printSheetItem->y_pos,
				    'width' => $printSheetItem->width,
				    'height' => $printSheetItem->height,
				    'size' => $printSheetItem->size];
		    }

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

		$productSizes = $this->getAllproductSizeWithId();

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
			$result = $this->orderSheetProductPosition($remainingBox);
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
}
