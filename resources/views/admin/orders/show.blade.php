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
    
    {{-- paybill modal --}}
    <div class="modal fade" id="payBillModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="exampleModalLabel">Choose Waiter</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <div class="form-group">                
                <label for="waiter_id">Waiter ရွေးပါ</label>
                <select name="waiter_id" id="waiter-id" class="form-control">
                    <option value="">----</option>
                    @foreach ($waiters as $waiter)
                    <option value="{{$waiter->id}}">{{$waiter->name}}</option>
                    @endforeach
                </select>
              </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>                
                <button id="payBill" class="btn btn-primary">
                  ရှင်းမည်
              </button>
            </div>
          </div>
        </div>
      </div>
    
    
    {{-- passcode modal starts --}}
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
    {{-- passcode modal ends --}}

    {{-- email modal starts --}}
    <div class="modal fade" id="emailModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="cancel-modal-title" id="exampleModalLabel">Send Email</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="modal-menu-id">
                <div class="row">                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="email">Send to</label>
                            <input required type="text" class="form-control" autocomplete="off" id="email">
                        </div>
                    </div>                  
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>                
                <button id="email-confirm-btn" class="btn btn-primary">
                    OK
              </button>
            </div>
          </div>
        </div>
    </div> 
    {{-- email modal ends --}}

    <div class="container">
        <h2><a href="javascript:history.back()">🔙</a>                        
            <span class="badge rounded-pill bg-success">{{$order->invoice_no}}</span>

            {{$order->created_at->format('d-M-Y')}} - {{$order->created_at->format('h:i A')}}
            
            <!-- Example single danger button -->
        <div class="btn-group" style="float:right">
                @if ($order->isExpressOrder())
                  @if ($order->status == 0)
                  <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#payBillModal">
                      ရှင်းမည်
                  </button>
                  @endif
                  
                  <a href="{{ route('orders.printOrderSummary', $order->id) }}" class="btn btn-info">
                      🖨  Print
                  </a>
                @endif

                @if (!$order->isExpressOrder())
                <a href="{{ route('orders.printOrderBill', $order->id) }}" class="btn btn-info">
                    🖨  Print Bill
                </a>
                @endif 

                <button class="btn btn-success" id="send-email-button">
                    📧  Email
                </button>

            </ul>
        </div>
        
        @if ($is_edit_mode)
        <a style="text-decoration:none; font-size: 0.8rem;color:black;" href="?edit=false">Cancel</a>
        @else
        <a style="text-decoration:none; font-size: 0.8rem;color:black" href="?edit=true">✏️ ️Edit</a>
        @endif

        </h2>
        @if(session('error'))
        <div class="alert alert-danger">
            <span>{{session('error')}}</span>
        </div>
        @enderror

        <div class="row">
            <div class="col-md-8 list-container">
                <table class="table table-sm table-hover">
                    <thead>
                        <tr>
                            <th style="padding:9px;">Qty</th>
                            <th></th>
                            <th>အမျိုးအမည်</th>
                            <th>နှုန်း</th>
                            <th>စုစုပေါင်း</th>
                            <th></th>
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
                            <td>
                                @if ($is_edit_mode)
                                <button class="btn cancel-order-menu" data-id="{{$orderMenu->id}}" data-menu-name="{{$orderMenu->menu->name}}" data-menu-quantity="{{$orderMenu->quantity}}">
                                    ❌
                                </button>
                                @endif
                            </td>
                        </tr>
                        @endif
                        @if ($orderMenu->isSummary && $order->table_id == '99999')
                            <tr style="font-weight: 900; border-bottom: 4px solid black;">
                                <td>{{ $orderMenu->menuGroupQty }}</td>
                                <td></td>
                                <td>{{ $orderMenu->menuGroupName }}</td>
                                <td></td>
                                <td >{{ $orderMenu->menuGroupTotal }} ကျပ်</td>
                                <td></td>
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

                <!-- delete logs -->
                <div style="margin-top:1rem;opacity: 0.6;">
                @if ($order->delete_logs)
                    <h4>ဖျက်ထားသည့် မှတ်တမ်းများ</h4>
                    <table class="table table-sm bg-white">                        
                        @foreach ($order->delete_logs as $log)
                        <tr>
                            <td>{{ $log['item_name'] ?? "" }}</td>
                            <td>{{ $log['price'] ?? "" }}</td>
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
                            @forelse($orderMenuGroups as $mg)
                            <tr>
                                <td>{{$mg->name}}</td>
                                <td style="padding:9px;">{{$mg->quantity}}</td>
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
<script>
    //for order menu deleting
    let passcodeModal; 

    //for sending email
    let emailModal;

    window.addEventListener('load', () => {        
        passcodeModal = new bootstrap.Modal(document.getElementById('passcodeModal'), {
            backdrop: true
        })
        emailModal = new bootstrap.Modal(document.getElementById('emailModal'), {
            backdrop: true
        })
    })

    let orderMenuId; //for passcode modal
    let cancelQuantity;
    let cancelModalTitle;

    function cancelOrderMenuBtnHandler(e) {
        orderMenuId = e.target.dataset['id'];
        document.querySelector('#cancel-modal-title').innerHTML = `Cancel ${e.target.dataset['menuName']} x ${e.target.dataset['menuQuantity']}`;
        document.querySelector('#passcode-txt').value = "";
        document.querySelector('#cancel-quantity').value = "";
        passcodeModal.show();                    
        const passcodeConfirmButton = document.querySelector('#passcode-confirm-btn');

        passcodeConfirmButton.addEventListener('click', cancelOrderMenuAction);                    
    }

    const cancelOrderMenuBtns = document.querySelectorAll('.cancel-order-menu');                
    for (cancelOrderMenuBtn of cancelOrderMenuBtns) {
        cancelOrderMenuBtn.addEventListener('click', cancelOrderMenuBtnHandler); 
    }
    
    const orderId = {{$order->id}};
    const waiterIdSelect = document.querySelector('#waiter-id');
    const payBillBtn = document.querySelector('#payBill');
    payBillBtn.addEventListener('click', payBillBtnHandler);
    
    const token=document.querySelector('#_token').value;   
    
    function payBillBtnHandler () {        
        const waiterId = waiterIdSelect.value;
        if (!waiterId) {
            Toastify({
            text: "Waiter ရွေးပါ",
            backgroundColor: "red",
            className: "info",
            }).showToast();
            return;
        }
        if (!confirm("သေချာပါသလား?")) {
            return;
        }
        //temp fix
        fetch(`/api/payBill/${orderId}/${waiterId}/false`, {
            headers: {
                "Content-Type": "application/json",
                "Accept": "application/json",
                "X-Requested-With": "XMLHttpRequest",
                "X-CSRF-Token": token
            },
            credentials: "same-origin",
            method: 'GET'                
        })
        .then (res => res.json())
        .then (res => {
            if (res.isOk) location.reload();
        })
        .catch (err => console.log(err));

    }


    function cancelOrderMenuAction () {
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

        fetch(`/api/orderMenus/cancel/${orderMenuId}/${cancelQuantity}`, {
            method: 'POST',
            headers: {
            "Content-Type": "application/json",
            "Accept": "application/json",
            "X-Requested-With": "XMLHttpRequest",
            "X-CSRF-Token": token
            },
            credentials: "same-origin",
        }) 
        .then(res => res.json())
        .then(res => {            
            if (res.returnToTables) {
                location.href="/admin/pos/tables";
            }
            if (res.returnToExpress) {
                location.href="/admin/express";   
            }
            if (res.isOk) {
                passcodeModal.hide();
                location.reload();                                
            }
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

    const sendEmailBtn = document.querySelector('#send-email-button');
    sendEmailBtn.addEventListener('click', emailBtnHandler);

    
    function emailBtnHandler () {
        emailModal.show();
    }

    const emailConfirmBtn = document.querySelector('#email-confirm-btn');
    emailConfirmBtn.addEventListener('click', () => emailAction(orderId));
    const email = document.querySelector('#email');
    
    function emailAction (orderId) {
        if (!orderId || !email.value) {
            alert("Email ထည့်သွင်းပါ")
            return;
        }
        let valid = true;
        const regex = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        const email_arr = email.value.split(',');
        
        for (var i = 0; i < email_arr.length; i++) {
            if( email_arr[i] === "" || !regex.test(email_arr[i].replace(/\s/g, ""))){
                valid = false;
            }
        }

        if (!valid) {
            alert("Email မှားယွင်းနေသည်")
        }

        emailConfirmBtn.disabled = true;
        fetch(`/api/email/order/${orderId}`, {
            method: 'POST',
            headers: {
            "Content-Type": "application/json",
            "Accept": "application/json",
            "X-Requested-With": "XMLHttpRequest",
            "X-CSRF-Token": token
            },
            credentials: "same-origin",
            body: JSON.stringify({
                orderId,
                email: email.value
            })
        }) 
        .then(res => res.json())
        .then(res => {
            if (res.success && res.success === true) {
                emailModal.hide();
                alert("Email successfully sent!");
            }            
            emailConfirmBtn.disabled = false;
        })
        .catch (err => {
            console.log("Error" + err);
            alert("Error sending email");
            emailConfirmBtn.disabled = false;
        })
    }
</script>
@endsection
