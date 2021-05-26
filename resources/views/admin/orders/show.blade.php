@extends('layouts.admin')
@section('css')
<style>
    body {
        background-color: rgb(233, 232, 232);
    }
    .details {
        /* border: 2px solid #a7a5a5; */
        border-radius: 10px;
        background-color: #fff;
        position: fixed;
        /*width: 30%;*/        
        max-height: 80vh;
        overflow-y: scroll;
        padding: 1rem;
    }
    .list-container {
        /* max-height: 80vh; */
        background-color: #fff; 
        border-radius: 10px;        
        /* overflow-y: scroll; */
    }
</style>
@endsection
@section('content')
    <div class="container">
        <h2><a href="javascript:history.back()">🔙</a>
            <span class="badge rounded-pill bg-success">{{$order->invoice_no}}</span>

            {{$order->created_at->format('d-M-Y')}} - {{$order->created_at->format('h:i A')}}
            
            <!-- Example single danger button -->
<!-- Example single danger button -->
<div class="btn-group">
    <a href="{{ route('orders.print', $order->id) }}" class="btn btn-info">
        🖨  Print
    </a>
    
  </ul>
</div>

        </h2>

        <div class="row">
            <div class="col-md-8 list-container">
                <table class="table invoice-table">
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
                            <td>{{$orderMenu->menu->name ?? ""}}</td>
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
            <div class="col-md-4">
                <section class="details">
                    <h4>အကျဉ်းချုပ်</h4>
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Menu အုပ်စုအမည်</th>
                                <th>အရေအတွက်</th>
                                <th>စုစုပေါင်း</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $grandTotal=0; @endphp
                            @forelse($orderMenuGroups as $mg)
                            <tr>
                                <td>{{$mg->name}}</td>
                                <td>{{$mg->quantity}}</td>
                                <td>{{$mg->total}} ကျပ်</td>
                                @php $grandTotal+=$mg->total; @endphp
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3">မရှိသေးပါ</td>
                            </tr>
                            @endforelse
                        </tbody>
                        <tfoot>
                            <tr style="font-weight: 900">
                                <td colspan="2">စုစုပေါင်း</td>                        
                                <td>{{$grandTotal}} ကျပ်</td>
                            </tr>
                        </tfoot>
                    </table>
                </section>
            </div>
        </div>

        <br>
        <form id="delete-form" class="hidden" action="{{ route('orders.destroy', $order->id) }}" method="post">
            @method('DELETE')
            @csrf
            <input type="hidden" name="id" value="{{ $order->id }}">
        </form>
        <button class="btn btn-danger" id="delete" onclick="deleteHandler()">
            Delete
        </button>
    </div>
@endsection

@section('js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/0.4.1/html2canvas.min.js" integrity="sha512-s/XK4vYVXTGeUSv4bRPOuxSDmDlTedEpMEcAQk0t/FMd9V6ft8iXdwSBxV0eD60c6w/tjotSlKu9J2AAW1ckTA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>        
<script>
    const ls = document.querySelector('.list-container');
    html2canvas(ls, {
        onrendered: function (canvas) {
            let image = new Image();
            image.src = canvas.toDataURL("image/png");
            console.log(image);
        }
    })
    function deleteHandler () {
        if (confirm("Are you sure?")) {
            document.querySelector('#delete-form').submit();
            return true;   
        }
        else {
            return false;
        }
    }
</script>
@endsection