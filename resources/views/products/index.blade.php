@extends('layouts.app')

@section('content')

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Products</h1>
    </div>
    {{-- @php dd($distincts, 'distincts');    @endphp --}}

    <div class="card">
        <form action="{{ route('product.filter')}}" method="post" class="card-header">
            @csrf
            <div class="form-row justify-content-between">
                <div class="col-md-2">
                    <input type="text" name="title" placeholder="Product Title" class="form-control">
                </div>
                <div class="col-md-2">
                    <select name="variant" id="" class="form-control" placeholder="Select a Variant">
                            <option value="">Selec a Variant</option>
                            @foreach ($distincts as $groupKey => $group)
                                {{-- @php dd($groupKey, $group, $group[0]->title, 'groupKey & group');    @endphp --}}
                                <option disabled>{{ $group[0]->title }}</option>
                                @foreach ($group as $key => $item)
                                    {{-- @php dd($item, $item->title, $item->variant, 'key & value');    @endphp --}}
                                    <option value="{{$item->variant}}"> --   {{$item->variant}}</option>
                                @endforeach
                            @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">Price Range</span>
                        </div>
                        <input type="text" name="price_from" aria-label="First name" placeholder="From" class="form-control">
                        <input type="text" name="price_to" aria-label="Last name" placeholder="To" class="form-control">
                    </div>
                </div>
                <div class="col-md-2">
                    <input type="date" name="date" placeholder="Date" class="form-control">
                </div>
                <div class="col-md-1">
                    <button type="submit" class="btn btn-primary float-right"><i class="fa fa-search"></i></button>
                </div>
            </div>
        </form>

        <div class="card-body">
            <div class="table-response">
                <table class="table">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Title</th>
                        <th>Description</th>
                        <th>Variant</th>
                        <th width="150px">Action</th>
                    </tr>
                    </thead>

                    @php
                        $i = 1;
                    @endphp
                    <tbody>

                    @foreach ($products as $product)
                        <tr>
                            <td>{{$i++}}</td>
                            <td>{{$product->title}} <br> Created at : {{\Carbon\Carbon::parse($product->created_at)->format('d-M-Y')}}</td>
                            {{-- <td>{{$product->description}}</td> --}}
                            <td>Quality product in low cost</td>
                            <td>
                                {{-- @php
                                    dd($product->productVariantPrices)
                                @endphp --}}
                                @foreach ($product->productVariantPrices as $productVariantPrice)
                                <dl class="row mb-0" style="height: 80px; overflow: hidden" id="variant">
                                    <dt class="col-sm-3 pb-0">
                                        @if ($productVariantPrice->productVariantTwo)
                                            {{ Str::upper($productVariantPrice->productVariantTwo->variant) }}/
                                        @endif
                                        @if ($productVariantPrice->productVariantOne)
                                            {{ ucfirst($productVariantPrice->productVariantOne->variant) }}/
                                        @endif
                                        @if ($productVariantPrice->productVariantThree)
                                            {{ $productVariantPrice->productVariantThree->variant }}
                                        @endif
                                        {{-- SM/ Red/ V-Nick --}}
                                    </dt>
                                    <dd class="col-sm-9">
                                        <dl class="row mb-0">
                                            <dt class="col-sm-4 pb-0">Price : {{ number_format($productVariantPrice->price,2) }}</dt>
                                            <dt class="col-sm-4 pb-0">InStock : {{ number_format($productVariantPrice->stock,2) }}</dt>
                                        </dl>
                                    </dd>
                                </dl>
                                    
                                @endforeach
                                {{-- <button onclick="$('#variant').toggleClass('h-auto')" class="btn btn-sm btn-link">Show more</button> --}}
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('product.edit', $product->id) }}" class="btn btn-success">Edit</a>
                                </div>
                            </td>
                        </tr>
                        
                    @endforeach

                    </tbody>

                </table>
            </div>

        </div>

        <div class="card-footer">
            <div class="row justify-content-between">
                <div class="col-md-6">
                <p>Showing {{ $products->firstItem() }} to {{ $products->lastItem() }} out of  {{$total}}</p>
                </div>
                <div class="col-md-6">
                        {{ $products->links() }}
                </div>
            </div>
        </div>
    </div>

@endsection
