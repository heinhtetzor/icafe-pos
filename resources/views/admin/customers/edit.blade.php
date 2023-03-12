@extends('layouts.admin')
@section('content')
<div class="container">
    <h2>
        <a href="{{route('customers.index')}}">🔙</a>
        Editing {{ $customer->name}}</h2>  
    <section>
        @csrf
        @method('put')
        <form action="{{ route('customers.update', $customer->id) }}" method="post">
            <div class="form-group">
                <label for="name">အမည်</label>
                <input value="{{$customer->name}}" name="name" type="text" class="form-control" placeholder="Enter Customer Name" required>
                <p style="color:red">{{ $errors->first('name') }}</p>
            </div>
            <div class="form-group">
                <label for="business">လုပ်ငန်း</label>
                <input value="{{$customer->business}}" name="business" type="text" class="form-control" placeholder="Enter Customer Business" required>
                <p style="color:red">{{ $errors->first('business') }}</p>
            </div>
            <div class="form-group">
                <label for="phone">ဖုန်း</label>
                <input value="{{$customer->phone}}" name="phone" type="text" class="form-control" placeholder="Enter Customer Phone" required>
                <p style="color:red">{{ $errors->first('phone') }}</p>
            </div>
            <div class="form-group">
                <label for="address">လိပ်စာ</label>
                <textarea name="address" id="address" cols="30" rows="10">
                    {{ $customer->address }}
                </textarea>
                <p style="color:red">{{ $errors->first('address') }}</p>
            </div>
            <div class="form-group">
                <label for="name">မှတ်ချက်</label>
                <textarea name="notes" id="notes" cols="30" rows="10" class="form-control" placeholder="Enter Remarks">
                    {{ $customer->notes }}
                </textarea>
                <p style="color:red">{{ $errors->first('notes') }}</p>
            </div>


            <button type="submit" class="btn btn-primary">Submit</button>
            <a class="btn btn-info" href="{{ route('customers.index') }}">Back</a>
        </form>
        <hr>
        <button 
            onclick="document.querySelector('#delete-form').submit();" 
            class="btn btn-danger">
                Delete	
            </button>
            {{-- hidden delete form --}}
            <form id="delete-form" class="hidden" action="{{ route('customers.destroy', $customer->id) }}" method="post">
                @method('DELETE')
                @csrf
                <input type="hidden" name="id" value="{{ $customer->id }}">
            </form>
    </section>
</div>
@endsection