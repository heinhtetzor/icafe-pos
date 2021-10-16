@extends('layouts.admin')
@section('css')
<style> 
    body {
        background-color: rgb(233, 232, 232);
    }
    .stock-row {
        background-color:  lightgreen;
    }
</style>
@endsection
@section('content')
{{-- Modal  --}}
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
    <h2 class="top-bar">
        <a href="{{route('admin.reports')}}">🔙 </a>   
        @if($isToday)
        ယနေ့   ({{$fromTime->format('d-M-Y')}}) <div class="badge bg-primary">အဝယ်စာရင်း</div>
        @else
        {{$fromTime->format('d-M-Y')}} မှ {{$toTime->format('d-M-Y')}} ထိ <div class="badge bg-primary">အဝယ်စာရင်း</div>
        @endif
        <a href="{{ route('expenses.create') }}" class="btn btn-success">🧾 အသစ်</a>
        <span style="float:right;">
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#dateModal">Search</button>            
            <a href="{{route('expenses.index')}}">ယနေ့</a>
        </span>
    </h2>
    <div class="row">
        <div class="col-md-8">
            <section class="list-container">
                @if (session('msg'))
                <div class="alert alert-danger">
                    {{ session('msg') }}
                </div>
                @endif
                @if (session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
                @endif
                <table class="table table-hover bg-white">
                    <thead>
                        <tr>
                            <th>ဘောင်ချာ နံပါတ်</th>
                            <th>အချိန်</th>
                            <th>စုစုပေါင်း</th>
                            <th>Status</th>
                            <th>Created By</th>
                            <th>မှတ်ချက်</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($expenses as $expense)
                        <tr class="{{ $expense->type == 1 ? 'stock-row' : '' }}">                         
                            @if ($expense->status == 0)
                            <td><a href="{{ route('expenses.edit', $expense->id) }}">{{ $expense->invoice_no }}</a></td>                            
                            @endif
                            @if ($expense->status == 1)                               
                            <td><a href="{{ route('expenses.show', ["id" => $expense->id, 'from_search_result' => true]) }}">{{ $expense->invoice_no }}</a></td>                            
                            @endif
                            <td>{{ $expense->datetime->format('d-M-Y h:i A') }}</td>
                            <td>{{ $expense->total }} ကျပ်</td>
                            <td>{{ $expense->status == 0 ? "🟠" : "🟢"}}</td>
                            <td>{{ $expense->user->username }}</td>
                            <td>{{ $expense->remarks }}</td>
                        </tr>
                        @empty
                        မရှိသေးပါ
                        @endforelse
                    </tbody>
                </table>

                {{$expenses->appends($_GET)->links()}}
            </section>
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
<script src="/litepicker/litepicker.js"></script>
<script>
    (() => {
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

        // get expense summary
        const params = new URLSearchParams(location.search);
        const date = params.get('date');

        let url;
        if (date) {
            url = `/api/expenses/getSummary/${date}`;
        }
        else {
            url = `/api/expenses/getSummary`;
        }

        fetch (url)
        .then (res => res.json())
        .then (res => {
            let grandtotal = 0;
            let mgs = "";
            res.expenseItemMenuGroups.forEach (mg => {
                if (!res.expenseItemMenuGroups) {
                    return "မရှိသေးပါ";
                }
                grandtotal += +mg.total;
                mgs += `<tr>                    
                    <td>${mg.is_general_item == 1 ? "အထွေထွေ" : mg.name}</td>
                    <td>${mg.quantity}</td>
                    <td>${mg.total} ကျပ်</td>
                </tr>`;
            });
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
                        <td>${(grandtotal).toFixed(2)} ကျပ်</td>
                    </tr>
                </tbody>
                <tfoot>
                </tfoot>
            </table>
            `;
        })
    })();
</script>
@endsection