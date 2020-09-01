@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-bottom">
                <h4>{{ $user->name }}'s Order History</h4>
                <a href="#" >Create Order</a>
            </div>
            <div class="col-md-8">
            </div>
        </div>
    </div>
</div>
@endsection
