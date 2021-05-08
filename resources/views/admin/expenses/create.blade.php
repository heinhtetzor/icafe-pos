@extends('layouts.admin')
@section('css')
<link rel="stylesheet" href="/choices/styles/choices.min.css" />
<link rel="stylesheet" href="/slimselect/slimselect.css">
<style>
    body {
        background-color: #d2d8d8;
    }
    .expense-form {
        background-color: white;
        border-radius: 8px;
        padding: 1rem;
        box-shadow: 8px 4px 8px 4px #bbbbbb;
    }
    .expense-item-form, .expense-item-list {
        margin-top: 1rem;
        background-color: white;
        border-radius: 8px;
        padding: 1rem;
        box-shadow: 8px 4px 8px 4px #bbbbbb; 
    }
    #expenseTotal {
        font-weight: 900;
        font-size: 2rem;
        color: green;
    }
    #expenseTotal::after {
        content: " ကျပ်";
    }
</style>
@endsection
@section('content')
    <div class="container">
        <h3>
            @if (Request::segment(3) == "edit")
            <a href="{{ route('expenses.create') }}">🔙</a>
            @else 
            <a href="{{ route('admin.home') }}">🔙</a>
            @endif
            {{-- <a href="{{ route('expenses.index') }}">🔙</a> --}}
            အသုံးစရိတ် အသစ်</h3>
        <section class="expense-form">
            <input type="hidden" name="_token" id="_token" value="{{csrf_token()}}">          
            {{-- edit page --}}
            @if (!empty($expense)) 
                <input type="hidden" name="expense_id" id="expenseId" value="{{$expense->id}}">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="">ဘောင်ချာနံပါတ်</label>
                            <input name="invoice_no" class="form-control" type="text" readonly value="{{ $expense->invoice_no }}" required>
                        </div>
                    </div>    
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="">အချိန်</label>
                            <input type="text" disabled class="form-control" value="{{ $expense->datetime->format('h:i a') }} {{ $expense->datetime->format('d-M-Y') }}">                                            
                        </div>
                    </div>    
                    <div class="col-md-3">
                    </div>            
                    <div class="col-md-3">
                        <p id="expenseTotal">{{ $expense->total }}</p>
                        <button type="button" class="btn btn-block btn-success" id="confirmBtn">အတည်ပြုမည်</button>

                        <form id="delete-form" class="hidden" action="{{ route('expenses.destroy', $expense->id) }}" method="post">
                            @method('DELETE')
                            @csrf
                            <input type="hidden" name="id" value="{{ $expense->id }}">
                        </form>
                        <button class="btn btn-danger" id="delete" onclick="deleteHandler()">
                            Delete
                        </button>
                    </div>
                </div>                
            @endif 
            {{-- create page --}}
            @if (empty($expense))
                <form action="{{ route('expenses.store') }}" method="POST">
                    @csrf
                    <div class="row">
              
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="invoice_no">ဘောင်ချာ နံပါတ်</label>                                
                                <input name="invoice_no" class="form-control" type="text" readonly value="{{ $invoice_no }}" required>                                
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="datetime">အချိန်</label>                                
                                <input id="datetime" name="datetime" type="datetime-local" class="form-control" required>                                
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="remarks">မှတ်ချက်</label>
                                <textarea class="form-control" name="remarks" id="remarks" cols="30" rows="3"></textarea>
                            </div>
                        </div>                        
                        <div class="col-md-3">
                            <br><br>
                            <button class="btn btn-primary" type="submit">Submit</button>
                        </div>
                    </div>                    
                </form>
            @endif
        </section>
        
        @if (!empty($expense))
        <section class="expense-items">
            <div class="expense-item-form">
                <div class="row">
                    <div class="col-md-3">
                        <select id="item-select" class="form-control">
                            <option value="">===</option>
                            @foreach ($items as $item)
                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                            @endforeach 
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select id="menu-group-select" class="form-control">
                            <option value="">===</option>
                            @foreach ($menu_groups as $menu_group)
                            <option value="{{ $menu_group->id }}">{{ $menu_group->name }}</option>
                            @endforeach 
                        </select>
                        <input name="is_general_item" value="0" class="form-check-input" type="checkbox" id="is_general_item">
                        <label class="form-check-label" for="is_general_item">
                        အထွေထွေ
                        </label>
                    </div>
                    <div class="col-md-1">
                        <input type="number" class="form-control" id="quantity" placeholder="Qty">
                        
                    </div>
                    <div class="col-md-1">
                        {{-- <input type="text" class="form-control" id="unit" placeholder="Unit"> --}}
                        <select name="unit" id="unit" class="form-control">
                            <option value="ခု">ခု</option>
                            <option value="ထုပ်">ထုပ်</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <input type="text" class="form-control" id="cost" placeholder="နှုန်း">
                    </div>
                    <div class="col-md-2">
                        <h4 id="total">0 ကျပ်</h4>
                        <button id="addBtn" class="btn btn-primary">Add</button>
                    </div>
                </div>
            </div>

            <table class="table table-hover expense-item-list">
                <thead>
                    <tr>
                        <th>အမည်</th>
                        <th>နှုန်း</th>
                        <th>Qty</th>
                        <th>Unit</th>
                        <th>ကျသင့်ငွေ</th>
                        <th>အမျိုးအစား</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>                    
                </tbody>
            </table>
        </section>       
        @endif

        @if (empty($expense))
        <section>
            <table class="table table-hover bg-white mt-3">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>ဘောင်ချာနံပါတ်</th>
                        <th></th>
                        <th>အချိန်</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($expenses as $key => $expense)
                    <tr>
                        <td>{{ $key + 1 }}</td>
                        @if ($expense->status == 0)
                        <td><a href="{{route('expenses.edit', $expense->id)}}">
                            ⇲
                            {{$expense->invoice_no}}
                        </a></td>
                        @endif
                        @if ($expense->status == 1)
                        <td><a href="{{route('expenses.show', $expense->id)}}">
                            ⇲
                            {{$expense->invoice_no}}
                        </a></td>
                        @endif
                        <td>
                            {{ $expense->status == 0 ? "🟡" : "🟢" }}
                        </td>
                        <td>{{ $expense->datetime->format('h:i a') }} {{ $expense->datetime->format('d-M-Y') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </section>
        @endif 
    </div>
@endsection
@section('js')

<script src="/slimselect/slimselect.js"></script>
<script src="/choices/scripts/choices.min.js"></script>
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
    (() => {        
        const token=document.querySelector('#_token').value;            
        
        const datetime = document.querySelector('#datetime');
        
        function _getTimeZoneOffsetInMs() {
            return new Date().getTimezoneOffset() * -60 * 1000;
        }
        let now = new Date().getTime();
        if (datetime)
            datetime.value = new Date(now + _getTimeZoneOffsetInMs()).toISOString().slice(0,19);    
        
    
        

        @if(!empty($expense))
        const itemSelect = document.querySelector('#item-select');        
        
        let itemSelectSlim = new SlimSelect({
            select: '#item-select'
        });

        const menuGroupSelect = document.querySelector('#menu-group-select');
        // const menuGroupChoices = new Choices(menuGroupSelect);
        const isGeneralItemRadio = document.querySelector('#is_general_item');
        const cost = document.querySelector('#cost');
        const quantity = document.querySelector('#quantity');
        const total = document.querySelector('#total');
        const unit = document.querySelector("#unit");
        const addBtn = document.querySelector('#addBtn');
        const expenseItemList = document.querySelector('.expense-item-list > tbody');
        const totalText = document.querySelector('#expenseTotal');
        const expenseId = document.querySelector('#expenseId');
        const confirmBtn = document.querySelector('#confirmBtn');

        addBtn.addEventListener('click', addBtnClickHandler);
        itemSelect.addEventListener('change', itemSelectChangeHandler);
        menuGroupSelect.addEventListener('change', menuGroupChangeHandler);
        isGeneralItemRadio.addEventListener('click', isGeneralItemChangeHandler);
        cost.addEventListener('input', costChangeHandler);
        quantity.addEventListener('input', quantityChangeHandler);
        confirmBtn.addEventListener('click', confirmBtnHandler);

        fetchExpenseItems();
        

        function resetExpenseItemForm () {
            menuGroupSelect.value = "";
            itemSelectSlim.set("");             
            cost.value = 0;
            quantity.value = 1;
            unit.value = "ခု";
            total.innerHTML = "0 ကျပ်";
            isGeneralItemRadio.checked = false;
        }

        function itemSelectChangeHandler (e) {            
            fetch (`/api/expenses/getItem/${e.target.value}`)
            .then (res => res.json())
            .then (res => {
                
                if (res.item.menu_group_id) {
                    menuGroupSelect.value = res.item.menu_group_id
                    menuGroupSelect.disabled = false;
                    is_general_item.checked = false;
                }
                if (!res.item.menu_group_id && res.item.is_general_item == 1) {                    
                    menuGroupSelect.disabled = true;
                    menuGroupSelect.value = "";
                    is_general_item.checked = true;
                }
                cost.value = res.item.cost;
                quantity.value = 1;
                total.innerHTML = calculateTotal();
            })
            .catch (err => {
                console.log(err);
            })
        }

        function isGeneralItemChangeHandler (e) {            
            if (isGeneralItemRadio.checked) {
                menuGroupSelect.disabled = true;
            }
            else {                
                menuGroupSelect.disabled = false;
            }
        }

        function menuGroupChangeHandler (e) {
            if (e.target.value) {
                isGeneralItemRadio.checked = false;
                isGeneralItemRadio.disabled = true;
            }
        }

        function costChangeHandler (e) {
            total.innerHTML = calculateTotal();
        }

        function quantityChangeHandler (e) {
            total.innerHTML = calculateTotal();
        }

        function calculateTotal () {            
            return (quantity.value * cost.value).toFixed(2) + " ကျပ်";
        }

        function confirmBtnHandler (e) {
            if (!confirm("သေချာပါသလား ?")) {
                return;
            }
            fetch (`/api/expenses/confirm/${expenseId.value}`, {
                headers: {
                    "Content-Type": "application/json",
                    "Accept": "application/json",
                    "X-Requested-With": "XMLHttpRequest",
                    "X-CSRF-Token": token
                },
                credentials: "same-origin",
                method: 'POST',
            })
            .then (res => res.json())
            .then (res => {             
                if (!res.isOk) {                    
                    Toastify({
                    text: res.message,                    
                    backgroundColor: "linear-gradient(to right, #A40606, #D98324)",
                    className: "info",
                    }).showToast();
                    return;
                }
                location.href=`/admin/expenses/${expenseId.value}`;
            })
            .catch (err => {
                console.log(err);
            })            
        }

        function addBtnClickHandler () {
            // TODO: front end validation
            if (!unit.value || !quantity.value || !cost.value) {
                alert("Please fill required fields");   
                return;
            }

            const data = {
                expense_id: {{$expense->id}},
                quantity: quantity.value,
                cost: cost.value,
                menu_group_id: menuGroupSelect.value,
                item_id: itemSelect.value,
                unit: unit.value,
                is_general_item: isGeneralItemRadio.checked ? 1 : 0
            }
            
            fetch(`/api/expenses/addExpenseItem`, {
                headers: {
                    "Content-Type": "application/json",
                    "Accept": "application/json",
                    "X-Requested-With": "XMLHttpRequest",
                    "X-CSRF-Token": token
                },
                credentials: "same-origin",
                method: 'POST',
                body: JSON.stringify(data)
            })
            .then (res => res.json())
            .then (res => {
                resetExpenseItemForm();
                fetchExpenseItems();
                getExpense();
            })
            .catch (err => {
                console.log(err)
            })
        }

        function getExpense () {
            fetch (`/api/expenses/getExpense/${expenseId.value}`)
            .then (res => res.json())
            .then (res => {
                totalText.innerHTML = res.expense.total;
            })
            .catch (err => {
                console.log(err);
            })
        }

        function fetchExpenseItems () {
            const expenseId = {{$expense->id}};
            fetch (`/api/expenses/${expenseId}/getExpenseItems`)
            .then (res => res.json())
            .then (res => {
                expenseItemList.innerHTML = "";
                if (res.expense_items.length == 0) {
                    expenseItemList.innerHTML += `
                    <tr>
                        <td colspan="7">မရှိသေးပါ</td>
                    </tr>
                    `;
                    return;
                }
                res.expense_items.forEach (expenseItem => {
                    expenseItemList.innerHTML += `
                        <tr>
                            <td>${expenseItem.item.name}</td>
                            <td>${expenseItem.cost}</td>
                            <td>${expenseItem.quantity}</td>
                            <td>${expenseItem.unit}</td>
                            <td>${(expenseItem.cost * expenseItem.quantity).toFixed(2)} ကျပ်</td>
                            <td>${expenseItem.menu_group ? expenseItem.menu_group.name : "အထွေထွေ"}</td>
                            <td>
                                <button class="btn btn-danger deleteExpenseItemBtn" data-id="${expenseItem.id}">⛔️</button>
                            </td>                             
                        </tr>
                    `;
                    const deleteExpenseItemBtns = document.querySelectorAll('.deleteExpenseItemBtn');
                    deleteExpenseItemBtns.forEach (x => {
                        x.addEventListener('click', deleteExpenseItem);
                    }) 
                })
            })
            .catch (err => {
                console.log(err);
            })
        }

        function deleteExpenseItem (e) {
            fetch (`/api/expenses/deleteExpenseItem`, {
                headers: {
                    "Content-Type": "application/json",
                    "Accept": "application/json",
                    "X-Requested-With": "XMLHttpRequest",
                    "X-CSRF-Token": token
                }, 
                credentials: "same-origin",
                method: 'POST',
                body: JSON.stringify({
                    "id": e.target.dataset["id"]
                })
            })
            .then (res => res.json())
            .then (res => {
                fetchExpenseItems();
                getExpense();
            })
            .catch (err => {
                console.log(err);
            })
        }
        @endif
    })();
</script>
@endsection