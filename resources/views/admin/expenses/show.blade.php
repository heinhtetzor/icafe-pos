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
        <span class="badge rounded-pill bg-success">{{$expense->invoice_no}}</span>

        {{$expense->created_at->format('d-M-Y')}} - {{$expense->datetime->format('h:i A')}}

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
                    @foreach ($expense_items as $expense_item)
                        <tr>
                            <td>{{ $expense_item->quantity }} {{ $expense_item->unit }}</td>
                            <td>x</td>
                            @if ($expense_item->menu_group)
                            <td>{{ $expense_item->item->name }} [{{ $expense_item->menu_group->name }}]</td>
                            @else                             
                            <td>{{ $expense_item->item->name }} [အထွေထွေ]</td>
                            @endif
                            <td>{{ $expense_item->cost }}</td>
                            <td>{{ $expense_item->cost * $expense_item->quantity }} ကျပ်</td>
                        </tr>
                    @endforeach
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
                        @forelse($expenseItemMenuGroups as $mg)
                        <tr>
                            @if ($mg->is_general_item == 1)
                            <td>အထွေထွေ</td>
                            @else 
                            <td>{{$mg->name}}</td>
                            @endif
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
    <form id="delete-form" class="hidden" action="{{ route('expenses.destroy', $expense->id) }}" method="post">
        @method('DELETE')
        @csrf
        <input type="hidden" name="id" value="{{ $expense->id }}">
    </form>
    <button class="btn btn-danger" id="delete" onclick="deleteHandler()">
        Delete
    </button>
</div>
@endsection

@section('js')
<script>
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
<script>

</script>
@endsection