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
</style>
@endsection 
@section('content')
<div class="container-fluid mt-5">
    <h2>{{ Auth()->guard('kitchen')->user()->name }}</h2>
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
					console.log(x)
		    		let orderMenus="";
		    		x.orderMenus.forEach(y=> {						
		    			//if havent served to customer
		    			if(y.status===0) {
			    			orderMenus+=`	
			    				<div style="border:1px solid #bcbcbc;padding:1rem;" id="item">
			    					${y.quantity} x ${y.menu.name}  
			    					<button class="tick-btn btn btn-success" data-id="${y.id}">
			    						✓ 
			    					</button><br>
			    					<span class="badge bg-primary">${y.waiter ? y.waiter.name : 'admin' }</span>
			    				</div>
			    			`;
		    			}
		    			// if already served ( dull color )
		    			else {
			    			orderMenus+=`
			    				<div style="color: #b3b3b3;cursor:not-allowed;" class="alert alert-secondary" id="item">
			    					${y.quantity} x ${y.menu.name}  			    					
			    				</div>
			    			`;
		    			}
		    		});	    	
		    		container.innerHTML+=`
		    			<div class="card">
		    				<div class="card-header" style="height: auto;">
		    					<span>${x.menuGroup.name}</span>		    					
	    						<button  style='float:right' class='tick-all-btn btn btn-success' data-id="${x.menuGroup.id}">
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