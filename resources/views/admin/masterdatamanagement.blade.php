@extends('layouts.admin')
@section('css')
<style>
    .flex {
    display: flex;
    flex-wrap: wrap;    
    }
    .flex > * {
        width: 300px;
        margin-right: 1rem;
    }
</style>
@endsection
@section('content')
<div class="container">
    <h3>
        <a href="{{route('admin.home')}}">🔙 </a>
        Master Data Management
    </h3>
    <div class="flex">
        <div class="card bg-success text-white">
            <div class="card-header">
                <h4 class="card-title">Menu Groups</h4>
            </div>
            <div class="card-body">
                {{-- Manage Master Data such as Menus, MenuGroups and Tables. --}}
            </div>
            <div class="card-footer">
                <a class="btn btn-warning" href="{{route('menugroups.index')}}">Go</a>
            </div> 
        </div>
        <div class="card bg-primary text-white">
            <div class="card-header">
                <h4 class="card-title">Menus</h4>
            </div>
            <div class="card-body">
                {{-- Waiter accounts and Admin Account Management --}}
            </div>
            <div class="card-footer">
                <a class="btn btn-warning" href="{{route('menus.index')}}">Go</a>
            </div>
        </div>

    </div>
    <hr>
    <div class="flex">
        <div class="card bg-warning text-dark">
            <div class="card-header">
                <h4 class="card-title">Tables</h4>
            </div>
            <div class="card-body">
                {{-- Table Management --}}
            </div>
            <div class="card-footer">
                <a class="btn btn-warning" href="{{route('tables.index')}}">Go</a>
            </div>
        </div>
    </div>
</div>
@endsection