@extends('layouts.admin')
@section('css')
	<style>
		body {
			background-color: #ffffff;
    	}
	</style>
@endsection
@section('content')
<div class="container" id="app">
	<h4>
        <a href="{{route('stockmenus.index')}}">🔙 </a>
		{{ $stock_menu->menu->name }}
	</h4>	
	<p class="alert alert-success">လက်ရှိ အရေအတွက် - {{ $stock_menu->balance }}</p>	

	<h4></h4>
	<table class="table table-striped">
		<thead>
			<tr>
				<th>အချိန်</th>
				<th>ဝယ်စျေး / ရောင်းစျေး</th>
				<th>ဝင် / ထွက်</th>
				<th>Ref</th>
			</tr>
		</thead>
		<tbody>
		@foreach ($stock_menu_entries as $entry)
			<tr>
				<td>{{ $entry->created_at->format('d-M-Y h:i A') }}</td>
				<td>{{ $entry->cost }}</td>
				<td>
					@if ($entry->in > 0)
					<span class="badge bg-success">{{ $entry->in }}</span>					
					@elseif ($entry->out > 0)
					<span class="badge bg-danger">{{ $entry->out }}</span>						
					@endif
				</td>
				<td>					
					@if ($entry->expenseStockMenu)
					<a href="{{ route('expenses.edit', $entry->expenseStockMenu->expense->id) }}">အဝယ်</a>
					@elseif ($entry->orderMenu)
					<a href="{{route('orders.show', $entry->orderMenu->order_id)}}">အရောင်း</a>
					@else
					- 
					@endif
				</td>
			</tr>
		@endforeach
		</tbody>
	</table>
		{{$stock_menu_entries->appends($_GET)->links()}}
</div>
@endsection
@section('js')

@endsection