@extends('layouts.client')
@section('style')
<style>
    .tables-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
        grid-gap: 1rem; 
    }
    .tables-grid-item {      
        text-decoration: none;
        display: block;  
        height: 100px;
        width: 100px;
        position: relative;
        text-align: center;        
        position: relative;
        background-image: url('/assets/table-icon.png');
        background-size: 100px;
        background-blend-mode: overlay;
        background-repeat: no-repeat;
        cursor: pointer;
        border: 0.8px solid #bcbcbc;
    }
    .tables-grid-item-occupied {
        filter: blur(3px);
    }

    .tables-grid-item  span {
        font-family: Arial, Helvetica, sans-serif;
        font-weight: 900;
        /* position: absolute;
        top: 40%; */
        font-size: 2rem;
        color: black;        
        text-shadow: 4px 4px 5px rgb(144, 172, 250);
    }
    .table-group {
        border: 1px solid #bcbcbc;
        margin-bottom: 1rem;
    }
    .table-group-name {
        font-size: 0.8rem;
    }
</style>
@endsection 
@section('content')
<div class="container" style="margin-top: 4rem">    
    <div class="row">
        {{-- tables --}}
        <div class="col-md-12">

            @if(Auth::guard('admin_account')->check())
            <h3>
            <a href="{{route('admin.home')}}">🔙</a>
            Table ရွေးပါ         
            </h3>   
            @endif

            @if(Auth::guard('waiter')->check())
            <h3>
            Table ရွေးပါ         
            </h3> 
            @endif
            
            @foreach($table_groups as $table_group)
                <fieldset class="table-group">
                    <legend class="table-group-name">{{ $table_group->name }}</legend>
                    <div class="tables-grid">
                        @foreach($table_group->tables as $table)        
                        @if(Auth::guard('admin_account')->check())
                        <a href="{{route('admin.pos', $table->id)}}" class="tables-grid-item @if(!$table->table_status->isTableFree()) tables-grid-item-occupied @endif">
                        @else 
                        <a href="{{route('waiter.pos', $table->id)}}" class="tables-grid-item @if(!$table->table_status->isTableFree()) tables-grid-item-occupied @endif">                                  
        
                        @endif         
                            <span>{{$table->name}}</span>
                        </a>
                        @endforeach                
                    </div>
                </fieldset>
            @endforeach
        </div>
    </div>
</div>
@endsection