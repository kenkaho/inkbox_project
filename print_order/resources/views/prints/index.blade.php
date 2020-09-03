@extends('layouts.app')

@section('content')

<div class="container">
    <div class="row">
        <div class="col-8 offset-2">
        @foreach($sheets as $sheet )
            <div class="wrapper">
                @foreach( $sheet as $list )
                    <div style="grid-area:{{ $list['y_pos'] }} / {{ $list['x_pos'] }} / span {{ $list['height'] }} / span {{ $list['width'] }};" >

                        {{ $list['size'] }}
                    </div>
                @endforeach
            </div>
        @endforeach
        </div>
    </div>
</div>
@endsection