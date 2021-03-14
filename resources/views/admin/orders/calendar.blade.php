@extends('layouts.admin')

@section('css')
<style>
    body {
        background-color: rgb(233, 232, 232);
    }
    .form {
    	padding: 2rem;
    	background-color: rgb(255,255,255); 
    	border-radius: 8px;
    }
    
</style>
@endsection

@section('content')
	<div class="container-fluid">
		<h3><a href="{{route('orders.index')}}">🔙 </a>   Calendar </h3>

		<div class="row">
			<div class="col-md-3">
				<div class="form">
					<div class="form-group">
						<form method="GET" action="/admin/orders/calendar">
							<input id="datePicker" type="date" name="date">
							<br><br>
							<button class="btn btn-primary">Search</button>	
						</form>
					</div>
				</div>				
			</div>
		</div>

	</div>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/litepicker/dist/litepicker.js"></script>
<script type="text/javascript">	  	
	(()=> {		
	  	const picker = new Litepicker({
	    	element: document.querySelector('#datePicker'),
	    	singleMode: false
	  	});
	})()
</script>
@endsection