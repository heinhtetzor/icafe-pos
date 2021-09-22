@extends('layouts.client')

@section('style')
@endsection
@section('content')
<div class="container mt-5">        
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

        {{-- CSRF token --}}
        <input type="hidden" name="_token" id="_token" value="{{csrf_token()}}">
        <h3>
            <a href="javascript:history.back()">🔙 </a>
            <span id="orderNumber"></span>
        </h3>   
        <table class="table" id="ordersTable">
            <thead>
                <tr>                    
                    <td>Menu အမည်</td>
                    <td>Waiter</td>
                    <td>ကျသင့်ငွေ</td>
                    @if(Auth::guard('admin_account')->check())
                    <td></td>
                    <td></td>
                    @endif
                    <td>အချိန်</td>
                    <td>Status</td>
                </tr>
            </thead>
            <tbody>
                
            </tbody>
        </table>
    </div>
@endsection
@section('script')

<script type="text/javascript" src="/js/socket.io.js"></script>
<script type="text/javascript">
    (()=> {
        let passcodeModal;
        
        let orderMenuId; //for passcode modal
        let cancelQuantity;
        let cancelModalTitle;

        window.addEventListener('load', () => {        
            passcodeModal = new bootstrap.Modal(document.getElementById('passcodeModal'), {
                backdrop: true
            })
        })
        const socket = io('{{config('app.socket_url')}}');

        socket.emit('join-room', {
            roomId: 1
        })

        socket.on('deliver-to-customer', data=> {            
            fetchOrderMenus();
        })

        const token=document.querySelector('#_token').value;
        
        let id;
        
        if(window.location.pathname.split('/').length===6) {
            id=window.location.pathname.split('/')[4];
        }
        else {
            id=window.location.pathname.split('/')[2];
        }        

        const table=document.querySelector('#ordersTable > tbody');
        const orderNumber=document.querySelector('#orderNumber');

        fetchOrderMenus();

        function fetchOrderMenus() {
            fetch(`/api/waiter/${id}/orders`, {
                headers: {
                    "Content-Type": "application/json",
                    "Accept": "application/json",
                    "X-Requested-With": "XMLHttpRequest",
                    "X-CSRF-Token": token
                },
                credentials: "same-origin",
                method: 'GET'
            })
            .then(res=> res.json())
            .then(res=> {                            
                table.innerHTML="";
                orderNumber.innerHTML="Order No. " + res.order.invoice_no;
                res.orderMenus.data.forEach(orderMenu=> {
                    //if waiter is null, it is ordered by admin
                    let orderedBy=orderMenu.waiter ? orderMenu.waiter.name : "Admin";
                    table.innerHTML+=`
                        <tr>                            
                            <td>${orderMenu.menu.name} x ${orderMenu.quantity}</td>
                            <td>
                                <span class="badge bg-primary">
                                    ${orderedBy}
                                    </span>
                            </td>
                            <td>${orderMenu.price * orderMenu.quantity}</td>
                            @if(Auth::guard('admin_account')->check())
                            <td>
                                <select data-id="${orderMenu.id}" class="menu-option">
                                    <option value="undo">-----</option>
                                    <option ${orderMenu.is_foc == 1 ? 'selected' : ''} value="foc">
                                        FOC
                                    </option>
                                </select>
                            </td>
                            <td>
                                <button class="btn cancel-order-menu" data-id="${orderMenu.id}" data-menu-name="${orderMenu.menu.name}" data-menu-quantity="${orderMenu.quantity}">
                                ❌
                                </button>
                            </td>
                            @endif
                            <td>${new Date(orderMenu.created_at).toLocaleString('en-IN')}</td>
                            <td>${orderMenu.status==0 ? "🟠" : "🟢"}</td>
                        </tr>            
                        `
                })                                
                // table.innerHTML+=`
                //     <tfoot>
                //         <tr>
                //             <th style="text-align:right" colspan="2">စုစုပေါင်း</th>
                //             <th>${res.total}</th>
                //             <th colspan="2"></th>
                //         </tr>
                //     </tfoot>
                // `;
                
                const menuOptionSelects=document.querySelectorAll('.menu-option');
                for (menuOptionSelect of menuOptionSelects) {                    
                    menuOptionSelect.addEventListener('change', menuOptionSelectorHandler);
                }

                const cancelOrderMenuBtns = document.querySelectorAll('.cancel-order-menu');                
                for (cancelOrderMenuBtn of cancelOrderMenuBtns) {
                    cancelOrderMenuBtn.addEventListener('click', cancelOrderMenuBtnHandler); 
                }

                function menuOptionSelectorHandler (e)
                {
                    const id = e.target.dataset.id;
                    const token=document.querySelector('#_token').value;
                    if (e.target.value === 'foc') {
                        fetch(`/api/orderMenus/makeFoc/${id}`, {
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
                            if (res.isOk) {                                
                                fetchOrderMenus();
                            }
                        })
                        .catch(err => console.log(err));
                    }
                    else if (e.target.value == 'undo') {
                        fetch(`/api/orderMenus/undoOption/${id}`, {
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
                            if (res.isOk) {
                                fetchOrderMenus();
                            }
                        })
                        .catch(err => console.log(err));
                    }            
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
                            socket.emit('send-order', {
                                roomId: 1
                            }) 

                            if (res.returnToTables) {
                                location.href="/admin/pos/tables";
                            }
                            if (res.returnToExpress) {
                                location.href="/admin/express";   
                            }
                            if (res.isOk) {
                                passcodeModal.hide();
                                fetchOrderMenus();                                
                                socket.emit('send-order', {
                                    roomId: 1
                                })
                                location.reload();
                            }
                        })
                        .catch (err => {
                            console("Error" + err);
                        })
                }

                function cancelOrderMenuBtnHandler(e) {
                    orderMenuId = e.target.dataset['id'];
                    document.querySelector('#cancel-modal-title').innerHTML = `Cancel ${e.target.dataset['menuName']} x ${e.target.dataset['menuQuantity']}`;
                    document.querySelector('#passcode-txt').value = "";
                    document.querySelector('#cancel-quantity').value = "";
                    passcodeModal.show();                    
                    const passcodeConfirmButton = document.querySelector('#passcode-confirm-btn');

                    passcodeConfirmButton.addEventListener('click', cancelOrderMenuAction);                    
                }
            })            
        }


    })()
</script>
@endsection
