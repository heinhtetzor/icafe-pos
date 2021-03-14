@extends('layouts.admin')
@section('css')
@endsection
@section('content')
    <div class="container-fluid">
        <h2><a href="javascript:history.back()">🔙</a>{{$order->id}}</h2>

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