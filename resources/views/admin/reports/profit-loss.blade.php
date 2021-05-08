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
            <a href="javascript:history.back()">🔙</a>
            ယခုလ

            <span style="float:right;">
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#dateModal">Search</button>            
                <a href="{{route('admin.reports.profit-loss')}}">ယနေ့</a>
            </span>
        </h3>
    </div>
@endsection
@section('js')
<!-- Include Choices JavaScript -->
<script src="/litepicker/litepicker.js"></script>
<script src="/choices/scripts/choices.min.js"></script>
<script>
    (() => {

        const datePicker = document.querySelector('#datePicker');
        const picker = new Litepicker({
            element: datePicker,
            singleMode: false
        });
    })();
</script>

@endsection