@extends('layouts.app')

@section('content')

<div class="container">
    <div class="row">
        <div class="col-8 offset-2">
            <h2>Order Id: {{ $orderNumber }}</h2>
        @foreach($sheets as $key => $sheet )
            <h3>Sheet#: {{ $key+1 }}</h3>
            <div class="wrapper">
                @foreach( $sheet as $list )
                    <div style="grid-area:{{ $list['y_pos'] }} / {{ $list['x_pos'] }} / span {{ $list['height'] }} / span {{ $list['width'] }};" >
                        {{ $list['productTitle'] }}
                    </div>
                @endforeach
            </div>
        @endforeach
        </div>
    </div>
</div>
@endsection