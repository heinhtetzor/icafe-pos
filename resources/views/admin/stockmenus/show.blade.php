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
        <a href="javascript:history.back()">🔙</a>
		{{ $stock_menu->menu->name }}
		<!-- <a href="{{ route('menus.edit', $stock_menu->menu->id) }}">စျေးနှုန်းပြောင်းရန်</a> -->
		<div style="float: right;">
			<a class="{{ !request()->query('type') ? 'btn btn-dark' : '' }}" href="{{route('stockmenus.show', ['stockMenu' => $stock_menu->id ])}}">All</a>
			<a class="{{ request()->query('type') === 'out' ? 'btn btn-danger' : '' }}" href="{{route('stockmenus.show', ['stockMenu' => $stock_menu->id, 'type' => 'out'])}}"> အရောင်း</a>
			<a class="{{ request()->query('type') === 'in' ? 'btn btn-success' : '' }}" href="{{route('stockmenus.show', ['stockMenu' => $stock_menu->id, 'type' => 'in'])}}"> အဝယ်</a>
		</div>
	</h4>	
	<p class="alert alert-success">ရောင်းစျေး - <strong><a href="{{ route('menus.edit', $stock_menu->menu->id) }}">{{ $stock_menu->menu->price }} ကျပ်</a> </strong> | အရေအတွက် - <span class="badge bg-primary">{{ $stock_menu->balance }}</span></p>	
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