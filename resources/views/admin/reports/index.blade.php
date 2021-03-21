@extends('layouts.admin')
@section('css')
<style>
    a {
        text-decoration: none;
    }
</style>
@endsection
@section('content')
<div class="container">
    <h3>
        <a href="{{route('admin.home')}}">🔙 </a>


    </h3>
    <div class="row">
        <div class="col-md-4">
            <a href="{{route('admin.reports.day')}}">
                <div class="card bg-success text-white">
                    <div class="card-header">
                        နေ့စဉ်
                    </div>
                    <div class="card-body">
                        နေ့အလိုက် အရောင်းစာရင်း
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <a href="{{route('admin.reports.menus')}}">
                <div class="card bg-danger text-white">
                    <div class="card-header">
                        Menus
                    </div>
                    <div class="card-body">
                        ..
                    </div>
                </div>
            </a>
        </div>
    </div>
</div>
@endsection