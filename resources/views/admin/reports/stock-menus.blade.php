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
            <a href="{{route('admin.reports')}}">π </a>
            Menu / Menu α‘α―ααΊαα―α‘αα­α―ααΊ <div class="badge bg-danger">α‘αααΊαα¬αααΊαΈ (Stock)</div>
            @if (count($results) > 0)

            <a href="#" id="print" class="btn btn-info">π¨ Print</a>
            @endif
        </h3>
        {{-- CSRF token --}}
        <input type="hidden" name="_token" id="_token" value="{{csrf_token()}}">
        

        <div>
            <form action="{{route('admin.reports.stock-menus')}}" method="GET">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            {{-- menugroups select --}}
                            <select multiple name="menuGroup[]" id="menuGroup" class="form-control">
                                <option value="">Menu α‘α―ααΊαα―αα½α±αΈαα«</option>
                                @foreach($menuGroups as $menuGroup)
                                <option value="{{$menuGroup->id}}">{{$menuGroup->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <select multiple name="stockMenu[]" id="menu" class="form-control">
                                <option value="">Menu αα½α±αΈαα«</option>
                                @foreach($menus as $stock_menu)
                                <option value="{{$stock_menu->id}}">{{$stock_menu->menu->name}}</option>
                                @endforeach
                            </select>                     
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <input autocomplete="off" class="form-control" required type="text" id="datePicker" placeholder="αα±α·αα½α²αα½α±αΈαα«" name="date">                     
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
            {{$fromTime->format('d-M-Y')}} ααΎ {{$toTime->format('d-M-Y')}} αα­
            @if(count($filtered_menu_groups) > 0)
                @foreach($filtered_menu_groups as $mg)
                <div class="badge bg-primary">{{$mg->name}}</div>
                @endforeach
            @endif
            @if(count($filtered_stock_menus) > 0)
                @foreach($filtered_stock_menus as $m)
                    <div class="badge bg-success">{{$m->menu->name}}</div>
                @endforeach
            @endif
            <span style="float:right;font-weight:bolder">{{$total}} αα»ααΊ</span>

        </span>
    </div>
    <table class="table table-hover" id="results-table">
        <thead class="bg-success text-white">
            <th>Menu α‘αααΊ</th>
            <th>ααΎα―ααΊαΈ</th>
            <th style="text-align: center">α‘αα±α‘αα½ααΊ</th>
            <th style="text-align: center">αα»αααΊα·αα½α±</th>
        </thead>
        <tbody>
            @foreach ($results as $result)
            <tr>
                <td>{{$result->stockMenu->menu->name}}</td>
                <td>{{$result->cost}}</td> 
                <td style="text-align: center">{{$result->total}}</td> 
                <td style="text-align: center">{{$result->total * $result->cost}}</td> 
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
