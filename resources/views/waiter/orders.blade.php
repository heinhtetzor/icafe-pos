@extends('layouts.client')

@section('style')
@endsection
@section('content')
    <div class="container-fluid mt-5">
        {{-- CSRF token --}}
        <input type="hidden" name="_token" id="_token" value="{{csrf_token()}}">
        <table class="table"  id="ordersTable">
            <thead>
                <tr>
                    <td>အော်ဒါ အမှတ်</td>
                    <td>Menu အမည်</td>
                    <td>ကျသင့်ငွေ</td>
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
        const socket = io('http://127.0.0.1:5000');

        socket.emit('join-room', {
            roomId: 1
        })

        socket.on('deliver-to-customer', data=> {            
            fetchOrderMenus();
        })

        const token=document.querySelector('#_token').value;
        const id=window.location.pathname.split('/')[2];
        const table=document.querySelector('#ordersTable')

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
                res.orderMenus.forEach(orderMenu=> {
                    table.innerHTML+=`
                        <tr>
                            <td>${res.order.id}</td>
                            <td>${orderMenu.menu.name} x ${orderMenu.quantity}</td>
                            <td>${orderMenu.menu.price * orderMenu.quantity}</td>
                            <td>${new Date(orderMenu.created_at).toLocaleString()}</td>
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
                `
                
            })            
        }


    })()
</script>
@endsection
