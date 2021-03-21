@extends('layouts.client')

@section('style')
@endsection
@section('content')
    <div class="container mt-5">

        {{-- CSRF token --}}
        <input type="hidden" name="_token" id="_token" value="{{csrf_token()}}">
        <h3>
            <a href="{{url()->previous()}}">🔙 </a>
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/socket.io/3.0.4/socket.io.js" integrity="sha512-aMGMvNYu8Ue4G+fHa359jcPb1u+ytAF+P2SCb+PxrjCdO3n3ZTxJ30zuH39rimUggmTwmh2u7wvQsDTHESnmfQ==" crossorigin="anonymous"></script>
<script type="text/javascript">
    (()=> {
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
                res.orderMenus.forEach(orderMenu=> {
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
                                    <option ${orderMenu.is_foc === 1 ? 'selected' : ''} value="foc">
                                        FOC
                                    </option>
                                </select>
                            </td>
                            <td>
                                <button class="btn btn-danger cancel-order-menu" data-id="${orderMenu.id}">
                                    <i style="pointer-events:none;" class="bi bi-x-octagon"></i>
                                </button>
                            </td>
                            @endif
                            <td>${new Date(orderMenu.created_at).toLocaleString('en-IN')}</td>
                            <td>${orderMenu.status===0 ? "🟠" : "🟢"}</td>
                        </tr>            
                        `
                })                                
                table.innerHTML+=`
                    <tfoot>
                        <tr>
                            <th style="text-align:right" colspan="2">စုစုပေါင်း</th>
                            <th>${res.total}</th>
                            <th colspan="2"></th>
                        </tr>
                    </tfoot>
                `;
                
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
                    else if (e.target.value === 'undo') {
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

                function cancelOrderMenuBtnHandler(e) 
                {
                    const id = e.target.dataset['id'];
                    fetch(`/api/orderMenus/cancel/${id}`, {
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
                                socket.emit('send-order', {
                                    roomId: 1
                                })
                            }
                        })
                }
            })            
        }


    })()
</script>
@endsection
