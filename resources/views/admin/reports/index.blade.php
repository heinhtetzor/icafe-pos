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
        <a href="{{route('admin.home')}}">π </a>


    </h3>
    <div class="row">
        <div class="col-md-4">
            <a href="{{route('admin.reports.day')}}">
                <div class="card round-card bg-success text-white">
                    <div class="card-header">
                        αα±α·αααΊ
                    </div>
                    <div class="card-body">
                        αα±α·α‘αα­α―ααΊ α‘αα±α¬ααΊαΈαα¬αααΊαΈ
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <a href="{{route('admin.reports.menus')}}">
                <div class="card round-card bg-danger text-white">
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
                <div class="card round-card bg-primary text-white">
                    <div class="card-header">
                        αα±α·αααΊ
                    </div>
                    <div class="card-body">
                        αα±α·α‘αα­α―ααΊ α‘αααΊαα¬αααΊαΈ
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <a href="{{route('admin.reports.items')}}">
                <div class="card round-card bg-info text-white">
                    <div class="card-header">
                        α‘αααΊ αααΉαααΊαΈαα»α¬αΈ
                    </div>
                    <div class="card-body">
                        ..
                    </div>
                </div>
            </a>
        </div>        
        <div class="col-md-4">
            <a href="{{route('admin.reports.stock-menus')}}">
                <div class="card round-card bg-info text-white">
                    <div class="card-header">
                        α‘αααΊ αααΉαααΊαΈαα»α¬αΈ (Stock)
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
                <div class="card round-card bg-dark text-white">
                    <div class="card-header">
                        α‘ααΎα―αΆαΈα‘ααΌααΊαα¬αααΊαΈ
                    </div>
                    <div class="card-body">
                        αα‘αα­α―ααΊααΌααΊα·αααΊ
                    </div>
                </div>
            </a>
        </div>
    </div>

    
</div>
@endsection