@extends('layouts.client')
@section('style')
<style>
    /* hide cart panel for mobile and small screens */
    @media screen and (max-width: 600px) {
        .cart-panel-container > #cart-panel {
            display: none;
        }
        #cart-modal-button {
            display: inline-block;
        }       
    }
    @media screen and (min-width: 600px) {
        #cart-modal-button {
            display: none;
        }
        #counter {
            display: none;
        }
    }
    .row {
        margin: 0 0px;
        --panel-height: 80vh;
    }
    .menus-panel {
        padding: 8px;
        /* height: var(--panel-height); */
        position: relative;
        /* border: 1px solid #353434; */
        border-radius: 10px;
        overflow-y: auto;
    }
    .menus-grid {        
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
        grid-row-gap: 2rem;
        grid-column-gap: 4px;
        max-height: 65vh;
        overflow-y: auto;
        /* padding-bottom: 3rem; */
    }
    .menus-grid-item {
        width: 100px;
        height: 100px;
        border: 1px solid #d3d2d2;
        /* border-bottom: none; */
        cursor: pointer;
        position: relative;         
        overflow: hidden;
        transition: all 0.3s ease-in-out;
    }
    .menus-grid-item:hover, .menus-grid-item:active {
        /* border: 3px solid green; */
        transform: scale(0.9);
    }
    /* to prevent event to propagate DOWN */
    .menus-grid-item * {
        pointer-events: none;        
    }
    .caption {             
        position: absolute;
        bottom: 0;
        /* right: 0; */
        background-color: purple;
        
        color: white;       
        white-space: nowrap;     
        /* display: inline-block; */
        /* display: block;
        white-space: nowrap;
        overflow: hidden;
        border: 2px solid rgb(155, 155, 155);
        border-top: none;
        background-color: rgb(255, 236, 174);
        text-overflow: ellipsis; */

    }
    .price {
        position: absolute;
        top: 0;
        right: 0;
        background-color: purple;
        color: white;
    }    
    .balance-badge {        
        position: absolute;
        top: 0;
        left: 0;
        min-width:  1rem;
        background-color: blue;
        color: white;
        border-radius: 25%;
    }
    .price::after {
        content: ' ကျပ်';
    }
    .cart-panel {
        border: 1px solid #aca9a9;
        border-radius: 10px;
        padding: 8px;
        height: 85vh;
    }

    .card-footer {
        display: flex;
        justify-content: space-around;
    }
    
    .menugroups-flex-container {        
        white-space: nowrap;
        overflow-x: scroll;       
        margin-bottom: 1rem;
        height: 100px;
        background-color: rgb(241, 238, 238);  
        
        position: sticky;
        top: 0;
    }
    .menugroups-flex-item {
        overflow: hidden;
        display: inline-block;
        /* border: 1px solid black; */
        border-radius: 10%;
        width: 100px;
        background-color: rgb(180, 247, 202); 
        height: 90%;        
        cursor: pointer;            
        position: relative;        
    }
    .menugroups-flex-item  span {
        position: absolute;
        bottom: 0;
        font-weight: 900;
    }
    /* hack for hover to work on mobile (touch) */
    .menugroups-flex-item:hover, .menugroups-flex-item:active {
        border: 4px solid green;
        background-color: #fff;
    }    
    /* .menugroups-flex-item[data-id="all"] {
        background-color: purple;
        color: white;
        /* border: 4px solid rgb(228, 74, 3); */
    } */
    .cart-table {
        color: #fff;
        
    }
    .cart-body {
        position: relative;        
    }
    .sticky {
        position: sticky;
        bottom: 0;
        width: 100%;
        /* height: 2rem; */
        border-radius: 10px;
        background-color: rgb(9, 82, 25);
        padding: 8px;
        text-align: center;
    }

    .header {
        display: flex;
        flex-direction: row;
        justify-content: space-between;
    }
    .header .button {
        font-size: 1.4rem;
    }

</style>
@endsection
@section('content')
<div class="container-fluid mt-5">
    {{-- CSRF token --}}
    <input type="hidden" name="_token" id="_token" value="{{csrf_token()}}">
    <div class="header">

            @if(Auth::guard('admin_account')->check())
            <a class="button" href="{{route('admin.tables')}}">🔙</a>            
            @endif

            @if(Auth::guard('waiter')->check())
            <a href="{{route('waiter.home')}}">🔙</a>            
            @endif

            Table - {{$table->name}}
            @if($current_order)
                @if(Auth::guard('admin_account')->check())
                <a class="btn btn-info" href="{{route('admin.pos.orders', $current_order->id)}}">အသေးစိတ်</a>
                @endif 

                @if(Auth::guard('waiter')->check())
                <a class="btn btn-info" href="{{route('waiter.orders', $current_order->id)}}">အသေးစိတ်</a>
                @endif
            @endif
        
        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#cart-modal" id="cart-modal-button">
          မှာမည့် Menu များ
        </button> 

        @if(Auth::guard('admin_account')->check())
        <select name="waiterId" id="waiterId" style="float: right">
            <option value="">Waiter ရွေးပါ</option>
            @foreach($waiters as $waiter)
            <option value="{{$waiter->id}}">{{$waiter->name}}</option>
            @endforeach
        </select>
        @endif

    </div>

    <div class="row">
        <div class="col-sm-8 menus-panel">
            {{-- <h3>MENU ရွေးရန်</h3> --}}
            {{-- menu group selector - scroll snap --}}
            <div class="menugroups-flex-container">
                <div class="menugroups-flex">
                    <div data-id="all" class="menugroups-flex-item">
                        အားလုံး
                    </div>
                    @forelse($menu_groups as $menu_group)
                    <div data-id="{{$menu_group->id}}" class="menugroups-flex-item">
                        {{$menu_group->name}}
                    </div>
                    @empty
                    NO MENU GROUP
                    @endforelse
                </div>
            </div>

            <input type="text" id="menuSearchInput" class="form-control" placeholder="ရှာပါ" role="search">
            
            <div class="menus-grid">                
                @forelse($menus as $menu)                
                <div 
                    data-menugroup-id="{{$menu->menu_group->id}}"
                    data-menu-id="{{$menu->id}}"
                    data-menu-name="{{$menu->name}}"                
                    data-menu-price="{{$menu->price}}"                
                    data-menu-code="{{$menu->code}}"
                    data-menu-is-stock="{{ $menu->stock_menu()->exists() ? TRUE : FALSE }}"
                    data-menu-stock-balance="{{$menu->stock_menu->balance ?? 0}}"
                    data-print-slip="{{$menu->menu_group->print_slip}}"                    
                    class="menus-grid-item"
                    @if ($menu->image)
                    style="background-size:cover;background-image: url('/storage/menu_images/{{$menu->image}}')">        
                    @else 
                    style="background-size:cover;background-image: url('/images/default.png')">                
                    @endif                    
                    <span class="price">{{$menu->price}}</span>
                    <span class="caption">{{$menu->name}}</span>
                    @if ($menu->stock_menu()->exists())
                    <span class="balance-badge">{{$menu->stock_menu->balance}}</span>
                    @endif
                    
                </div>
                @empty 
                NO MENU
                @endforelse
            </div>
 
        </div>
        <div class="col-sm-4 cart-panel-container">
  
            {{-- panel for larger screens --}}
            <div class="cart-panel card text-warning card-primary bg-success" id="cart-panel">
                <div class="card-body" style="overflow-y: auto;">
                    <table class="table table-hovered cart-table text-white">
                        <thead>
                            <tr>
                                <th>Qty</th>
                                <th></th>
                                <th>အမည်</th>                                
                                <th>နှုန်း</th>
                                <th>စုစုပေါင်း</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order_menus as $order_menu)                            
                            <tr class="cartRowsToBeSaved" 
                            style="font-weight: 900"
                            data-id="{{$order_menu->menu_id}}" 
                            data-price="{{$order_menu->price}}"
                            data-print-slip="{{$order_menu->menu->menu_group->print_slip}}"
                            data-menugroup_id="{{$order_menu->menu->menu_group_id}}"
                            data-is-in-order="true"
                            data-is-foc="{{$order_menu->is_foc == 1 ? 'true' : 'false'}}">
                                <td id="qty">{{$order_menu->quantity}}</td>
                                <td>x</td>
                                <td>{{$order_menu->menu->name}}</td>
                                @if(Auth::guard('admin_account'))

                                @endif
                                <td>
                                    {{$order_menu->price}}
                                </td>
                                <td>{{$order_menu->price*$order_menu->quantity}}</td>
                            </tr>
                   
                            @endforeach
                        </tbody>
                    </table>
                    {{-- <div class="sticky">
                        <i>Total </i> : <b class="subtotal">{{$total}} ကျပ်</b><br>
                        <button class="btn btn-success" id="orderBtn">မှာမည်</button>
                        <button class="btn btn-primary" id="payBtn">ရှင်းမည်</button>
                        <button class="btn btn-danger" id="rollbackBtn"><<<</button>
                    </div> --}}
           
                </div>
                <div class="card-footer" style="display: block">
                    <div class="card-footer-total" style="display:flex;justify-content:space-between">
                        <div></div>
                        <div>
                            <i>Total </i> :&nbsp; <b class="subtotal">{{$total}} ကျပ်</b>                            
                        </div>
                        <div>
                            <input name="print_bill" value="0" class="form-check-input" type="checkbox" id="print_bill">
                            <label class="form-check-label" for="print_bill">
                            Bill ထုတ်မည်
                            </label>                            
                        </div>
                    </div><hr>
                    <div class="card-footer-btns" style="display:flex;justify-content:space-between;">
                        <button class="btn btn-success" id="orderBtn">မှာမည်</button>
                        <button class="btn btn-primary" id="payBtn">ရှင်းမည်</button>
                        <button class="btn btn-danger" id="rollbackBtn"><<<</button>
                    </div>
                </div>
            </div>
     
            {{-- modal for mobile/small screens --}}
            <div class="modal modal-fullscreen" id="cart-modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                  <div class="modal-content">
                    <div class="modal-header">
                      <h5 style="width:100%" class="modal-title" id="exampleModalLabel">
                        <span>Table - {{$table->name}}</span>  
                        <span style="float:right;">{{date("d-m-Y")}}</span>
                        </h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="cart-modal-body">
                      {{-- cart content copied from original cart panel --}}
                      
                    </div>
                   
                  </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
@section('script')
{{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/socket.io/3.0.4/socket.io.js" integrity="sha512-aMGMvNYu8Ue4G+fHa359jcPb1u+ytAF+P2SCb+PxrjCdO3n3ZTxJ30zuH39rimUggmTwmh2u7wvQsDTHESnmfQ==" crossorigin="anonymous"></script> --}}
<script>
    (() => {
        // const socket = io('{{config('app.socket_url')}}');      
        // socket.emit('join-room', {
        //     roomId: 1
        // })
        
        const menuGroupItems=document.querySelectorAll('.menugroups-flex-item');
        const menuItems=document.querySelectorAll('.menus-grid-item');
        const originalMenuItems=[...document.querySelector('.menus-grid').children];
        const cartTable=document.querySelector('.cart-table');
        const cartTableBody=document.querySelector('.cart-table > tbody');
        const cartPanel=document.querySelector('.cart-panel');
        const orderBtn=document.querySelector('#orderBtn');
        const payBtn=document.querySelector('#payBtn');
        const rollbackBtn=document.querySelector('#rollbackBtn');
        const cartModalButton=document.querySelector('#cart-modal-button');
        const menuOptionSelects=document.querySelectorAll('.menu-option');
        const menuSearchInput=document.querySelector('#menuSearchInput');

        //for mobile
        const counter=document.querySelector('#counter');
        
        //attaching event listeners for menu groups
        for(menuGroupItem of menuGroupItems) {
            menuGroupItem.addEventListener('click', menuGroupItemClickHandler);
        }
        //attaching event listeners for menu items
        for(menuItem of menuItems) {
            menuItem.addEventListener('click', menuItemClickHandler);
        }

        menuSearchInput.addEventListener('input', menuSearchInputHandler);
        //attching event listener to orderBtn         
        orderBtn.addEventListener('click', orderBtnClickHandler);        
        //attaching event listener to payBtn
        payBtn.addEventListener('click', payBtnClickHandler);
        //attching event listener to rollbackBtn
        rollbackBtn.addEventListener('click', rollbackBtnClickHandler);
        //modal open listener
        const cartModal=document.querySelector('#cart-modal');
        const modalBody=document.querySelector('.cart-modal-body');
        cartModal.addEventListener('shown.bs.modal', function(e) {            
            const clonedCart=cartPanel.cloneNode(true);
            modalBody.innerHTML=clonedCart.outerHTML;
            //select orderBtn inside modal
            const orderBtn=document.querySelector('.cart-modal-body > .cart-panel > .card-footer > .card-footer-btns > #orderBtn');
            const payBtn=document.querySelector('.cart-modal-body > .cart-panel > .card-footer > .card-footer-btns > #payBtn');
            const rollbackBtn=document.querySelector('.cart-modal-body > .cart-panel > .card-footer > .card-footer-btns > #rollbackBtn');
            //attach event listener inside modal
            //attching event listener to orderBtn         
            orderBtn.addEventListener('click', orderBtnClickHandler);        
            //attaching event listener to payBtn
            payBtn.addEventListener('click', payBtnClickHandler);
            //attching event listener to rollbackBtn
            rollbackBtn.addEventListener('click', rollbackBtnClickHandler);
            
        })


        function filterByMenugroupId(originalMenuItems, menuGroupId) {
            originalMenuItems.forEach(x=>{
                x.style.display='block';
            })
            if(menuGroupId==="all") {
                return;
            }
            originalMenuItems.forEach(x=>{
                if(x.dataset['menugroupId']!==menuGroupId) {
                    x.style.display='none';
                }
            })            
        }

        function filterByTextSearch(originalMenuItems, text) {
            originalMenuItems.forEach(x=>{
                x.style.display='block';
            }) 
            if (!text) {
                return;
            }
            originalMenuItems.forEach (x => {                
                if (!x.dataset['menuName'].includes(text) && !x.dataset['menuCode'].includes(text)) {
                    x.style.display = 'none';
                }
            })            
        }


        function menuGroupItemClickHandler(e) {           
            // const menus=filterByMenugroupId(originalMenuItems, e.target.dataset['id']);
            filterByMenugroupId(originalMenuItems, e.target.dataset['id']);            
            // redrawMenuList(menus);
        }

        function menuSearchInputHandler (e) {
            filterByTextSearch(originalMenuItems, e.target.value);
        }

        var orderMenus=[];
        function updateOrderMenusArr(e) {
            console.log(e.target.dataset);
            const foundIndex=orderMenus.findIndex(x=>x.id==e.target.dataset['menuId']);
            if(foundIndex>-1) {
                orderMenus[foundIndex].quantity++;
            }
            else {
                const menu={
                    menu_id:e.target.dataset['menuId'],                
                    name:e.target.dataset['menuName'],
                    price:e.target.dataset['menuPrice'],
                    printSlip:e.target.dataset['printSlip'],
                    menugroupId:e.target.dataset['menugroupId'],
                    quantity:1
                }
                orderMenus.push(menu);
            }
        }
        function menuItemClickHandler(e) {
            
            let isNew=true;
            // current cart Row clicked
            let cartRow;
            
            let menu;

            //check if it is stock item 
            //then check balance
            const isStockMenu = e.target.dataset['menuIsStock'];

            if (isStockMenu == 1) {
                const stockBalance = e.target.dataset['menuStockBalance'];
                

                if (stockBalance == 0) {
                    Toastify({
                    text: "ပစ္စည်းမရှိတော့ပါ",
                    backgroundColor: "red",
                    className: "info",
                    }).showToast();
                    return;
                }

                e.target.dataset['menuStockBalance'] = stockBalance - 1;

            }

            //existing old cart item
            //check if item already exists
            for(let i of cartTableBody.children) {                
                if (i.dataset['isFoc'] === 'true') {
                    continue;
                }

                const isInOrder = i.dataset['isInOrder'] === 'true';

                //get exiting item
                if(i.dataset['id']===e.target.dataset['menuId'] && !isInOrder) {
                    isNew=false;
                    cartRow=i;
                    break;
                }                                
            }
            // console.warn('existing ends')

            //to send to backend  
            //to prepare orderMenus Array ****
            updateOrderMenusArr(e);
            //for displaying cart ****
            if(isNew) {
                //new cart item 
                menu={
                    id: e.target.dataset['menuId'],
                    name: e.target.dataset['menuName'],
                    price: e.target.dataset['menuPrice'],             
                    print_slip: e.target.dataset['printSlip'],       
                    menugroup_id: e.target.dataset['menugroupId'],
                    quantity: 1
                }
                
                cartTableBody.innerHTML+= `
                    <tr style="opacity:0.6" class="cartRowsToBeSaved" data-id="${menu.id}" data-price="${menu.price}" data-is-foc="false" data-menugroup-id="${menu.menugroup_id}" data-print-slip="${menu.print_slip}">
                        <td id="qty">${1}</td>
                        <td>x</td>
                        <td>${menu.name}</td>
                        <td>
                            ${menu.price}
                        </td>
                        <td>${menu.price * 1}</td>
                    </tr>
                `;
            }
            else {
                //assuming QTY is index  0
                cartRow.children[0].innerHTML=parseInt(cartRow.children[0].innerHTML)+1;
                const quantity=parseInt(cartRow.children[0].innerHTML);
                const name=cartRow.children[2].innerHTML;
                
                //price deferred from dataset
                const price=parseInt(cartRow.dataset['price']);                
                const id=cartRow.dataset['id'];
                
                menu={
                    id,
                    name,
                    price,
                    quantity
                }
                //assuming Total is last index
                const lastCol=cartRow.children[cartRow.children.length-1];
                const total=(price*quantity);
                lastCol.innerHTML=total;
            }            
            calculateCartTotal(); 
            counter.innerHTML=`${menu.name} x ${menu.quantity}`;
        }

        function calculateCartTotal() {
            const subTotalEles=document.querySelectorAll('.subtotal');
            //recalculate subtotal
            let subTotal=0;
            for(let row of cartTableBody.children) {
                subTotal+=parseInt(row.children[row.children.length-1].innerHTML);
            }
            //becuase subtotal appears twice including in modal
            for (let subTotalEle of subTotalEles) {
                subTotalEle.innerHTML=subTotal + " ကျပ်";
            }
        }
        
        function orderBtnClickHandler(e) {
            //TODO:: Reduce the orderMenus arr to become 
            //groupby id and sum(quantity)
            /* implement */

            //catch the cart rows whenever orderbtn is clicked
            //only select cart rows inside panel 
            const cartRows=document.querySelectorAll('.cart-panel-container > #cart-panel .cart-table > tbody > .cartRowsToBeSaved');
            
            //get watier id or "admin"
            let waiterId="{{$currentWaiter}}";            

            //for admin pos 
            //only when admin chooses waiter from dropdown
            const selectedWaiter=document.querySelector('#waiterId');
            if(selectedWaiter) {
                if (!selectedWaiter.value) {
                    Toastify({
                    text: "Waiter ရွေးပါ",
                    backgroundColor: "linear-gradient(to right, #A40606, #D98324)",
                    className: "info",
                    }).showToast();
                    return;
                }
                waiterId=selectedWaiter.value;
            }

            let tableId={{$tableId}};
            const token=document.querySelector('#_token').value;            
   
                
            // api post call to Api ordercontroller             
            // e.target.textContent="Loading"

            //prepare for print array
            let map = {};

            for (let om of orderMenus)
            {
                if (om.printSlip != 1) continue;

                let m = om.menu_id;
                if (map[om.menugroupId]) {

                    let found = map[om.menugroupId].findIndex(x => x.menu_id == om.menu_id);
                    if (found != -1) {                        
                        map[om.menugroupId][found]['qty']++;
                    }
                    else {
                        let s = {};
                        s["menu_id"] = m;
                        s["qty"] = 1;
                        map[om.menugroupId].push(s);                        
                    }

                }
                else {
                    map[om.menugroupId] = [];
                    let s = {};
                    s["menu_id"] = m;
                    s["qty"] = 1;
                    map[om.menugroupId].push(s);                    
                }
                
            }                
            


            fetch(`/api/submitOrder/${tableId}/${waiterId}`, {
                headers: {
                    "Content-Type": "application/json",
                    "Accept": "application/json",
                    "X-Requested-With": "XMLHttpRequest",
                    "X-CSRF-Token": token
                },
                credentials: "same-origin",
                method: 'POST',
                body: JSON.stringify({
                    orderMenus,
                    printOrderMenus: map
                })
            })
            .then(res=>res.json())
            .then(res=>{
                if(res.isOk) {
                    // send to socket io server
                    // TODO: Websocket implementation

                    /*
                    create node web server
                    listen from order page
                    and kitchen page
                    test connection
                    and res.data through
                    */                    
                    // socket.emit('send-order', {
                    //     roomId: 1,
                    //     data: orderMenus
                    // })

                    location.reload();
                }
            });
        }
        function payBtnClickHandler() {
            if (!confirm("သေချာပါသလား?")) {
                return;
            }

            let waiterId="{{$currentWaiter}}";
            let orderId={{$current_order->id ?? "null"}};            
            let printBill = document.querySelector('#print_bill').checked;
            
            //for admin pos 
            //only when admin chooses waiter from dropdown
            const selectedWaiter=document.querySelector('#waiterId');
            if(selectedWaiter) {
                if (!selectedWaiter.value) {
                    Toastify({
                    text: "Waiter ရွေးပါ",
                    backgroundColor: "linear-gradient(to right, #A40606, #D98324)",
                    className: "info",
                    }).showToast();                                        
                    return;
                }
                waiterId=selectedWaiter.value;
            }
            const token=document.querySelector('#_token').value;
            fetch(`/api/payBill/${orderId}/${waiterId}/${printBill}`, {
                headers: {
                    "Content-Type": "application/json",
                    "Accept": "application/json",
                    "X-Requested-With": "XMLHttpRequest",
                    "X-CSRF-Token": token
                },
                credentials: "same-origin",
                method: 'GET'                
            })
            .then(res=>res.json())
            .then(res=>{
                if(res.isOk) {
                    location.reload();
                }
                else {
                    Toastify({
                    text: "ရှင်းလို့မရသေးပါ",
                    backgroundColor: "linear-gradient(to right, #A40606, #D98324)",
                    className: "info",
                    }).showToast();
                    return;                   
                }
            })
            // .catch(err=>console.log(err))
        }

        function rollbackBtnClickHandler(e) {
            //remove last action in orderMenus array
            const popedOrderMenu=orderMenus.pop();

            //early return if there is nothing to rollback
            if(!popedOrderMenu) return;

            let cartRowToBeUpdated;

            //search cart row in display
            for(let i of cartTableBody.children) {
                const isInOrder = i.dataset['isInOrder'] === 'true';
                const isFoc = i.dataset['isFoc'] === 'true';
                if(i.dataset['id']===popedOrderMenu.menu_id 
                && !isFoc
                && !isInOrder) {
                    //ignore FOC items
                    cartRowToBeUpdated=i;
                    break;
                }                
            }

            //update cart display
            //remove the current row if quantity becomes  0
            if(parseInt(cartRowToBeUpdated.children[0].innerHTML)===1) 
                cartRowToBeUpdated.parentNode.removeChild(cartRowToBeUpdated);
            
            let existingQty = parseInt(cartRowToBeUpdated.children[0].innerHTML);
            let existingPrice = parseInt(cartRowToBeUpdated.children[3].innerHTML);
            
            let rowTotal = cartRowToBeUpdated.children[cartRowToBeUpdated.children.length - 1];
            cartRowToBeUpdated.children[0].innerHTML = existingQty - 1;
            rowTotal.innerHTML = parseInt(rowTotal.innerHTML) - existingPrice;
            calculateCartTotal();
            //TODO: update cart display in modal too
            //check if the user is clicking from modal
            if(e.target.parentNode.parentNode.parentNode.parentNode.classList.contains('cart-modal-body')) {
                let c=document.querySelector('.cart-modal-body > .cart-panel > .card-body > .cart-table > tbody');
                let cartRowToBeUpdatedModal;
                
                //search cart row in display for modal**
                for(let i of c.children) {
                    const isInOrder = i.dataset['isInOrder'] === 'true';
                    const isFoc = i.dataset['isFoc'] === 'true';
                    if(i.dataset.id===popedOrderMenu.menu_id
                    && !isFoc
                    && !isInOrder) {
                        cartRowToBeUpdatedModal=i;
                        break;
                    }
                }

                //update cart display
                //remove the current row if quantity becomes  0
                if(parseInt(cartRowToBeUpdatedModal.children[0].innerHTML)===1) 
                    cartRowToBeUpdatedModal.parentNode.removeChild(cartRowToBeUpdatedModal);
                    
                let existingQty = parseInt(cartRowToBeUpdatedModal.children[0].innerHTML);
                let existingPrice = parseInt(cartRowToBeUpdatedModal.children[3].innerHTML);
            
                let rowTotal = cartRowToBeUpdatedModal.children[cartRowToBeUpdatedModal.children.length - 1];
                cartRowToBeUpdatedModal.children[0].innerHTML = existingQty - 1;
                rowTotal.innerHTML = parseInt(rowTotal.innerHTML) - existingPrice; 
                calculateCartTotal();
            }
        }
        

    })();
</script>
@endsection