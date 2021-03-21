@extends('layouts.admin')
@section('css')
@endsection
@section('content')
    <div class="container">
        <h2><a href="javascript:history.back()">🔙</a>
            <span class="badge rounded-pill bg-success">{{$order->invoice_no}}</span>

            {{$order->created_at->format('d-M-Y')}} - {{$order->created_at->format('h:i A')}}

        </h2>

        <table class="table">
            <thead>
                <tr>
                    <th>Qty</th>
                    <th></th>
                    <th>အမျိုးအမည်</th>
                    <th>နှုန်း</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($orderMenus as $orderMenu)
                <tr>
                    <td>{{$orderMenu->quantity}}</td>
                    <td>x</td>
                    <td>{{$orderMenu->menu->name}}</td>
                    <td>{{$orderMenu->price}}</td>
                    <td>{{$orderMenu->price*$orderMenu->quantity}} ကျပ်</td>
                </tr>
                @endforeach
                <tr style="font-weight: 900">
                    <td align="center" colspan="4">စုစုပေါင်း</td>
                    <td>{{$total}} ကျပ်</td>
                </tr>
            </tbody>   
        </table>
    </div>
@endsection