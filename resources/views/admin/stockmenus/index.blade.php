@extends('layouts.admin')
@section('css')
	<style>
		body {
			background-color: #ffffff;
    	}
		.grid-stock-menu {
			display: grid;			
			grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
			grid-template-rows: repeat(auto-fill, minmax(70px, 1fr));			
			grid-gap: 1rem;
		}
		.grid-stock-menu-item {
			border: 1px solid black;
			text-align: center;
			padding-top: 12px;	
			cursor: pointer;			
			text-decoration: none;
		}
		.grid-stock-menu-item-name {
			user-select: none;
			white-space: nowrap;
			overflow: hidden;
			text-overflow: ellipsis;
		}
		.grid-stock-menu-item-badge {

		}
	</style>
@endsection
@section('content')
<div class="container" id="app">
	<h4><a href="{{route('admin.home')}}">🔙 </a> ရောင်းကုန် ပစ္စည်းများ
		<a style="float:right" class="btn btn-secondary" href="">🔄<a>	
		<a class="btn btn-success" href="{{route('expenses.create')}}">🟢 စာရင်းသွင်းရန်</a>		
	</h4>
	<br>

	<div class="grid-stock-menu">
		@foreach ($stock_menus as $stock_menu)
		<a href="{{ route('stockmenus.show', $stock_menu->id) }}" class="grid-stock-menu-item">
			<div class="grid-stock-menu-item-name">
				{{$stock_menu->menu->name}} 
			</div>
			{{ $stock_menu->balance }}
		</a>
		@endforeach	
	</div>
</div>
@endsection
@section('js')

@endsection