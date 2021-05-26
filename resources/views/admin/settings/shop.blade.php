@extends('layouts.admin')
@section('css')
<style type="text/css">
    #preview {
        border: 1px solid #efefef;
        border-radius: 8px;

    }
</style>
@endsection
@section('content')
<div class="container">
    <h4><a href="{{route('settings.index')}}">🔙</a>
    ဆိုင်အချက်အလက်</h4>
    <div class="row">
        @if(session('msg'))
        <p class="alert alert-success">{{ session('msg') }}</p>
        @endif
        @if(session('error'))
        <p class="alert alert-danger">{{ session('error') }}</p>
        @endif
        <form action="{{ route('settings.saveShop') }}" method="post">
            @csrf
            <div class="col-md-3">
                <div class="form-group">
                    <label for="shop_name">ဆိုင်အမည်</label>
                    <input value="{{ $shop_name ?? '' }}" type="text" name="shop_name" class="form-control" required autofocus>
                </div>

                <div class="form-group">
                    <label for="shop_line_1">Line 1</label>                                               
                    <input type="text" class="form-control" name="shop_line_1" value="{{ $shop_line_1 ?? '' }}">
                </div>

                <div class="form-group">
                    <label for="shop_line_2">Line 2</label>                       
                    <input type="text" class="form-control" name="shop_line_2" value="{{ $shop_line_2 ?? '' }}">
                </div>
                <hr>

                <div class="form-group">
                    <label for="printer_connector">Printer Connector</label>   
                    <input type="text" name="printer_connector" class="form-control" value="{{ $printer_connector ?? '' }}">                    
                </div>

                <br>
                <button class="btn btn-success">Save</button>
            </div>

            <div class="col-md-6">
                <section class="preview">
                        
                </section>
            </div>
        </form>
    </div>
</div>
@endsection