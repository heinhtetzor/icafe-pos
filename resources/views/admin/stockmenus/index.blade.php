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
	<h4><a href="{{route('admin.home')}}">π </a> αα±α¬ααΊαΈαα―ααΊ αααΉαααΊαΈαα»α¬αΈ
		@if (request()->query('sortByBalance') === 'ASC')
		<a style="float:right" class="btn btn-secondary" href="{{route('stockmenus.index', ['sortByBalance'=>'DESC'])}}">βοΈ<a>	
		@else
		<a style="float:right" class="btn btn-secondary" href="{{route('stockmenus.index', ['sortByBalance'=>'ASC'])}}">βοΈ<a>	
		@endif
		<!-- @if (request()->query('sortByAlpha') === 'ASC')
		<a style="float:right" class="btn btn-secondary" href="{{route('stockmenus.index', ['sortByAlpha'=>'DESC'])}}">π <a>
		@else
		<a style="float:right" class="btn btn-secondary" href="{{route('stockmenus.index', ['sortByAlpha'=>'ASC'])}}">π <a>	
		@endif -->
		<a style="float:right" class="btn btn-secondary" href="{{route('stockmenus.index')}}">π<a>	
		<a class="btn btn-success" href="{{route('expenses.create')}}">π’ αα¬αααΊαΈαα½ααΊαΈαααΊ</a>		
	</h4>
	<form method="GET" action="{{route('stockmenus.index')}}">
		<input type="text" class="col-md-3" name="search" placeholder="ααΎα¬αα«" value="{{request()->query('search')}}">
		<button class="btn btn-dark">Search</button>
	</form>
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