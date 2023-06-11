<?php

namespace App\Http\Controllers;

use App\TableGroup;
use Exception;
use Illuminate\Http\Request;

class TableGroupController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        TableGroup::create([
            "name" => $request->name,
            "store_id" => $request->store_id
        ]);
        return redirect('/admin/tables');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\TableGroup  $tableGroup
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {                
        $table_group = TableGroup::with('tables')->findOrFail($id);
        return view('admin.tablegroups.show', [
            "table_group" => $table_group
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\TableGroup  $tableGroup
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $table_group = TableGroup::findorfail($id);
        return view('admin.tablegroups.edit', [
            "table_group" => $table_group
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\TableGroup  $tableGroup
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        TableGroup::findorfail($id)->update($request->all());
        return redirect('/admin/tablegroups/'.$id)->with('msg', 'Updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\TableGroup  $tableGroup
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {        
        try {
            TableGroup::findorfail(intval($id))->delete();
            return redirect('/admin/tables')->with('msg', 'Deleted successfully');
        }
        catch (Exception $e) {            
            return redirect()->back()->with('msg', 'ဖျက်မရပါ');
        }
    }
}
