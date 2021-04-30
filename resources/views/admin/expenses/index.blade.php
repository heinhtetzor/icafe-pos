@extends('layouts.admin')
@section('content')
<div class="container">
    <section class="top-bar">
        <a href="{{ route('expenses.create') }}" class="btn btn-success">🧾 အသစ်</a>
    </section>
    <section class="list">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Invoice No</th>
                    <th>နေ့စွဲ</th>
                    <th>စုစုပေါင်း</th>
                    <th>Created By</th>
                </tr>
            </thead>
        </table>
    </section>
</div>
@endsection
@section('js')
@endsection