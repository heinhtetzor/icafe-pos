<?php

namespace App\Http\Controllers;

use App\Http\Requests\TableRequest;
use App\Table;
use App\TableGroup;
use App\TableStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TableController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $store_id = Auth()->guard('admin_account')->user()->store_id;
        $tables = Table::where('store_id', $store_id)->orderBy('name')->get();
        $tables_groups = TableGroup::where('store_id', $store_id)->get();
        return view('admin.tables.index', [
            'tables' => $tables,
            'table_groups' => $tables_groups
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(TableRequest $request)
    {
        // whenver we create table
        // we also create new TableStatus record too
        $table = Table::create($request->all());
        TableStatus::create([
            'table_id' => $table->id,
            "store_id" => $request->store_id
        ]);
        return redirect()->back()->with('msg', 'Table successfully created.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Table  $table
     * @return \Illuminate\Http\Response
     */
    public function show(Table $table)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Table  $table
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $table = Table::findorfail($id);
        return view('admin.tables.edit', [
            'table' => $table
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Table  $table
     * @return \Illuminate\Http\Response
     */
    public function update(TableRequest $request, $id)
    {
        Table::findorfail($id)->update($request->all());
        return redirect('/admin/tables')->with('msg', 'Updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Table  $table
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Table::findorfail(intval($id))->delete();
        TableStatus::where('table_id', $id)->delete();
        return redirect('/admin/tables');
    }
}
