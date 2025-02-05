<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
	protected $guarded = [];

    public function user(){
	    return $this->belongsTo(User::class, 'customer_id');
    }

	public function orderItems(){
		return $this->hasMany(OrdersItem::class, 'order_id');
	}
}
