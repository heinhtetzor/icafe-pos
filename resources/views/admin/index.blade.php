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
    /* temp for thandingyut */
    .img-cover {
        /* position: absolute;
        height: 1000px;
        width: 98vw;
        background-image: url("/images/thadingyut/tdg-3.avif");
        background-size: cover;
        filter: contrast(100%) opacity(70%); */
    }
    @media (max-width: 900px) {
        .img-cover {
            display: none;
        }
    }
</style>
@endsection
@section('content')
<div class="img-cover"></div>
<div class="container">
    @if (session('msg'))
    <div class="alert alert-info">
        {{ session('msg') }}
    </div>
    @endif
    <div class="flex">
        <a href="{{route('admin.tables')}}">
            <div class="card round-card bg-dark text-white">
                <div class="card-header">
                    <h2 class="card-title">အရောင်း</h2>
                </div>
                <div class="card-body">
                    <p class="icon" style="font-size: 4rem;">☕️</p>
                </div>
            </div>
        </a>
        <a href="{{route('express.home')}}">
            <div class="card round-card bg-dark text-white">
                <div class="card-header">
                    <h2 class="card-title"><i>Express</i></h2>
                </div>
                <div class="card-body">
                    <p class="icon" style="font-size: 4rem;">🖥</p>
                </div>
            </div>
        </a>
        
    </div>
    <br>

    <div class="flex">
        <a href="{{route('expenses.create')}}">
            <div class="card round-card bg-dark text-white">
                <div class="card-header">
                    <h2 class="card-title"><i>အသုံးစာရင်း</i></h2>
                </div>
                <div class="card-body">
                    <p class="icon" style="font-size: 4rem;">🛒</p>
                </div>
            </div>
        </a>
        {{-- stock menus --}}
        <a href="{{route('stockmenus.index')}}">
            <div class="card round-card bg-info text-white">
                <div class="card-header">
                    <h2 class="card-title"><i>အဝယ်စာရင်း Stock</i></h2>
                </div>
                <div class="card-body">
                    <p class="icon" style="font-size: 4rem;">🛒</p>
                </div>
            </div>
        </a>
    </div>

    <br>

    <div class="flex">
        
        <a href="{{route('admin.reports')}}">
            <div class="card round-card bg-success text-white">
                <div class="card-header">
                    <h2 class="card-title">စာရင်းများ</h2>
                </div>
                <div class="card-body">
                    နေ့စဉ်စာရင်း ၊​ အသေးစိတ်စာရင်းများ ကြည့်ရန်
                </div>
             
            </div>
        </a>
    
    
        <a href="{{route('admin.masterdatamanagement')}}">
            <div class="card round-card bg-danger text-white">
                <div class="card-header">
                    <h2 class="card-title">Master Data</h2>
                </div>
                <div class="card-body">
                    Table များ ၊ Menu များ ထည့်သွင်းရန်
                </div>                
            </div>
        </a>

        <a href="{{route('admin.accountmanagement')}}">
            <div class="card round-card bg-warning text-white">
                <div class="card-header">
                    <h2 class="card-title">Account များ</h2>
                </div>
                <div class="card-body">
                    Waiter အကောင့် ၊​ Admin အကောင့် ၊​ Kitchen အကောင့်ပြုလုပ်ရန်
                </div>                
            </div>              
        </a>
        
    </div>

    <br>

    <div class="flex">
        
        <a href="{{route('settings.index')}}">        
            <div class="card round-card bg-primary text-white">
                <div class="card-header">
                    <h2 class="card-title">Setting</h2>
                </div>
                <div class="card-body">
                  <p class="icon" style="font-size: 4rem;">⚙️</p>
                </div>
             
            </div>
        </a>
    
    
        
    </div>

    
</div>
@endsection
@section('js')
<script>
    fetch (`/admin/settings/getAll`)
    .then (res => res.json())
    .then (res => {
        res.settings.forEach (setting => {
            localStorage.setItem(setting.key, setting.value);
        })
        document.querySelector('.topnav-text').innerHTML = localStorage.getItem('shop_name');
    })
    .catch (err => {
        console.log(err);
    })    
</script>
 
@endsection
