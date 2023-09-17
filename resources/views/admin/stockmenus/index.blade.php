@extends('layouts.admin')
@section('css')
<link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" type="text/css">

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
	</style>
@endsection
@section('content')
<div class="container" id="app">
	<h4><a href="{{route('admin.home')}}">🔙 </a> ရောင်းကုန် ပစ္စည်းများ
		<a class="btn btn-success" href="{{route('expenses.create')}}">🟢 စာရင်းသွင်းရန်</a>		
	</h4>
	<br>

	<table id="stock-menus-table" class="table table-striped table-bordered table-sm">
		<thead>
			<tr>
				<th>Code</th>
				<th>အမျိုးအမည်</th>
				<th>လက်ကျန်</th>
				<!-- <th>တန်ဖိုး</th> -->
			</tr>
		</thead>
		<tbody>
			@foreach ($stock_menus as $stock_menu)
			<tr>
				<td>
					{{$stock_menu->menu->code}}
				</td>
				<td>
					<a href="{{ route('stockmenus.show', $stock_menu->id) }}">
							{{ $stock_menu->menu->name }}
					</a>
				</td>
				<td>
					{{ $stock_menu->balance }}			
				</td>
				<!-- <td>
					
				</td> -->
			</tr>
			@endforeach
		</tbody>
	</table>

</div>
@endsection
@section('js')
<script src="https://cdn.jsdelivr.net/npm/simple-datatables@latest" type="text/javascript"></script>
<script>
	const dataTable = new simpleDatatables.DataTable("#stock-menus-table", {
		searchable: true,
		fixedHeight: false,
		sortable: true,
		perPage: 50,
		perPageSelect: [10, 20, 50, 100, 200]
	})

</script>
@endsection