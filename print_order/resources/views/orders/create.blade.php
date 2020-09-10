@extends('layouts.app')

@section('content')
    <div class="container">
        <form action="/order" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row">
                <div class="col-8 offset-2">
                    <h4>Create Order</h4>
                    <div class="form-group row">
                        @foreach( $products as $product )
                            <label for="name" class="col-md-4 col-form-label text-md-right pt-lg-5">{{ $product->title }} (Size {{$product->size}})</label>
                            <div class="col-md-6 pt-lg-5">
                                <select id="{{ $product->product_id }}"
                                        class="form-control @error('product1x1') is-invalid @enderror"
                                        name="productList[{{$product->product_id}}]"
                                        autocomplete="name" autofocus>

                                @error('product1x1')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                                    @for ($i=0; $i <=20; $i++)
                                        <option value="{{ $i }}">{{ $i }}</option>
                                    @endfor

                                </select>
                            </div>
                        @endforeach
                    </div>
                    <div class="row pt-3 float-right">
                        <button class="btn btn-primary">Add order</button>

                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection