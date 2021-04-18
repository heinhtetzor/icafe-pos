@extends('layouts.client')
@section('style')
<style>
    .container {
        margin-top: 4rem;
    }
</style>
@endsection
@section('content')
<div class="container">
    <div class="card" style="width: 300px;">
        <div class="card-header">
            Start new session
        </div>
        <div class="card-body">
            <form action="{{route('express.store')}}" method="POST">
                <input type="hidden" name="_token" id="_token" value="{{csrf_token()}}">
                <button type="submit" class="btn btn-success">
                    Start
                </button>
            </form>
        </div>
    </div>

    <table class="table table-hover">
        <thead>
            <tr>
                <th>No</th>
                <th>Invoice No</th>
                <th>အချိန်</th>
                <th>စုစုပေါင်း</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($expressOrders as $key => $order)
            <tr>
                <td>{{ $key + 1 }}</td>
                <td><a href="{{route('orders.show', $order->id)}}">
                    ⇲
                    {{$order->invoice_no}}
                </a></td>
                <td>{{ $order->created_at->format('h:i a') }} {{ $order->created_at->format('d-M-Y') }}</td>
                @php
                $total=0; 
                foreach($order->order_menus as $or) {
                    $total+=$or->quantity*$or->price;
                }
                @endphp 
                <td>{{ $total }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection