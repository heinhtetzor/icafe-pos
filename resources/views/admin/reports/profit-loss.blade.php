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
    <!-- Modal -->
<div class="modal fade" id="dateModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">ရှာရန်</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form method="GET" action="">
          <div class="modal-body">
              <div class="form-group">
                  <input placeholder="နေ့စွဲရွေးပါ" autocomplete="off" class="from-control" type="text" name="date" id="datePicker">                    
              </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary">Search</button>
        </div>
      </form> 
      </div>
    </div>
  </div>

    <div class="container">
        <h3>
            <a href="{{route('admin.reports')}}">🔙</a>
            ယခုလ

            <span style="float:right;">
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#dateModal">Search</button>            
                <a href="{{route('admin.reports.profit-loss')}}">ယနေ့</a>
            </span>
        </h3>
        <section class="charts">
            <div class="row">
                <div class="col-md-6">
                    <div id="barchart"></div>
                </div>
            </div>
        </section>
    </div>
@endsection
@section('js')
<!-- Include Choices JavaScript -->
<script src="/litepicker/litepicker.js"></script>
<script src="/choices/scripts/choices.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    (() => {

        const datePicker = document.querySelector('#datePicker');
        const picker = new Litepicker({
            element: datePicker,
            singleMode: false
        });

        const params = new URLSearchParams(location.search);
        const date = params.get('date');        


        //get all menu groups

        //
        
        fetch (`/admin/reports/profit-loss/get-data-for-menu-groups-bar-chart?date=${date ? date : ""}`)
        .then (res => res.json())
        .then (res => {
            let sales_total_arr = [];
            let expenses_total_arr = [];
            let menugroups_sales_arr = [];
            let menugroups_expenses_arr = [];

            for (obj of res.menuGroupsWithSales) {
                sales_total_arr.push(obj.total);
                menugroups_sales_arr.push(obj.name);
            }
            for (obj of res.menuGroupsWithExpenses) {
                expenses_total_arr.push(obj.total);
                if (!obj.name && obj.is_general_item == 1) {
                    menugroups_expenses_arr.push("အထွေထွေ");
                }
                else {                    
                    menugroups_expenses_arr.push(obj.name);
                }                
            }
            var options = {
            chart: {
                type: 'bar'
            },
            colors: [
                "#278a58", "#b02139"
            ],
            series: [{
                name: 'ရောင်းရငွေ',
                data: sales_total_arr
            }, {
                name: 'သုံးငွေ',
                data: expenses_total_arr,

            }
        ],
            
            xaxis: {
                categories: menugroups_sales_arr
            }
            }
    
            new ApexCharts(document.querySelector("#barchart"), options).render();            
        })


    })();
</script>

@endsection