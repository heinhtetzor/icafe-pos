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
<div class="container-fluid">
    <div>
        <h3 style="display: inline">
            <a href="{{route('admin.reports')}}">🔙 </a>
            Menu / Menu အုပ်စုအလိုက် <div class="badge bg-danger">အရောင်းစာရင်း</div>
            @if (count($results) > 0)

            <a href="#" id="print" class="btn btn-info">🖨 Print</a>
            @endif
        </h3>
        {{-- CSRF token --}}
        <input type="hidden" name="_token" id="_token" value="{{csrf_token()}}">
        

        <div>
            <form action="{{route('admin.reports.menus')}}" method="GET">
                <div class="row">
                    <div class="col-md-2">
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
                    <div class="col-md-2">
                        <div class="form-group">
                            <select multiple name="menu[]" id="menu" class="form-control">
                                <option value="">Menu ရွေးပါ</option>
                                @foreach($menus as $menu)
                                <option value="{{$menu->id}}">{{$menu->name}}</option>
                                @endforeach
                            </select>                     
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <select multiple name="table[]" id="table" class="form-control">
                                <option value="">Table ရွေးပါ</option>
                                <option value="express">Express</option>
                                @foreach($tables as $table)
                                <option value="{{$table->id}}">{{$table->name}}</option>
                                @endforeach
                            </select>                     
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group"> 
                            <select multiple name="waiter[]" id="waiter" class="form-control">
                                <option value="">Waiter ရွေးပါ</option>
                                @foreach($waiters as $waiter)
                                <option value="{{$waiter->id}}">{{$waiter->name}}</option>
                                @endforeach
                            </select>                     
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <input autocomplete="off" class="form-control" required type="text" id="datePicker" placeholder="နေ့စွဲရွေးပါ" name="date">                     
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
            @if(count($filtered_tables) > 0)
                @foreach($filtered_tables as $t)
                    <div class="badge bg-danger">{{$t->name}}</div>
                @endforeach
            @endif
            @if(count($filtered_waiters) > 0)
                @foreach($filtered_waiters as $w)
                    <div class="badge bg-danger">{{$w->name}}</div>
                @endforeach
            @endif
            <span style="float:right;font-weight:bolder">{{$total}} ကျပ်</span>

        </span>
    </div>
    <table class="table table-hover" id="results-table">
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
    const tableSelect = document.querySelector('#table');
    const waiterSelect = document.querySelector('#waiter');
    const menuGroupChoices = new Choices(menuGroupSelect);
    const menuChoices = new Choices(menuSelect);
    const tableChoices = new Choices(tableSelect);
    const waiterChoices = new Choices(waiterSelect);

    const printBtn = document.querySelector('#print');
    const resultTable = document.querySelector('#results-table tbody');
    printBtn.addEventListener('click', printHandler);

    function printHandler () {
        const token=document.querySelector('#_token').value;

        let lines = [];
        for (let item of resultTable.children) {
             const line = {
                 menuName : item.children[0].innerHTML,
                 menuPrice : item.children[1].innerHTML,
                 menuQuantity : item.children[2].innerHTML,
                 total : item.children[3].innerHTML,               
             }
             lines.push(line);            
        }                
            
        fetch(`/admin/reports/menus/printMenuReport`, {
            headers: {
                "Content-Type": "application/json",
                "Accept": "application/json",
                "X-Requested-With": "XMLHttpRequest",
                "X-CSRF-Token": token
            },
            credentials: "same-origin",
            method: 'POST',
            body: JSON.stringify({
                lines
            })
        })
        .then(res=>res.json())
        .then(res => {
            console.log(res);
        })
    }
})()
</script>
@endsection
