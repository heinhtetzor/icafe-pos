@extends('layouts.admin')
@section('css')
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
            အဝယ်ပစ္စည်း / Menu အုပ်စုအလိုက် <div class="badge bg-info">အဝယ်စာရင်း</div>
        </h3>
        

        <div>
            <form action="{{route('admin.reports.items')}}" method="GET">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            {{-- menugroups select --}}
                            <select multiple name="menuGroup[]" id="menuGroup" class="form-control">
                                <option value="">Menu အုပ်စုရွေးပါ</option>
                                @foreach($menuGroups as $menuGroup)
                                <option value="{{$menuGroup->id}}">{{$menuGroup->name}}</option>
                                @endforeach
                                <option value="is_general_item">အထွေထွေ</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <select multiple name="item[]" id="item" class="form-control">
                                <option value="">Menu ရွေးပါ</option>
                                @foreach($items as $item)
                                <option value="{{$item->id}}">{{$item->name}}</option>
                                @endforeach
                            </select>                     
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <input autocomplete="off" class="form-control" required type="text" id="datePicker" placeholder="နေ့စွဲရွေးပါ" name="date">                     
                        </div>                
                    </div>
                </div>   
                <div style="float:right">
                    <input name="group_by_expense" class="form-check-input" type="checkbox" id="group_by_expense">
                    <label class="form-check-label" for="group_by_expense">
                    ဘောင်ချာအလိုက်
                    </label>
                    <button class="btn btn-dark"><i class="bi bi-search"></i> Search</button>
                </div>             
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
                @if ($is_general_item_requested)
                <div class="badge bg-primary">အထွေထွေ</div>
                @endif 
            @endif
            @if(count($filtered_items) > 0)
                @foreach($filtered_items as $i)
                    <div class="badge bg-success">{{$i->name}}</div>
                @endforeach
            @endif
            <span style="float:right;font-weight:bolder">{{$total}} ကျပ်</span>

        </span>
    </div>
    <table class="table table-hover">
        <thead class="bg-success text-white">
            @if ($is_group_by_expense)
            <th>ဘောင်ချာနံပါတ်</th>
            <th>အချိန်</th>
            @endif
            <th>ပစ္စည်း အမည်</th>
            <th>အမျိုးအစား</th>
            <th>နှုန်း</th>
            <th style="text-align: center">အရေအတွက်</th>
            <th style="text-align: center">Unit</th>
            <th style="text-align: center">ကျသင့်ငွေ</th>
        </thead>
        <tbody>
            @foreach ($results as $result)
            <tr>
                @if ($is_group_by_expense)                            
                    <td>{{$result->expense->invoice_no}}</td>                    
                    <td>{{ $result->expense->datetime->format('h:i a') }} {{ $result->expense->datetime->format('d-M-Y') }}</td>
                @endif
                <td>{{$result->item->name}}</td>
                <td>{{$result->is_general_item == 1 ? "အထွေထွေ" : $result->menu_group->name}}</td>
                <td>{{$result->cost}}</td> 
                <td style="text-align: center">{{$result->total}}</td> 
                <td style="text-align: center">{{ $result->unit }}</td>
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
    const itemSelect = document.querySelector('#item');
    const menuGroupChoices = new Choices(menuGroupSelect);
    const menuChoices = new Choices(itemSelect);
})()
</script>
@endsection
