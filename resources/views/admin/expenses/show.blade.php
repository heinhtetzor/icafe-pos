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
{{-- CSRF token --}}
    <input type="hidden" name="_token" id="_token" value="{{csrf_token()}}">
<!-- cancel modal starts -->
    <div class="modal fade" id="passcodeModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="cancel-modal-title" id="exampleModalLabel">Cancel </h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="modal-menu-id">
                <div class="row">                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="quantity">📉 အရေအတွက်</label>
                            <input type="number" class="form-control" id="cancel-quantity" min="1" step="1" autocomplete="off">
                        </div>
                        <div class="form-group">
                            <label for="passcode">🔐 Passcode</label>
                            <input type="password" class="form-control" id="passcode-txt" autocomplete=off>
                        </div>  
                    </div>                  
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>                
                <button id="passcode-confirm-btn" class="btn btn-primary">
                    OK
              </button>
            </div>
          </div>
        </div>
    </div> 
<!-- cancel modal ends -->



<div class="container">
    @if (session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
    @endif
    <h2>
        @if ($from_search_result)
        <a href="javascript:history.back()">🔙</a>
        @else
        <a href="{{ route('expenses.index') }}">🔙</a>
        @endif

        <span class="badge rounded-pill bg-success">{{$expense->invoice_no}}</span>

        {{$expense->created_at->format('d-M-Y')}} - {{$expense->datetime->format('h:i A')}}
        @if ($expense->type == 1)
        <span class="badge bg-primary">Stock Item</span>
        @endif

        @if ($is_edit_mode)
        <a style="text-decoration:none; font-size: 0.8rem;color:black;" href="?edit=false">Cancel</a>
        @else
        <a style="text-decoration:none; font-size: 0.8rem;color:black" href="?edit=true">✏️ ️Edit</a>
        @endif

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
                        <th>-</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- non stock -->
                    @if ($expense->type == 0)

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
                            <td>
                                @if ($is_edit_mode)
                                <button class="btn cancel-expense-item" data-id="{{$expense_item->id}}" data-name="{{$expense_item->item->name}}" data-quantity="{{$expense_item->quantity}}" data-type="{{$expense->type}}">
                                    ❌
                                </button>
                                @endif
                            </td>
                        </tr>
                    @endforeach

                    @endif 

                    @if ($expense->type == 1)

                    @foreach ($expense_items as $expense_item)
                        <tr>
                            <td>{{ $expense_item->quantity }} {{ $expense_item->unit }}</td>
                            <td>x</td>
                            <td>{{ $expense_item->stockMenu->menu->name }}</td>
                            <td>{{ $expense_item->cost }}</td>
                            <td>{{ $expense_item->cost * $expense_item->quantity }} ကျပ်</td>
                            <td>
                                @if ($is_edit_mode)
                                <button class="btn cancel-expense-item" data-id="{{$expense_item->id}}" data-name="{{$expense_item->stockMenu->menu->name}}" data-quantity="{{$expense_item->quantity}}" data-type="{{$expense->type}}">
                                    ❌
                                </button>
                                @endif
                            </td>
                        </tr>

                    @endforeach
                    @endif
                </tbody>   
            </table>

            <!-- delete logs -->
            <div style="margin-top:1rem;opacity: 0.6;">
            @if ($expense->delete_logs)
                <h4>ဖျက်ထားသည့် မှတ်တမ်းများ</h4>
                <table class="table table-sm bg-white">
                    @foreach ($expense->delete_logs as $log)
                    <tr>
                        <td>{{ $log['item_name'] ?? "" }}</td>
                        <td>{{ $log['cost'] ?? "" }}</td>
                        <td>x</td>
                        <td>{{ $log['quantity'] ?? "" }}</td>
                        <td>{{ $log['deleted_at'] ?? "" }}</td>
                    </tr>
                    @endforeach
                </table>
            @endif    
            </div>

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
                            @if ($expense->type == 0 && $mg->is_general_item == 1)
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
    //for expense item deleting
    let passcodeModal; 
    let orderMenuId; //for passcode modal
    let cancelQuantity;
    let cancelModalTitle;

    window.addEventListener('load', () => {        
        passcodeModal = new bootstrap.Modal(document.getElementById('passcodeModal'), {
            backdrop: true
        })
    })

    function cancelExpenseItemBtnHandler(e) {
        expenseItemId = e.target.dataset['id'];
        expenseType = e.target.dataset['type'];
        document.querySelector('#cancel-modal-title').innerHTML = `Cancel ${e.target.dataset['name']} x ${e.target.dataset['quantity']}`;
        document.querySelector('#passcode-txt').value = "";
        document.querySelector('#cancel-quantity').value = "";
        passcodeModal.show();                    
        const passcodeConfirmButton = document.querySelector('#passcode-confirm-btn');

        passcodeConfirmButton.addEventListener('click', cancelExpenseItemAction);                    
    }

    const cancelExpenseItemBtns = document.querySelectorAll('.cancel-expense-item ');
    for (cancelExpenseItemBtn of cancelExpenseItemBtns) {
        cancelExpenseItemBtn.addEventListener('click', cancelExpenseItemBtnHandler); 
    }

    function cancelExpenseItemAction () {
        const cancelQuantityValue = document.querySelector('#cancel-quantity').value;
        if (!cancelQuantityValue) {
            alert("အရေအတွက်ထည့်သွင်းပါ");
            return;
        }
        cancelQuantity = cancelQuantityValue;

        const passcodeTxtValue = document.querySelector('#passcode-txt').value;
        
        if (passcodeTxtValue != "{{$passcode}}") {
            alert("လျှို့ဝှက်နံပါတ်မှားယွင်းနေပါသည်");
            return;
        }

        const token=document.querySelector('#_token').value;

        fetch(`/api/expenses/deleteExpenseItem`, {
            method: 'POST',
            headers: {
            "Content-Type": "application/json",
            "Accept": "application/json",
            "X-Requested-With": "XMLHttpRequest",
            "X-CSRF-Token": token
            },
            credentials: "same-origin",
            body: JSON.stringify({
                "id": expenseItemId,
                "type": expenseType,
                "cancelQuantity": cancelQuantity
            })
        }) 
        .then(res => res.json())
        .then(res => {                                
            passcodeModal.hide();
            location.reload();
        })
        .catch (err => {
            console.log("Error" + err);
        })
    }

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