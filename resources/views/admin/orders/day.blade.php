@extends('layouts.admin')
@section('css')
<style>
    body {
        background-color: rgb(233, 232, 232);
    }
    
</style>
@endsection 
@section('content')
<!-- modal -->
<!-- Modal -->
<div class="modal fade" id="dateModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">ရှာရန်</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form method="GET" action="">
        <div class="modal-body">
            <div class="form-group">
                <input placeholder="နေ့စွဲရွေးပါ" autocomplete="off" class="from-control" type="text" name="date" id="datePicker">                    
            </div>
            <hr>
            <input class="form-check-input" type="checkbox" value="" id="searchWithInvoiceRadio">
            <label class="form-check-label" for="searchWithInvoiceRadio">
              ဘောင်ချာနံပါတ်ဖြင့်ရှာမည်
            </label>
            <div class="form-group">
                <input disabled placeholder="ဘောင်ချာနံပါတ်ထည့်သွင်းပါ" type="text" autocomplete="off" class="form-control" name="invoiceNo" id="invoiceNo">
            </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary">Search</button>
      </div>
    </form> 
    </div>
  </div>
</div>

<div class="container">
    <h3> 

        <a href="{{route('admin.reports')}}">🔙 </a>   
        @if($isToday)
        ယနေ့   ({{$fromTime->format('d-M-Y')}}) <div class="badge bg-success">အရောင်းစာရင်း</div>
        @else
        {{$fromTime->format('d-M-Y')}} မှ {{$toTime->format('d-M-Y')}} ထိ <div class="badge bg-success">အရောင်းစာရင်း</div>
        @endif

        <span style="float:right;">
            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#dateModal">Search</button>            
            <a href="{{route('orders.today')}}">ယနေ့</a>
        </span>
    </h3>
    <div class="row">
        <div class="col-md-8">
            <div class="list-container">
                @if (session('msg'))
                <div class="alert alert-danger">
                    {{ session('msg') }}
                </div>
                @endif
                <table class="table table-hover bg-white">
                    <thead>
                        <tr>
                            <th>ဘောင်ချာ နံပါတ်</th>
                            <th>နေ့စွဲ</th>
                            <th>အချိန်</th>
                            <th>Status</th>
                            <th>Table</th>
                            <th>ငွေရှင်းသူ</th>
                            <th>စုစုပေါင်း</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $order)
                        <tr>
                            <td><a href="{{route('orders.show', $order->id)}}">
                                ⇲
                                {{$order->invoice_no}}
                            </a></td>
                            <td>{{$order->created_at->format('d-M-Y')}}</td>
                            <td>{{$order->created_at->format('h:i A')}}</td>                            
                            <td>{{($order->status==0) ? "🟠" : "🟢"}}</td>
                            <td>
                                @if($order->table_id == 0)
                                Express
                                @elseif ($order->table)
                                {{$order->table->name}}
                                @else                             
                                DELETED
                                @endif                            
                            </td>
                            <td>{{$order->waiter->name ?? ""}}</td>                            
                            @php
                            $total=0; 
                            if ($order->total > 0) {
                                $total = $order->total;
                            }
                            else {
                                foreach($order->order_menus as $or) {
                                    $total+=$or->quantity*$or->price;
                                }                                
                            }
                            @endphp 
                            <td>{{$total}} ကျပ်</td>                                                        
                        </tr>
                        @empty 
                        <tr>မရှိသေးပါ</tr>
                        @endforelse
                    </tbody>
                </table>
                              
                {{$orders->appends($_GET)->links()}}
            </div>
        </div>
        <div class="col-md-4">
            <section class="details">
                <img style="margin-left:50px;" src="/loading.gif" alt="loading" width="100">            
            </section>
        </div>
    </div>
</div>
@endsection
@section('js')
{{-- <script src="https://cdn.jsdelivr.net/npm/litepicker/dist/litepicker.js"></script> --}}
<script src="/litepicker/litepicker.js"></script>
<script type="text/javascript">     
    (()=> {     
        const datePicker = document.querySelector('#datePicker');

        const detailsSection = document.querySelector('.details');

        const picker = new Litepicker({
            element: datePicker,
            singleMode: false
        });
        
        const searchWithInvoiceRadio = document.querySelector('#searchWithInvoiceRadio');
        const invoiceNo = document.querySelector('#invoiceNo');
        searchWithInvoiceRadio.addEventListener('click', function () {
            if (searchWithInvoiceRadio.checked) {
                datePicker.disabled = true;
                invoiceNo.disabled = false;
            }
            else {                
                datePicker.disabled = false;
                invoiceNo.disabled = true;
            }
        })

        // get order summary
        const params = new URLSearchParams(location.search);
        const date = params.get('date');

        let url;
        if (date) {
            url = `/api/orders/getSummary/${date}`;
        }
        else {
            url = `/api/orders/getSummary`;
        }

        fetch (url)
        .then (res => res.json())
        .then (res => {
            let grandtotal = 0;
            let mgs = "";
            res.orderMenuGroups.forEach (mg => {
                if (!res.orderMenuGroups) {
                    return "မရှိသေးပါ";
                }
                grandtotal += +mg.total;
                mgs += `<tr>
                    <td>${mg.name}</td>
                    <td>${mg.quantity}</td>
                    <td>${mg.total} ကျပ်</td>
                </tr>`;
            })
            detailsSection.innerHTML=`
            <h4>အကျဉ်းချုပ်</h4>

            <table class="table table-hover" id="summaryTable">
                <thead>
                    <tr>
                        <th>Menu အုပ်စု</th>
                        <th>အရေအတွက်</th>
                        <th>စုစုပေါင်း</th>
                    </tr>
                </thead>
                <tbody>
                    ${mgs}
                    <tr style="font-weight:900">
                        <td colspan="2">စုစုပေါင်း</td>
                        <td>${grandtotal} ကျပ်</td>
                    </tr>
                </tbody>
                <tfoot>
                </tfoot>
            </table>
            `;
        })            
        .catch (err => console.log(err));
    })()
</script>
@endsection