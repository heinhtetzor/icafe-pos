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
    <hr>
    <div class="row">
        <div class="col-md-4">
            <a href="{{route('admin.reports.expenses')}}">
                <div class="card bg-primary text-white">
                    <div class="card-header">
                        နေ့စဉ်
                    </div>
                    <div class="card-body">
                        နေ့အလိုက် အဝယ်စာရင်း
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <a href="{{route('admin.reports.items')}}">
                <div class="card bg-info text-white">
                    <div class="card-header">
                        အဝယ် ပစ္စည်းများ
                    </div>
                    <div class="card-body">
                        ..
                    </div>
                </div>
            </a>
        </div>        
        <div class="col-md-4">
            <a href="{{route('admin.reports.stock-menus')}}">
                <div class="card bg-info text-white">
                    <div class="card-header">
                        အဝယ် ပစ္စည်းများ (Stock)
                    </div>
                    <div class="card-body">
                        ..
                    </div>
                </div>
            </a>
        </div>
    </div>
    <hr>
    <div class="row">
        <div class="col-md-4">
            <a href="{{route('admin.reports.profit-loss')}}">
                <div class="card bg-dark text-white">
                    <div class="card-header">
                        အရှုံးအမြတ်စာရင်း
                    </div>
                    <div class="card-body">
                        လအလိုက်ကြည့်ရန်
                    </div>
                </div>
            </a>
        </div>
    </div>

    
</div>
@endsection