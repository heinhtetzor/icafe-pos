@extends('layouts.admin')
@section('css')
<link rel="stylesheet" href="/css/tables.css">
@endsection
@section('content')
    <div class="container">
        <h2>
            <a href="{{ route('admin.masterdatamanagement') }}">🔙</a>
            Customer များ</h2>
        @if (session('msg'))
            {{ session('msg') }}
        @endif
        <h4>Create New Customer</h4>
        <section>
            <form action="{{ route('customers.store') }}" method="post">
                @csrf
                <div class="form-group">
                    <label for="name">အမည်</label>
                    <input type="text" autofocus name="name" class="form-control" placeholder="Enter Customer Name" required>
                    <p style="color:red">{{ $errors->first('name') }}</p>
                </div>
                <div class="form-group">
                    <label for="name">လုပ်ငန်း</label>
                    <input type="text" autofocus name="busienss" class="form-control" placeholder="Enter Customer Business" required>
                    <p style="color:red">{{ $errors->first('business') }}</p>
                </div>
                <div class="form-group">
                    <label for="name">ဖုန်း</label>
                    <input type="text" name="phone" class="form-control" placeholder="Enter Customer Phone" required>
                    <p style="color:red">{{ $errors->first('phone') }}</p>
                </div>
                <div class="form-group">
                    <label for="name">လိပ်စာ</label>
                    <textarea name="address" id="address" cols="30" rows="10" class="form-control" placeholder="Enter Customer Address"></textarea>
                    <p style="color:red">{{ $errors->first('address') }}</p>
                </div>
                <div class="form-group">
                    <label for="notes">မှတ်ချက်</label>
                    <textarea name="notes" id="notes" cols="30" rows="10" class="form-control" placeholder="Enter Remarks"></textarea>
                    <p style="color:red">{{ $errors->first('notes') }}</p>
                </div>


                <button type="submit" class="btn btn-primary">Submit</button>
            </form>
        </section>
        <section>
            <h2>All Customers</h2>
            <div class="tables-grid">
                <table>
                    <thead>
                        <tr>အမည်</tr>
                        <tr>လုပ်ငန်း</tr>
                        <tr>ဖုန်း</tr>
                        <tr>လိပ်စာ</tr>
                        <tr>မှတ်ချက်</tr>
                    </thead>
                </table>
                @foreach($customers as $customer)
                <a href="{{ route('customers.edit', $customer->id) }}" class="">
                    <span>{{ $customer->name }}</span>
                    <span>{{ $customer->business }}</span>
                    <span>{{ $customer->phone }}</span>
                    <span>{{ $customer->address }}</span>
                    <span>{{ $customer->notes }}</span>
                </a>
                @endForeach
            </div>
        </section>
    </div>
@endsection