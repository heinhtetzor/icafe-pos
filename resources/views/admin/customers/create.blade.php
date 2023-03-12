@extends('layouts.admin')
@section('content')
<div class="container">
    <h2>
        <a href="{{route('customers.index')}}">🔙</a>
        Create new Customer</h2>  
    <section>
        @csrf
        @method('post')
        <form action="{{ route('customers.store') }}" method="post">
            <div class="form-group">
                <label for="name">အမည်</label>
                <input name="name" type="text" class="form-control" placeholder="Enter Customer Name" required>
                <p style="color:red">{{ $errors->first('name') }}</p>
            </div>
            <div class="form-group">
                <label for="business">လုပ်ငန်း</label>
                <input name="business" type="text" class="form-control" placeholder="Enter Customer Business" required>
                <p style="color:red">{{ $errors->first('business') }}</p>
            </div>
            <div class="form-group">
                <label for="phone">ဖုန်း</label>
                <input name="phone" type="text" class="form-control" placeholder="Enter Customer Phone" required>
                <p style="color:red">{{ $errors->first('phone') }}</p>
            </div>
            <div class="form-group">
                <label for="address">လိပ်စာ</label>
                <textarea name="address" id="address" cols="30" rows="10">
                </textarea>
                <p style="color:red">{{ $errors->first('address') }}</p>
            </div>
            <div class="form-group">
                <label for="name">မှတ်ချက်</label>
                <textarea name="notes" id="notes" cols="30" rows="10" class="form-control" placeholder="Enter Remarks">
                </textarea>
                <p style="color:red">{{ $errors->first('notes') }}</p>
            </div>


            <button type="submit" class="btn btn-primary">Submit</button>
            <a class="btn btn-info" href="{{ route('customers.index') }}">Back</a>
        </form>
    </section>
</div>
@endsection