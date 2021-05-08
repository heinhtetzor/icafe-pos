@extends('layouts.admin')
@section('css') 
<style>
    .flex {
        display: flex;
        flex-wrap: wrap;
        
    }
    .flex > * {
        width: 300px;
        height: 200px;
        margin-right: 1rem;        
        margin-bottom: 1rem;
    }
    @media(max-width:700px) {
        .flex > * {
            width: 100%;
        }
    }
    .flex a {
        text-decoration: none;
        color: #fff;
    }
    .card {
        height: 200px;
    }
</style>
@endsection
@section('content')
<div class="container">
    @if (session('msg'))
    <div class="alert alert-info">
        {{ session('msg') }}
    </div>
    @endif
    <div class="flex">
        <a href="{{route('admin.tables')}}">
            <div class="card bg-dark text-white">
                <div class="card-header">
                    <h2 class="card-title">အရောင်း</h2>
                </div>
                <div class="card-body">
                     
                </div>
            </div>
        </a>
        <a href="{{route('express.home')}}">
            <div class="card bg-dark text-white">
                <div class="card-header">
                    <h2 class="card-title"><i>Express</i></h2>
                </div>
                <div class="card-body">
                     
                </div>
            </div>
        </a>
        <a href="{{route('expenses.create')}}">
            <div class="card bg-dark text-white">
                <div class="card-header">
                    <h2 class="card-title"><i>အသုံးစာရင်း</i></h2>
                </div>
                <div class="card-body">
                     
                </div>
            </div>
        </a>
    </div>

    <hr>

    <div class="flex">
        
        <a href="{{route('admin.reports')}}">
            <div class="card bg-success text-white">
                <div class="card-header">
                    <h2 class="card-title">စာရင်းများ</h2>
                </div>
                <div class="card-body">
                    နေ့စဉ်စာရင်း ၊​ အသေးစိတ်စာရင်းများ ကြည့်ရန်
                </div>
             
            </div>
        </a>
    
    
        <a href="{{route('admin.masterdatamanagement')}}">
            <div class="card bg-danger text-white">
                <div class="card-header">
                    <h2 class="card-title">Master Data</h2>
                </div>
                <div class="card-body">
                    Table များ ၊ Menu များ ထည့်သွင်းရန်
                </div>                
            </div>
        </a>

        <a href="{{route('admin.accountmanagement')}}">
            <div class="card bg-warning text-white">
                <div class="card-header">
                    <h2 class="card-title">Account များ</h2>
                </div>
                <div class="card-body">
                    Waiter အကောင့် ၊​ Admin အကောင့် ၊​ Kitchen အကောင့်ပြုလုပ်ရန်
                </div>                
            </div>              
        </a>
        
    </div>

    <hr>
    
    <a href="/upgrade" class="btn btn-dark" onclick="console.log(this.children[0].style.display='inline-block')">
        <span style="display: none" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
        <span>Upgrade</span>
    </a>

    
</div>
@endsection