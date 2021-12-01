@extends('layouts.admin')
@section('head')
<script defer src="/js/alpine.js"></script>
@endsection
@section('css')
<style>
	.menu-grid {
		display: grid;
        grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
	}
	.menu-container {
		cursor: pointer;

	}
	.menu-image {
		width: 100%;
		height: 100%;
		object-fit: contain;
	}
	.menu-stock-label {

	}
</style>
@endsection
@section('content')
<div id="app" 
x-data="alpineInstance()"
x-init="getMenus">
	<div class="menu-grid">
		<template x-for="menu in menus">
			<div class="menu-container">
				<span class="menu-name" x-text="menu.name"></span>
				<span class="menu-price" x-text="menu.price"></span>
				<img class="menu-image" 
				onerror="this.src='/images/default.png'"
				:src="menu.image 
				? '/storage/menu_images/'+menu.image
				: '/images/default.png'
				"
				/>
			</div>
		</template>	
	</div>
</div>
@endsection
@section('js')
<script>
	const alpineInstance = () => {
		return {
			menus: [],
			menu_groups: [],

			async getMenus () {
				const response = await fetch('/api/menus');
				const resJson = await response.json();
				this.menus = [...resJson.data];
			}
		}
	};

</script>
@endsection