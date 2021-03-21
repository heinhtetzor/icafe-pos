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
    <div class="row">
        <div class="col-md-3">
            <a href="{{route('menugroups.index')}}">
                <div class="card bg-primary text-white">
                    <div class="card-header">
                        <h4 class="card-title">Menus</h4>
                    </div>
                    <div class="card-body">
                        {{-- Waiter accounts and Admin Account Management --}}
                    </div>                
                </div>
            </a>
        </div>
        <div class="col-md-3">
            <a href="{{route('tables.index')}}">
                <div class="card bg-warning text-dark">
                    <div class="card-header">
                        <h4 class="card-title">Tables</h4>
                    </div>
                    <div class="card-body">
                        {{-- Table Management --}}
                    </div>                
                </div>
            </a>

        </div>
    </div>
</div>
@endsection