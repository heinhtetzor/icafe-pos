@extends('layouts.client')
@section('style')
<style>
    /* mobile screens */
    @media screen and (max-width: 600px) {
        .left-container {
            width: 100% !important;
        }
        .right-container {
            display: none;
        }
        .started-time {
            display: none;
        }
        .menus-grid {
            grid-template-columns: repeat(auto-fill, minmax(100px, 1fr)) !important;

        }
        .menus-grid-item {
            width: 100px !important;
            height: 100px !important;
        }
        .caption {
            width: 100px !important;
        }
    }

    /* large screens */
    @media screen and (min-width: 600px) {
        .menugroups-top-container {
            display: none;
        }
    }

    #ticker 
    {
        display: none;
    }
    .container {        
        margin-top: 4rem;
    }
    .menus-panel {
        padding: 8px;
        /* height: var(--panel-height); */
        position: relative;
        /* border: 1px solid #353434; */
        border-radius: 10px;
        overflow-y: scroll;
    }
    .menus-grid {
        padding-top: 5px;
        padding-left: 5px;
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
        grid-row-gap: 2rem;
        grid-column-gap: 12px;
        max-height: 70vh;
        overflow-y: scroll;
        /* padding-bottom: 3rem; */
    }
    .menus-grid-item {
        width: 120px;
        height: 120px;
        /* border: 1px solid #d3d2d2; */
        /* border-bottom: none; */
        cursor: pointer;
        position: relative;         
        overflow: hidden;
        transition: all 0.3s ease-in-out;
        box-shadow: rgba(0, 0, 0, 0.16) 0px 10px 36px 0px, rgba(0, 0, 0, 0.06) 0px 0px 0px 1px;
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
        background-color: black;
        width: 120px;
        color: white;       
        word-wrap: break-word;

    }
    .price {
        position: absolute;
        top: 0;
        right: 0;
        background-color: black;
        color: white;
        border-radius: 8px;
        padding: 5px;
    }
    .stock-mark {
        position: absolute;
        top: 0;
        left: 0;
        background-color: black;
        color: white;
        border-radius: 8px;
        padding: 5px;
        font-size: 1.4rem;
    }
    .price::after {
        content: ' ကျပ်';
    }
    .top {
        margin-top: 3.5rem;
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
    }
    .parent-container {
        display: flex;
        flex-direction: row;
    }
    .left-container {
        width: 70%;
    }
    .right-container {
        width: 30%;
    }
    .menugroups-flex {  
        display: grid;      
        grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
        grid-row-gap: 2rem;
        grid-column-gap: 4px;
        overflow-y: scroll;       
        max-height: 70vh;
        background-color: rgb(241, 238, 238);          
    }
    .menugroups-flex-item {
        /* width: 100px;
        height: 100px; */
        height: 100px;
        border-radius: 10%;
        background-color: rgb(180, 247, 202); 
        cursor: pointer;            
        position: relative;     
        overflow: hidden;   
        box-shadow: rgba(0, 0, 0, 0.1) 0px 0px 5px 0px, rgba(0, 0, 0, 0.1) 0px 0px 1px 0px;
        font-weight: 1000;
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
    .stock_menu_caption {
        background-color:  black;
    }
    .stock_menu_price {
        background-color:  black;
    }
    #history-container {
        display: flex;
        flex-direction: column;
        height: 30px;
        overflow-x: scroll;
        overflow-y: hidden;
        white-space: nowrap;
        font-size: 0.75rem;
        font-weight: 700;
    }
    .menugroups-top-container {
        white-space: nowrap;
        overflow-x: scroll;
        overflow-y: hidden;
        height: 40px;
    }
    .menugroups-top-container-item {
        display: inline-block;
        padding: 3px;
    }
</style>
@endsection
@section('content')
<div class="container-fluid">
    <input type="hidden" name="_token" id="_token" value="{{csrf_token()}}">
    {{-- bulk insert modal --}}
    <div class="modal fade" id="bulkInsertModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="exampleModalLabel">မှာရန် <a id="modal-menu-edit-link"> &nbsp; ✏️ Edit</a></h5>
              
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="modal-menu-id">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="modal-menu-name">Menu အမည်</label>
                            <input disabled type="text" class="form-control" id="modal-menu-name">
                        </div>  
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="modal-menu-price">နှုန်း</label>
                            <input type="text" disabled class="form-control" id="modal-menu-price">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="modal-menu-quantity">အရေအတွက်</label>
                            <input value="1" type="number" class="form-control" id="modal-menu-quantity" min="0">
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>                
                <button id="modal-menu-order-btn" class="btn btn-primary">
                    မှာမည်
              </button>
            </div>
          </div>
        </div>
      </div> 

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

    <div class="top">
        <div>
            @if(Auth::guard('admin_account')->check())  
                <a style="font-size: 1.4rem; text-decoration: none; margin-right: 1rem;" href="{{route('admin.home')}}">🔙 </a>
            @endif
            @if(Auth::guard('waiter')->check())  
                <a style="font-size: 1.4rem; text-decoration: none; margin-right: 1rem;" href="{{route('waiter.home')}}">🔙 </a>
            @endif
            <span class="started-time badge bg-primary" style="font-size:1rem">
                Started time - {{$order->created_at->format('h:i a')}}  {{ $order->created_at->format('d-M-Y') }}
            </span>
        </div>
        <span class="" id="ticker">
        </span>
        @if(Auth::guard('admin_account')->check())  
        <div>
            <a class="btn btn-warning" href="{{route('express.show', $order->id)}}">
                <i class="bi bi-calendar3"></i>
            </a>
        
            <a class="btn btn-info" href="{{route('admin.pos.orders', $order->id)}}">
                <i class="bi bi-card-list"></i>
            </a>

            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#payBillModal">
                <i class="bi bi-cash"></i>
            </button>

            <button class="btn btn-danger" id="delete" onclick="deleteHandler()">
                <i class="bi bi-trash"></i>
            </button>
        </div>
        @endif 
    </div>
    <hr>

    {{-- @if(Auth::guard('waiter')->check())
    <a class="btn btn-info" href="{{route('waiter.orders', $current_order->id)}}">အသေးစိတ်</a>
    @endif --}}

    <form id="delete-form" class="hidden" action="{{ route('express.destroy', $order->id) }}" method="post">
        @method('DELETE')
        @csrf
        <input type="hidden" name="id" value="{{ $order->id }}">
    </form>
    <div id="history-container">

    </div>
    <div class="menugroups-top-container">
        <div data-id="all" class="menugroups-top-container-item ">
            အားလုံး
        </div>
        @forelse($menu_groups as $menu_group)
        <div data-id="{{$menu_group->id}}" class="menugroups-top-container-item " style="background-color:{{$menu_group->color}}">
            {{$menu_group->name}}
        </div>
        @empty
        NO MENU GROUP
        @endforelse
    </div>
    <input type="text" id="menuSearchInput" class="form-control mb-4" placeholder="ရှာပါ" role="search">
    <div class="parent-container">
        <div class="left-container">

            <div class="menus-grid">
                @foreach ($menus as $menu)
                <div 
                data-menugroup-id="{{$menu->menu_group->id}}"
                data-menu-id="{{$menu->id}}"
                data-menu-name="{{$menu->name}}"                
                data-menu-price="{{$menu->price}}"                
                data-menu-code="{{$menu->code}}"
                class="menus-grid-item"
                title="{{$menu->name}} ({{$menu->code}})" 

                @if ($menu->image)
                style="box-shadow: 0 0 0 2px {{$menu->menu_group->color}}, 10px 10px 0 0 {{$menu->menu_group->color}};background-size:cover;background-image: url('/storage/menu_images/{{$menu->image}}')">        
                @else
                style="box-shadow: 0 0 0 2px {{$menu->menu_group->color}}, 10px 10px 0 0 {{$menu->menu_group->color}};background-repeat:no-repeat;background-size:100px 100px;background-image: url('/images/default-menu.svg')">        
                @endif
                @if ($menu->stock_menu != null && $menu->stock_menu->status == 1)
                <span class="stock-mark">📈</span>
                @endif
                <span class="price {{ $menu->stock_menu != null && $menu->stock_menu->status == 1 ? 'stock_menu_price' : '' }}">{{$menu->price}}</span>
                <span class="caption {{ $menu->stock_menu != null && $menu->stock_menu->status == 1 ? 'stock_menu_caption' : '' }}">{{$menu->name}}</span>
                </div>     
                @endforeach
            </div>
        </div>
        <div class="right-container">
            <div class="menugroups-flex-container">
                <div class="menugroups-flex">
                    <div data-id="all" class="menugroups-flex-item">
                        အားလုံး
                    </div>
                    @forelse($menu_groups as $menu_group)
                    <div data-id="{{$menu_group->id}}" class="menugroups-flex-item" style="background-color:{{$menu_group->color}}">
                        {{$menu_group->name}}
                    </div>
                    @empty
                    NO MENU GROUP
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('script')
<script>    
    const orderAlert = new Audio('/sounds/quick-chime.wav');

    let bulkInsertModal;
    window.addEventListener('load', () => {        
        bulkInsertModal = new bootstrap.Modal(document.getElementById('bulkInsertModal'), {
            backdrop: true
        })
    })
    const token=document.querySelector('#_token').value;            

    const ticker = document.querySelector('#ticker');    

    const historyContainer = document.querySelector("#history-container");
    let historyArr = []

    const menuSearchInput=document.querySelector('#menuSearchInput');
    
    menuSearchInput.addEventListener('input', menuSearchInputHandler);

    const orderId = {{$order->id}};
    const waiterIdSelect = document.querySelector('#waiter-id');

    const menuGridItems = document.querySelectorAll('.menus-grid-item');
    const menuGroupItems=document.querySelectorAll('.menugroups-flex-item');
    const menuGroupTopItems = document.querySelectorAll('.menugroups-top-container-item');

    const payBillBtn = document.querySelector('#payBill');
    payBillBtn.addEventListener('click', payBillBtnHandler);

    const originalMenuItems=[...document.querySelector('.menus-grid').children];
    menuGridItems.forEach (x => {
        x.addEventListener('click', menuGridItemHandler);
    })
    menuGridItems.forEach (x => {
        x.addEventListener('contextmenu', menuGridItemRightClickHandler);
    })
    //attaching event listeners for menu groups
    for(menuGroupItem of menuGroupItems) {
        menuGroupItem.addEventListener('click', menuGroupItemClickHandler);
    }
    for(menuGroupTopItem of menuGroupTopItems) {
        menuGroupTopItem.addEventListener('click', menuGroupItemClickHandler);
    }

    const modalMenuOrderBtn = document.querySelector('#modal-menu-order-btn');
    const modalMenuName = document.querySelector('#modal-menu-name');
    const modalMenuQuantity = document.querySelector('#modal-menu-quantity');
    const modalMenuId = document.querySelector('#modal-menu-id');
    const modalMenuPrice = document.querySelector('#modal-menu-price');
    const modalMenuEditLink = document.querySelector('#modal-menu-edit-link');

    modalMenuOrderBtn.addEventListener('click', modalMenuOrderBtnHandler);

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

    function menuSearchInputHandler (e) {
        filterByTextSearch(originalMenuItems, e.target.value);
    }

    function menuGridItemRightClickHandler (e) {        
        e.preventDefault();
        modalMenuName.value = e.target.dataset['menuName'];
        modalMenuId.value = e.target.dataset['menuId'];
        modalMenuPrice.value = e.target.dataset['menuPrice'];
        modalMenuQuantity.value = e.target.dataset['menuQuantity'] || 1;
        modalMenuEditLink.href = `/admin/menus/${e.target.dataset['menuId']}/edit`;
        bulkInsertModal.show();
    }

    function modalMenuOrderBtnHandler () {        
        const menuId = modalMenuId.value;
        const menuName = modalMenuName.value;
        const menuQuantity = modalMenuQuantity.value;        
        const menuPrice = modalMenuPrice.value;
        
        addOrderMenuApiCall(menuId, menuName, menuPrice, menuQuantity);

        bulkInsertModal.hide();
    }



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

    function menuGroupItemClickHandler(e) {                   
        filterByMenugroupId(originalMenuItems, e.target.dataset['id']);                    
    }

    function menuGridItemHandler (e) {
        const menuId = e.target.dataset['menuId'];        
        const menuName = e.target.dataset['menuName'];        
        const menuPrice = e.target.dataset['menuPrice'];        

        addOrderMenuApiCall (menuId, menuName, menuPrice, 1);
    }

    function addOrderMenuApiCall (menuId, menuName, menuPrice, menuQuantity) {
        const orderId = {{$order->id}};        
        const waiterId = {{$order->waiter_id}}; //waiter who started express session

        fetch(`/api/addOrderMenu`, {
            headers: {
                "Content-Type": "application/json",
                "Accept": "application/json",
                "X-Requested-With": "XMLHttpRequest",
                "X-CSRF-Token": token
            },
            credentials: "same-origin",
            method: 'POST',
            body: JSON.stringify({
                waiterId,
                menuId,
                menuPrice,
                orderId,
                quantity: menuQuantity
            })
        })
        .then (res => res.json())
        .then (res => {     
            console.log(res)

            if (!res.success) {
                    Toastify({
                    text: res.message,
                    backgroundColor: "red",
                    className: "info",
                    }).showToast();
                    return;
            }
            
            ticker.classList.add('badge', 'bg-success');            
            let menuQuantityDisplay = menuQuantity;
            if (ticker.innerHTML.split(' x ')[0] === menuName) {
                menuQuantityDisplay = +ticker.innerHTML.split(' x ')[1] + 1;     
                console.log(menuQuantityDisplay)                           
                ticker.innerHTML = `${menuName} x ${menuQuantityDisplay}`;
                historyContainer.innerHTML += ticker.innerHTML;
            }
            ticker.innerHTML = `${menuName} x ${menuQuantityDisplay}`;

            //truncate history items
            historyArr = historyArr.slice(0, 50);
            let historyMenuQuantityDisplay = menuQuantity;  
            if (historyArr.length > 0 && historyArr[0].split(' x ')[0] === menuName) {
                historyMenuQuantityDisplay = +historyArr[0].split(' x ')[1] + 1;
                historyArr[0] = `${menuName} x ${historyMenuQuantityDisplay}`;              
            } else {
                let arrItem = `${menuName} x ${historyMenuQuantityDisplay}`;
                historyArr.unshift(arrItem);
            }

            historyContainer.innerHTML = "";
            for (let historyItem of historyArr) {
                historyContainer.innerHTML += `${historyItem}  &nbsp;&nbsp;&nbsp; `;
            }
            orderAlert.play();
        })
        .catch (err => {
            alert("Error");
            console.log(err.message)
        })
    }

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

@endsection