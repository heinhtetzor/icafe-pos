@extends('layouts.client')
@section('style')
<style>
    body {
        background-color: {{Auth()->guard('kitchen')->user()->color}};
    }
    .horizontal-container {
    	white-space: nowrap;
    }
    .card {
    	display: inline-block;
    	min-width: 300px;

    	min-height: 85vh;
    	max-height: 85vh;

    	overflow-y: scroll;
    }    
    #item {
    	transition: all 200ms ease-in-out;    	
    	cursor: pointer;
    }
    #item:hover {
    	transform: scale(1.05);    	
    }
    .tick-btn {
    	float: right;
    }

	.order-item {
		border: 1px solid #bcbcbc;
		padding: 1rem;
		user-select: none;
	}

	.order-item-disabled {
		color: #b3b3b3;
		cursor:not-allowed;
		user-select: none;
	}
</style>
@endsection 
@section('content')
<div class="container-fluid mt-5">	
	<!-- Modal -->
	<div class="modal" id="settingModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
		<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
			<h5 class="modal-title" id="exampleModalLabel">Adjust Size</h5>
			<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<form action="{{ route('kitchen.adjustPanelSize') }}" method="POST">
				@csrf
				<div class="modal-body">
					<input type="hidden" value="{{Auth()->guard('kitchen')->user()->id}}" name="id">	
					<div class="form-group">
						<label for="panel_size">Panel Size</label>
						<input class="form-range" type="range" name="panel_size" min="300" max="1000" step="50" value="{{Auth()->guard('kitchen')->user()->panel_size}}">				
					</div>
					<div class="form-group">
						<label for="font_size">Font Size</label>
						<input class="form-range" type="range" name="font_size" min="16" max="50" step="5" value="{{Auth()->guard('kitchen')->user()->font_size}}">
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
					<button type="submit" class="btn btn-primary">Save changes</button>
				</div>
			</form>
		</div>
		</div>
	</div>


    <h2 id="name">{{ Auth()->guard('kitchen')->user()->name }}
		<button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#settingModal">
			Setting
		</button>
	</h2>	
    <input type="hidden" id="id" value="{{Auth()->guard('kitchen')->user()->id}}">
    {{-- CSRF token --}}
    <input type="hidden" name="_token" id="_token" value="{{csrf_token()}}">
    <div class="horizontal-container">
	      	
    </div>
</div>
@endsection
@section('script')
<script src="https://cdnjs.cloudflare.com/ajax/libs/socket.io/3.0.4/socket.io.js" integrity="sha512-aMGMvNYu8Ue4G+fHa359jcPb1u+ytAF+P2SCb+PxrjCdO3n3ZTxJ30zuH39rimUggmTwmh2u7wvQsDTHESnmfQ==" crossorigin="anonymous"></script>
<script>
	(()=> {
		const kitchenColor = "{{Auth()->guard('kitchen')->user()->color}}";
		const kitchenAlert = new Audio('/sounds/kitchen-alert.wav');
		
		const name = document.querySelector('#name');
		name.style.color = getContrastYIQ(kitchenColor);

		// https://stackoverflow.com/a/11868398/11156865
		function getContrastYIQ(hexcolor){
			hexcolor = hexcolor.replace("#", "");
			var r = parseInt(hexcolor.substr(0,2),16);
			var g = parseInt(hexcolor.substr(2,2),16);
			var b = parseInt(hexcolor.substr(4,2),16);
			var yiq = ((r*299)+(g*587)+(b*114))/1000;
			return (yiq >= 128) ? 'black' : 'white';
		}

	    const socket = io('{{config('app.socket_url')}}');
	    
	    const id=document.querySelector('#id').value;
	    const token=document.querySelector('#_token').value;



	    socket.emit('join-room', {
	    	roomId: 1
	    });

	    socket.on('deliver-order', data=> {
	    	console.log(data);
	    	// location.reload()
	    	fetchOrderMenus();

			kitchenAlert.play();
	    })

	   
	    fetchOrderMenus();

	    //functions declarations
	    //tick each  -serve to customer
	    function onTickHandler(e) {
    		const orderMenuId=e.target.dataset.id;
    		//fetch call to change status
    		fetch(`/api/serveToCustomer/${orderMenuId}`, {
		        headers: {
		            "Content-Type": "application/json",
		            "Accept": "application/json",
		            "X-Requested-With": "XMLHttpRequest",
		            "X-CSRF-Token": token
		        },
		        credentials: "same-origin",
		        method: 'GET'
		    })
		    .then(res=> {
		    	fetchOrderMenus();

		    	//send serve-to-customer to waiter 
		    	socket.emit('serve-to-customer', res);
		    })
		    .catch(err=> console.log(err));
	    }   

	    //tick all at once -serve to customer
	    function onTickAllHandler(e) {	
	    	const menuGroupId=e.target.dataset.id;
	    	//fetch call to change status of All
	    	fetch(`/api/serveAllToCustomer/${menuGroupId}`, {
	    		headers: {
		            "Content-Type": "application/json",
		            "Accept": "application/json",
		            "X-Requested-With": "XMLHttpRequest",
		            "X-CSRF-Token": token
		        },
		        credentials: "same-origin",
		        method: 'GET'
	    	})
	    	.then(res=> {
	    		fetchOrderMenus();

	    		//send serve-to-customer to waiter 
		    	socket.emit('serve-to-customer', res);
	    	})
	    	.catch(err=> console.log(err));
	    }

	    function fetchOrderMenus() {

	    	console.warn("====fetching=====");	    
		    // fetch related menu groups with orders
	        fetch(`/api/kitchen/${id}/orders`, {
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
		    	const container=document.querySelector('.horizontal-container');
		    	container.innerHTML="";
		    	res.forEach(x=> {	    			    	
		    		let orderMenus="";
		    		x.orderMenus.forEach(y=> {						
		    			//if havent served to customer
		    			if(y.status===0) {
			    			orderMenus+=`	
			    				<div class="order-item" id="item">
			    					${y.quantity} x ${y.menu}  
			    					<button class="tick-btn btn btn-success" data-id="${y.id}">
			    						✓ 
			    					</button><br>
			    					<span class="badge bg-primary">${y.waiter ? y.waiter : 'admin' }</span>
									<span class="badge bg-success">${y.table}</span>
			    				</div>
			    			`;
		    			}
		    			// if already served ( dull color )
		    			else {
			    			orderMenus+=`
			    				<div class="order-item-disabled" style="" class="alert alert-secondary" id="item">
			    					${y.quantity} x ${y.menu}  			    					
			    				</div>
			    			`;
		    			}
		    		});	    						
		    		container.innerHTML+=`
		    			<div class="card" 
						style="width: {{Auth()->guard('kitchen')->user()->panel_size}}; font-size: {{Auth()->guard('kitchen')->user()->font_size}};">
		    				<div class="card-header" style="height: auto;">
		    					<span>${x.menuGroup.name}</span>		    					
	    						<button  style='float:right' class='tick-all-btn btn btn-success btn-sm' data-id="${x.menuGroup.id}">
	    							✓✓✓
	    						</button>		    					
		    				</div>
		    				<div class="card-body">	
		   						${orderMenus}
		    				</div>	    				
		    			</div>
		    			`
		    	})
		    	const tickBtns=document.querySelectorAll('.tick-btn');
		    	const tickAllBtns=document.querySelectorAll('.tick-all-btn');
			    
			    tickBtns.forEach(tickBtn=> {
			    	tickBtn.addEventListener('click', onTickHandler);
			    })

			    tickAllBtns.forEach(tickAllBtn=> {
			    	tickAllBtn.addEventListener('click', onTickAllHandler);
			    })
		    })
		    .catch(err=> console.error(err))
	    }

	})();
	
</script>
@endsection