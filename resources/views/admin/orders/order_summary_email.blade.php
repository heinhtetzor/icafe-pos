<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Order No #1232</title>
</head>
<body>
    <h1>Order ID: {{ $order->invoice_no }}</h1>
    <h2>Datetime: {{ $datetime }}</h2>
    <p>By: {{ $order->waiter->name ?? "" }}</p>

    <table border="1" class="table table-sm table-hover">
        <thead>
            <tr>
                <th style="padding:9px;">Qty</th>
                <th></th>
                <th>အမျိုးအမည်</th>
                <th>နှုန်း</th>
                <th>စုစုပေါင်း</th>
            </tr>
        </thead>
        <tbody>
            @foreach($orderMenus as $orderMenu)
            @if (!$orderMenu->isSummary)
            <tr>
                <td style="padding:9px;">{{$orderMenu->quantity}}</td>
                <td style="padding:9px;">x</td>
                <td>{{$orderMenu->menu->name ?? ""}}</td>
                <td style="padding:9px;">{{$orderMenu->price}}</td>
                <td>{{$orderMenu->price*$orderMenu->quantity}} ကျပ်</td>
            </tr>
            @endif
            @if ($orderMenu->isSummary)
                <tr style="font-weight: 900; border-bottom: 4px solid black;">
                    <td></td>
                    <td></td>
                    <td>{{ $orderMenu->menuGroupName }}</td>
                    <td></td>
                    <td >{{ $orderMenu->menuGroupTotal }} ကျပ်</td>
                </tr>
            @endif
            @endforeach
            <tr style="font-weight: 900">
                <td align="center" colspan="4">စုစုပေါင်း</td>
                <td>{{$total}} ကျပ်</td>
                <td>
                </td>
            </tr>
        </tbody>   
    </table>
    <hr>
    <i>This is a computer generated email.</i>
</body>
</html>