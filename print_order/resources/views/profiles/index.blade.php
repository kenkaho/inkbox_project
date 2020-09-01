@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-bottom">
                <h4>{{ $user->name }}'s Order History</h4>
                <a href="#" >Create Order</a>
            </div>
            <div class="col-md-6">
                @foreach($orders as $order)
                    <div>
                        Order id: {{ $order[0]->order_number }} | Total Cost: {{ $order[0]->total_price }}
                        <ul>
                        @foreach($order[1] as $orderItem)
                            <li>{{ $orderItem->title }} ({{ $orderItem->size }}) x {{ $orderItem->quantity }}</li>
                        @endforeach
                        </ul>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
