@extends('layouts.admin')
@section('content')
<div class="container">
    <h2>
        <a href="{{route('admin.accountmanagement')}}">🔙</a>
        Editing {{ $kitchen->name}}</h2>  
    <section>
        <form action="{{ route('kitchens.update', $kitchen->id) }}" method="post">
            @csrf
            @method('put')
            <div class="form-group">
                <label for="name">Kitchen Name</label>
                <input value="{{$kitchen->name}}" name="name" type="text" class="form-control" placeholder="Enter Waiter Name" required>
                <p style="color:red">{{ $errors->first('name') }}</p>
            </div>
            <div class="form-group">
                <label for="name">Color</label>
                <input value="{{$kitchen->color}}" name="color" type="color" class="form-control" required>
                <p style="color:red">{{ $errors->first('color') }}</p>
            </div>

            <div class="form-group">
            <label for="menu_groups">Assign Menu Groups</label>
               <select multiple name="menu_groups[]" class="form-control" required>
                   <option>----</option>
                   @foreach($menu_groups as $menu_group)
                   <option 
                        @foreach($kitchen->menu_groups as $mgk)
                            @if($mgk->id==$menu_group->id)
                            selected
                            @endif
                        @endforeach
                   value="{{$menu_group->id}}">{{$menu_group->name}}</option>
                   @endforeach
                   <option></option>
               </select>
            </div>
            <div class="form-group">
                <label for="name">Username</label>
                <input value="{{$kitchen->username}}" name="username" type="text" class="form-control" placeholder="Enter Username" required>
                <p style="color:red">{{ $errors->first('username') }}</p>
            </div>
            <div class="form-group">
                <label for="name">Password</label>
                <input value="" name="password" type="password" class="form-control" placeholder="">
                <p style="color:red">{{ $errors->first('password') }}</p>
            </div>
            
            <button type="submit" class="btn btn-primary">Submit</button>
            <a class="btn btn-info" href="{{route('kitchens.index')}}">Back</a>
        </form>
        <hr>
        <button 
            onclick="document.querySelector('#delete-form').submit();" 
            class="btn btn-danger">
                Delete	
            </button>
            {{-- hidden delete form --}}
            <form id="delete-form" class="hidden" action="{{ route('kitchens.destroy', $kitchen->id) }}" method="post">
                @method('DELETE')
                @csrf
                <input type="hidden" name="id" value="{{ $kitchen->id }}">
            </form>
    </section>
</div>
@endsection