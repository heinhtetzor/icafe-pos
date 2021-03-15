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
    }
    .tables-grid-item-occupied {
        filter: blur(3px);
    }

    .tables-grid-item  span {
        font-family: Arial, Helvetica, sans-serif;
        font-weight: 900;
        /* position: absolute;
        top: 40%; */
        font-size: 4rem;
        color: black;        
        text-shadow: 4px 4px 5px rgb(228, 4, 4);
    }
</style>
@endsection 
@section('content')
<div class="container-fluid mt-5">    
    <div class="row">
        {{-- tables --}}
        <div class="col-md-12">
            <h3>Table ရွေးပါ</h3>            
            
            <div class="tables-grid">
                @foreach($tables as $table)                    
                <a href="{{route('waiter.pos', $table->id)}}" class="tables-grid-item @if(!$table->table_status->isTableFree()) tables-grid-item-occupied @endif">                                  
                    <span>{{$table->name}}</span>
                </a>
                @endforeach                
            </div>
        </div>
    </div>
</div>
@endsection