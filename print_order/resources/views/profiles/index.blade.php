@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-8 offset-2 border-bottom">
            <div class="d-flex justify-content-between align-bottom row">
                <h4>{{ $user->name }}'s Order History</h4>
                <a href="/order/create">Create Order</a>
            </div>
            <div class="col-md-10">
                @foreach($orders as $order)
                    <div class="pt-lg-5">
                        <label><h3>Order id: {{ $order[0]->order_number }} | Total Cost: ${{ $order[0]->total_price }}</h3></label>
                        <ul class="list-group">
                            <li class="list-group-item nav-header">
                                <div class="d-flex justify-content-between">
                                    <div>Order Items</div>
                                    <div>
                                        <form action="/printOrder" method="post">
                                            @csrf
                                            <input id="order_id" name="order_id" type="hidden" value="{{$order[0]->order_id}}">
                                            <input id="order_number" name="order_number" type="hidden" value="{{$order[0]->order_number}}">
                                            <button class="btn-primary" type="submit">Print Order</button>
                                        </form>
                                    </div>
                                </div>
                            </li>
                        @foreach($order[1] as $orderItem)
                            <li class="list-group-item">{{ $orderItem->title }} ({{ $orderItem->size }}) x {{ $orderItem->quantity }}</li>
                        @endforeach
                        </ul>
                    </div>
                @endforeach
                    <span class="border-bottom"></span>
            </div>
        </div>
    </div>
</div>
@endsection
