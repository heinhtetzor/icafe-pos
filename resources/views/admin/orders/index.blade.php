@extends('layouts.admin')

@section('css')
<style>
    .header {
        display: flex;
        justify-content: space-between;
    }
    .flex {
        display: flex;
        flex-wrap: wrap; 
    }
    .flex a {
        height: 200px;
        width: 300px;
        border: 1px solid #ececec;
    }

</style>
@endsection

@section('content')
<div class="container">
    <h3 class="header">
        <a href="{{route('admin.home')}}">🔙 </a>
        <span class="title">{{date("d-m-Y")}}</span>
        
    </h3>
    <div class="flex">
        <a href="{{route('orders.today')}}" style="background-size:contain;background-repeat:no-repeat;background-image: url('https://cdn4.iconfinder.com/data/icons/small-n-flat/24/calendar-512.png')">
            Today
        </a>
        <a href="#">
            Reports
        </a>
    </div>
</div>
@endsection

