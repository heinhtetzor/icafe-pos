@extends('layouts.admin')
@section('content')
    <h2>Editing {{ $menu_group->name}}</h2>  
    <section>
        <form action="{{ route('menugroups.update', $menu_group->id) }}" method="post">
            @csrf
            @method('put')
            @if (session('error'))
            <p class="alert alert-danger">{{ session('error') }}</p>
            @endif

            
            <input name="store_id" type="hidden" value="{{Auth::guard('admin_account')->user()->store_id}}"/>

            <div class="col-md-4">
                <div class="form-group">
                    <label for="name">Menu အုပ်စု အမည်</label>
                    <input value="{{$menu_group->name}}" name="name" type="text" class="form-control" placeholder="Enter Menu Group Name" required>
                    <p style="color:red">{{ $errors->first('name') }}</p>
                </div>
    
                <div class="form-group">
                    <label for="name">Color</label>
                    <input value="{{$menu_group->color}}" name="color" type="color" class="form-control" placeholder="Enter Menu Group Name" required>
                    <p style="color:red">{{ $errors->first('color') }}</p>
                </div>
    
                <div class="form-group">
                    <input @if ($menu_group->print_slip == 1) checked  @endif name="print_slip" value="yes" class="form-check-input" type="checkbox" id="print_slip_radio">
                    <label class="form-check-label" for="print_slip_radio">
                    စလစ်ထုတ်မည်
                    </label>
                </div>
                <br>

                <div class="form-group">
                    <input type="hidden" name="status" value="0"/>
                    <input {{$menu_group->status == 1 ? "checked" : ""}} name="status" value="1" class="form-check-input" type="checkbox" id="active_menu_radio">
                    <label class="form-check-label" for="active_menu_radio">
                        Active
                    </label>
                </div>
                <br>
    
                <button type="submit" class="btn btn-primary">Submit</button>
                <a class="btn btn-info" href="{{ route('menugroups.index') }}">Back</a>
            </div>
        </form>
        <hr>
        <button 
            onclick="document.querySelector('#delete-form').submit();" 
            class="btn btn-danger">
                Delete	
            </button>
            {{-- hidden delete form --}}
            <form id="delete-form" class="hidden" action="{{ route('menugroups.destroy', $menu_group->id) }}" method="post">
                @method('DELETE')
                @csrf
                <input type="hidden" name="id" value="{{ $menu_group->id }}">
            </form>
    </section>
@endsection
@section('js')

@endsection