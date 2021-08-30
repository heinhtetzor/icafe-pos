@extends('layouts.admin')
@section('css')
	<style>
		body {
			background-color: #d2d8d8;
    	}
	</style>
@endsection
@section('content')
<div class="container" id="app">
<!-- 	<h3>Stock Menu</h3>	
	@{{ message }}
	@foreach ($stock_menus as $stock_menu)
		{{$stock_menu->menu->name}}
	@endforeach -->
	Coming Soon
</div>
@endsection
@section('js')
<script src="https://cdn.jsdelivr.net/npm/vue@2/dist/vue.js"></script>
<script>
//get stock items from api
var app = new Vue({
  el: '#app',
  data: {
    message: 'Hello Vue!'
  }
})
</script>
@endsection