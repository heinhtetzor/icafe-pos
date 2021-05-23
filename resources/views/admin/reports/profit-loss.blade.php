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
                <a href="{{route('admin.reports.profit-loss')}}">ယခုလ</a>
            </span>
        </h3>
        <section class="charts">
            <div class="row">
                <div class="col-md-6">
                    <canvas id="barchart"></canvas>
                </div>
                <div class="col-md-6">
                    <table id="summaryTable" class="table table-hovered">
                        
                    </table>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <canvas id="dailychart"></canvas>
                </div>
            </div>
        </section>
    </div>
@endsection
@section('js')
<!-- Include Choices JavaScript -->
<script src="/litepicker/litepicker.js"></script>
<script src="/choices/scripts/choices.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.2.1/chart.min.js" integrity="sha512-tOcHADT+YGCQqH7YO99uJdko6L8Qk5oudLN6sCeI4BQnpENq6riR6x9Im+SGzhXpgooKBRkPsget4EOoH5jNCw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script>
    (() => {

        const datePicker = document.querySelector('#datePicker');
        const picker = new Litepicker({
            element: datePicker,
            singleMode: false
        });

        const params = new URLSearchParams(location.search);
        const date = params.get('date');        

        const summaryTable = document.querySelector('#summaryTable');

        
        //retreive sales and expenses and calculate profits
        fetch (`/admin/reports/profit-loss/get-data-for-menu-groups-bar-chart?date=${date ? date : ""}`)
        .then (res => res.json())
        .then (res => {
            let sales_total_arr = [];
            let expenses_total_arr = [];
            let profits_total_arr = [];
            let menugroups_name_arr = [];

            let summary = [];

            let totalSales = 0;
            let totalExpenses = 0;


            let menu_groups = res.menuGroups;        

            let searchInMenuGroupsWithSales = (menuGroupId) => {
                let total = 0;
                res.menuGroupsWithSales.forEach (ms => {
                    if (ms.id == menuGroupId)                                            
                        total = ms.total;
                });
                return total;
            }

            let searchInMenuGroupsWithExpenses = menuGroupId => {
                let total = 0;
                res.menuGroupsWithExpenses.forEach (me => {
                    if (me.id == menuGroupId)                                            
                        total = me.total;
                });
                return total;
            }
 
            for (let i = 0; i < menu_groups.length; i++) {         
                let sales_total = searchInMenuGroupsWithSales(menu_groups[i].id);                
                let expenses_total = searchInMenuGroupsWithExpenses(menu_groups[i].id);                
                menugroups_name_arr.push(menu_groups[i].name);

                sales_total_arr.push(sales_total);
                totalSales += sales_total;

                expenses_total_arr.push(expenses_total);
                totalExpenses += expenses_total;


                profits_total_arr.push(sales_total - expenses_total);

                let summaryObj = {
                    'menu_group': menu_groups[i].name,
                    'sales': sales_total,
                    'expenses': expenses_total
                };
                summary.push(summaryObj);

            }

            //catering for non-menugroup data
            menugroups_name_arr.push("အထွေထွေ");
            expenses_total_arr.push(res.generalExpenses.total);    
            totalExpenses += +res.generalExpenses.total;
            
            summaryTable.innerHTML += 
            `
            <thead>
                <tr>
                    <th></th>
                    <th>အရောင်း</th>
                    <th>အဝယ်</th>
                    <th>အမြတ်</th>
                </tr>
            </thead>            
            `;
            summaryTable.innerHTML += 
                `
                <tbody>
                `;
            let salesTotal = 0
            let expensesTotal = 0;
            summary.forEach (s => {
                salesTotal += +s.sales;
                expensesTotal += +s.expenses;

                summaryTable.innerHTML += 
                `
                 <tr>
                    <td>${s.menu_group}</td>
                    <td>${s.sales}</td>
                    <td>${s.expenses}</td>
                    <td>${(s.sales - s.expenses).toFixed(2)}</td>
                 </tr>
                `;
            })
            summaryTable.innerHTML += 
                `
                <tr>
                    <td>အထွေထွေ</td>
                    <td>-</td>
                    <td>${+res.generalExpenses.total}</td>
                    <td>${-res.generalExpenses.total}</td>
                </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <th>စုစုပေါင်း</th>
                        <th>${salesTotal}</th>
                        <th>${expensesTotal + +res.generalExpenses.total}</th>
                        <th>${salesTotal - expensesTotal - +res.generalExpenses.total}</th>
                    </tr>
                </tfoot>
                `;

        

            const data = {
                labels: menugroups_name_arr,
                datasets: [{
                    label: "အရောင်း",
                    data: sales_total_arr,
                    backgroundColor: 'lightblue',
                    opacity: 0.2
                }, {
                    label: "အဝယ်",
                    data: expenses_total_arr,
                    backgroundColor: 'orange'
                }, {
                    type: 'bar',
                    label: "အမြတ်",
                    data: profits_total_arr,
                    backgroundColor: 'green'
                }]
            };

            const options = {
                legend: {
                    display: true,
                    position: 'top',
                    labels: {
                        boxWidth: 80,
                        fontColor: 'black',                        
                    }
                }
            }

            const config = {
              type: 'bar',
              data,
              options
            };

            var barChart = new Chart(
                document.getElementById('barchart').getContext('2d'),
                config
            );
            
        })

        //get daily or monthly line chart
        fetch (`/admin/reports/profit-loss/get-data-for-daily-line-chart?date=${date ? date : ""}`)
        .then (res => res.json())
        .then (res => {            

            let dates = [];
            let sales_total_arr = [];

            for (let [key, val] of Object.entries(res.dailySales)) {
                dates.push(key);
                sales_total_arr.push(val);
            }


            const data = {
                labels: dates,
                datasets: [{
                    label: "အရောင်း",
                    data: sales_total_arr,
                    borderColor: 'darkgreen',
                    pointBackgroundColor: 'purple'                    
                } 
                
                ]
            };

            const options = {
                legend: {
                    display: true,
                    position: 'top',
                    labels: {
                        boxWidth: 80,
                        fontColor: 'black',                        
                    }
                }
            }

            const config = {
                type: 'line',
                data,
                options
            }

            const lineChart = new Chart(
                document.getElementById('dailychart').getContext('2d'),
                config
            );
        

        });



    })();
</script>

@endsection