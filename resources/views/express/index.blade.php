@extends('layouts.client')
@section('style')
<style>
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
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
        grid-row-gap: 2rem;
        grid-column-gap: 4px;
        max-height: 70vh;
        overflow-y: scroll;
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
    .price::after {
        content: ' ကျပ်';
    }
    .top {
        display: flex;
        justify-content: space-between;
    }
    .menugroups-flex-container {        
        white-space: nowrap;
        overflow-x: scroll;       
        margin-bottom: 1rem;
        height: 70px;
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
</style>
@endsection
@section('content')
<div class="container">
    <input type="hidden" name="_token" id="_token" value="{{csrf_token()}}">
    {{-- bulk insert modal --}}
    <div class="modal fade" id="bulkInsertModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="exampleModalLabel">မှာရန်</h5>
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
                            <input type="number" class="form-control" id="modal-menu-quantity">
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
        <span class="badge bg-primary" style="font-size:1rem">
            Started time - {{$order->created_at->format('h:i a')}}  {{ $order->created_at->format('d-M-Y') }}
        </span>
        <span class="" id="ticker">
        </span>
        <div>
            <a class="btn btn-warning" href="{{route('express.show', $order->id)}}">အကျဉ်းချုပ်</a>
        
            @if(Auth::guard('admin_account')->check())
            <a class="btn btn-info" href="{{route('admin.pos.orders', $order->id)}}">အသေးစိတ်</a>
            @endif 

            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#payBillModal">
                ရှင်းမည်
            </button>

            <button class="btn btn-danger" id="delete" onclick="deleteHandler()">
                Delete
            </button>
        </div>
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
    <div class="menus-grid">
        @foreach ($menus as $menu)
        <div 
        data-menugroup-id="{{$menu->menu_group->id}}"
        data-menu-id="{{$menu->id}}"
        data-menu-name="{{$menu->name}}"                
        data-menu-price="{{$menu->price}}"                
        data-menu-code="{{$menu->code}}"
        class="menus-grid-item"
        @if ($menu->image)
        style="background-size:cover;background-image: url('/storage/menu_images/{{$menu->image}}')">        
        @else 
        style="background-size:cover;background-image: url('/images/default.png')">                
        @endif
        <span class="price">{{$menu->price}}</span>
        <span class="caption">{{$menu->name}}</span>
        </div>     
        @endforeach
    </div>
</div>
@endsection
@section('script')
<script>    
    let bulkInsertModal;
    window.addEventListener('load', () => {        
        bulkInsertModal = new bootstrap.Modal(document.getElementById('bulkInsertModal'), {
            backdrop: true
        })
    })
    const token=document.querySelector('#_token').value;            

    const ticker = document.querySelector('#ticker');    

    const orderId = {{$order->id}};
    const waiterIdSelect = document.querySelector('#waiter-id');

    const menuGridItems = document.querySelectorAll('.menus-grid-item');
    const menuGroupItems=document.querySelectorAll('.menugroups-flex-item');

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

    const modalMenuOrderBtn = document.querySelector('#modal-menu-order-btn');
    const modalMenuName = document.querySelector('#modal-menu-name');
    const modalMenuQuantity = document.querySelector('#modal-menu-quantity');
    const modalMenuId = document.querySelector('#modal-menu-id');
    const modalMenuPrice = document.querySelector('#modal-menu-price');

    modalMenuOrderBtn.addEventListener('click', modalMenuOrderBtnHandler);

    function menuGridItemRightClickHandler (e) {        
        e.preventDefault();
        modalMenuName.value = e.target.dataset['menuName'];
        modalMenuId.value = e.target.dataset['menuId'];
        modalMenuPrice.value = e.target.dataset['menuPrice'];
        modalMenuQuantity.value = e.target.dataset['menuQuantity'];
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
        fetch(`/api/payBill/${orderId}/${waiterId}`, {
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
        const waiterId = 1;        
        const orderId = {{$order->id}};        

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
            ticker.classList.add('badge', 'bg-success');
            ticker.innerHTML = `${menuName} x ${menuQuantity}`;
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