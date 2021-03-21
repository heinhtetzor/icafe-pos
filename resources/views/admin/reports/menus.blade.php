@extends('layouts.admin')
@section('css')
<!-- Include base CSS (optional) -->
{{-- <link rel="stylesheet" href="/choices/styles/base.min.css" /> --}}
<!-- Include Choices CSS -->
<link rel="stylesheet" href="/choices/styles/choices.min.css" />
<style>
    /* overwrites choices css */
    .choices {
        display: inline-flex;
        margin-bottom: 0;
        min-width: 200px;
        width: 100%;
    }
</style>
@endsection
@section('content')
<div class="container">
    <div>
        <h3 style="display: inline">
            <a href="{{route('admin.reports')}}">🔙 </a>
            Menu / Menu အုပ်စုအလိုက် စာရင်း
        </h3>
        

        <div>
            <form action="{{route('admin.reports.menus')}}" method="GET">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            {{-- menugroups select --}}
                            <select multiple name="menuGroup[]" id="menuGroup" class="form-control">
                                <option value="">Menu အုပ်စုရွေးပါ</option>
                                @foreach($menuGroups as $menuGroup)
                                <option value="{{$menuGroup->id}}">{{$menuGroup->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <select multiple name="menu[]" id="menu" class="form-control">
                                <option value="">Menu ရွေးပါ</option>
                                @foreach($menus as $menu)
                                <option value="{{$menu->id}}">{{$menu->name}}</option>
                                @endforeach
                            </select>                     
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <input class="form-control" required type="date" id="datePicker" placeholder="Choose Date" name="date">                     
                        </div>                
                    </div>
                </div>                
                <button style="float:right" class="btn btn-dark"><i class="bi bi-search"></i> Search</button>
            </form>
        </div>
    </div>
    <br>
    @if(count($results) > 0)
    <div class="alert alert-primary">
        <span>
            {{$fromTime->format('d-M-Y')}} မှ​ {{$toTime->format('d-M-Y')}} ထိ
            @if(count($filtered_menu_groups) > 0)
                @foreach($filtered_menu_groups as $mg)
                <div class="badge bg-primary">{{$mg->name}}</div>
                @endforeach
            @endif
            @if(count($filtered_menus) > 0)
                @foreach($filtered_menus as $m)
                    <div class="badge bg-success">{{$m->name}}</div>
                @endforeach
            @endif
            <span class="badge rounded-pill bg-dark" style="float: right;">{{$total}} - ကျပ်</span>            
        </span>
    </div>
    <table class="table table-hover">
        <thead class="bg-success text-white">
            <th>Menu အမည်</th>
            <th>နှုန်း</th>
            <th style="text-align: center">အရေအတွက်</th>
            <th style="text-align: center">ကျသင့်ငွေ</th>
        </thead>
        <tbody>
            @foreach ($results as $result)
            <tr>
                <td>{{$result->menu->name}}</td>
                <td>{{$result->price}}</td> 
                <td style="text-align: center">{{$result->total}}</td> 
                <td style="text-align: center">{{$result->total * $result->price}}</td> 
            </tr>        
            @endforeach
        </tbody>
    </table>    
    @endif    
</div>
@endsection
@section('js')

<script src="/litepicker/litepicker.js"></script>

<!-- Include Choices JavaScript -->
<script src="/choices/scripts/choices.min.js"></script>
<script>
(()=> {     
    const picker = new Litepicker({
        element: document.querySelector('#datePicker'),
        singleMode: false,        
    });
    const menuGroupSelect = document.querySelector('#menuGroup');
    const menuSelect = document.querySelector('#menu');
    const menuGroupChoices = new Choices(menuGroupSelect);
    const menuChoices = new Choices(menuSelect);
})()
</script>
@endsection
