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
        height: 80px;
        width: 80px;
        position: relative;
        text-align: center;        
        position: relative;
        background-image: url('/assets/table-icon.png');
        background-size: 80px;
        background-blend-mode: overlay;
        background-repeat: no-repeat;
        cursor: pointer;
        border: 5px solid green;
    }
    .tables-grid-item-occupied {
        filter: grayscale(1);
    }

    .tables-grid-item  span {
        font-family: Arial, Helvetica, sans-serif;
        font-weight: 900;
        /* position: absolute;
        top: 40%; */
        font-size: 1rem;
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

            @if($existing_express)
            <a style="float:right;" class="btn btn-primary" href="{{ route('waiter.express.home') }}">
                Express
            </a>         
            @endif
            </h3> 
            <br>
            @endif

            <section id="table-groups">
            </section>
                        
        </div>
    </div>
</div>
@endsection
@section('script')
<script>
    const _TABLE_OCCUPIED = 1;

    const is_admin = !!"{{Auth::guard('admin_account')->check()}}";    
    
    // get table statuses
    async function fetchTableStatuses () {
        const response = await fetch (`/api/table-statuses`);
        return await response.json();        
    }

    function createTableGroupContainer (element, tableGroups) {
        element.innerHTML = "";
        for (let tableGroup of tableGroups) {
            const tableGroupEle = document.createElement('div');
            tableGroupEle.classList += 'table-group';
            element.appendChild(tableGroupEle);

            const tableGroupNameEle = document.createElement('span');
            tableGroupNameEle.classList += 'table-group-name';
            tableGroupNameEle.innerText = tableGroup.name;
            tableGroupEle.appendChild(tableGroupNameEle);

            const tableGridEle = document.createElement('div');
            tableGridEle.classList += 'tables-grid';

            for (let table of tableGroup.tables) {
                const tableLink = document.createElement('a');
                const href = is_admin ? `/admin/pos/tables/${table.id}` : `/waiter/${table.id}/pos`;
                tableLink.classList += 'tables-grid-item';
                tableLink.setAttribute('href', href);
                if (table.table_status.status === _TABLE_OCCUPIED) {
                    tableLink.classList += ' tables-grid-item-occupied';
                    tableLink.innerHTML = `<span style="text-decoration:line-through;">${table.name}</span>`;
                } else {
                    tableLink.innerHTML = `<span>${table.name}</span>`;
                }
                tableGridEle.appendChild(tableLink);
            }
            tableGroupEle.appendChild(tableGridEle);
            element.appendChild(tableGroupEle);
        }
    }

    async function updateTables () {
        console.log('fetching tables..');
        const table_groups = await fetchTableStatuses();        

        const tableGroupEle = document.querySelector('#table-groups');
        createTableGroupContainer(tableGroupEle, table_groups);
    }

    (async () => {
        await updateTables();
        setInterval(updateTables, 3000);
    }) ();
</script>
@endsection