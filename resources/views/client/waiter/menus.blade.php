@extends('layouts.client')
@section('style')
<style>
    .container {
        padding: 3rem;
    }
    .menu-container {
        margin-bottom: 4rem;
        user-select: none;
    }
    .menugroup-name {
        text-align: center;
        padding: 1rem;
    }
    .menu-item-container {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
        margin-bottom: 1rem;
    }
    .menu-item {
        border: 1px solid #bcbcbc;
        height: 100px;        
    }
    .menu-item:active {
        border: 1px solid blue;
    }
    .bottom-bar {
        position: fixed;
        bottom: 0;
        width: 100%;
        height: 4rem;   
        display: flex;
        justify-content: center;
        align-items: center; 
        background-color: black;
        color: white;   
    }
    .add-to-basket-sheet {
        position: fixed;
        background-color: #000;
        color: #fff; 
        width: 100%;
        min-height: 100%;
        top: 0;
        left: 0;
        transform: translateX(-1000px);
        transition: all 1s ease-in-out;
        user-select: none;
        padding: 1rem;
    }
    .close-add-to-basket-sheet-btn {
        float: right;
        cursor: pointer;
    }
    .add-to-basket-sheet-animated {
        visibility: visible;
        transform: translateX(0);
    }
    .basket-body-item {
        padding: 1rem;
        display: flex;
        justify-content: space-between;
    }
    .qtyIcon {
        display: inline-block;
        font-size: 2rem;
        width: 33px;
        border: 1px solid #fff;
        border-radius: 50%;
        text-align: center;
        cursor: pointer;
    }
</style>
@endsection

@section('content')
    <div class="container">
        <h1>Menus</h1>
        <div class="menu-container">
            @foreach($menuGroups as $menuGroup)
            <div class="menugroup">
                <details open>
                    <summary class="menugroup-name">{{$menuGroup->name}}</summary>
                    <div class="menu-item-container">
                        @foreach($menuGroup->menus as $menu)
                        <div data-menu-id="{{$menu->id}}" data-menu-name="{{$menu->name}}" data-menu-price="{{$menu->price}}" class="menu-item">{{$menu->name}}</div>
                        @endforeach
                    </div>
                </details> 

            </div>
            @endforeach
        </div>
        {{-- hidden at startup (add to basket sheet)--}}
        <div class="add-to-basket-sheet">
            <h3>Basket
                <span class="close-add-to-basket-sheet-btn">[X]</span>
            </h3>
            <div class="basket-body">
             
            </div>
        </div>
    </div>  

    <div class="bottom-bar">
        <div class="bottom-bar-text">
            0 items
        </div>
    </div>

@endsection
@section('script')
<script>
    
    (function() {
        redirectIfNoUser();

        const bottomBarText = document.querySelector('.bottom-bar-text');
        const addToBasketSheet = document.querySelector('.add-to-basket-sheet');
        const closeAddToBasketSheetBtn = document.querySelector('.close-add-to-basket-sheet-btn');
        const menuItems = document.querySelectorAll('.menu-item-container');
 

        const basketArr = [];

        bottomBarText.addEventListener('click', handleClickBottomBarText);
        closeAddToBasketSheetBtn.addEventListener('click', handleCloseAddToBasketSheetBtn);

        

        function handleQtyPlusBtn (e) {
           e.target.previousElementSibling.value = parseInt(e.target.previousElementSibling.value) + 1;
        }
        function handleQtyMinusBtn (e) {
           if (parseInt(e.target.nextElementSibling.value) === 1) {
               document.querySelector('.basket-body').removeChild(e.target.parentElement.parentElement)
               return;
           }
           e.target.nextElementSibling.value = parseInt(e.target.nextElementSibling.value) - 1;

        }

        menuItems.forEach(menuItemDiv => {
            menuItemDiv.addEventListener('click', e => pushToBasketArr(e));
        })
        function pushToBasketArr (e) {
            const obj = {};
            const found = basketArr.findIndex(x => e.target.dataset['menu-id'] === x['menuId']);
            if (found===-1) {
                // if not found
                obj['menuName'] = e.target.dataset['menu-name'];
                obj['menuId'] = e.target.dataset['menu-id'];
                obj['menuPrice'] = e.target.dataset['menu-price'];
                obj['menuQuantity'] = 1;
                basketArr.push(obj);
            }
            else {
                //if found
                basketArr[found]['menuQuantity'] += 1;
            }

            updateCart(basketArr);

            const qtyPlus = document.querySelectorAll('.qtyPlus');
            const qtyMinus = document.querySelectorAll('.qtyMinus');
            qtyPlus.forEach (x => {
                x.addEventListener('click', e =>handleQtyPlusBtn(e));
            })
            qtyMinus.forEach (x => {
                x.addEventListener('click', e =>handleQtyMinusBtn(e));
            })

        }
        function updateCart(basketArr) {
            const basketBody = document.querySelector('.basket-body');
    
            basketBody.innerHTML = null;
            basketArr.forEach(item => {
                const basketBodyItem = document.createElement('div');
                basketBodyItem.classList.add('basket-body-item');
                basketBodyItem.innerHTML = `
                    <span>${item.menuName}</span>
                    <span>${item.menuPrice}</span>
                    <span>
                     <div class="qtyIcon qtyMinus">-</div>                        
                        <input value="${item.menuQuantity}">
                     <div class="qtyIcon qtyPlus">+</div>                        
                    </span>
                `;
                basketBody.appendChild(basketBodyItem);

            });
        }
        function handleClickBottomBarText () {
            addToBasketSheet.classList.add('add-to-basket-sheet-animated');
        }
        function handleCloseAddToBasketSheetBtn () {
            addToBasketSheet.classList.remove('add-to-basket-sheet-animated');

        }
        
    })();  
</script>
@endsection