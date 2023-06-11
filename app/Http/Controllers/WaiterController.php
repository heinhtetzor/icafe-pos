<?php

namespace App\Http\Controllers;

use App\Http\Requests\WaiterReqeust;
use App\Waiter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class WaiterController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $store_id = Auth()->guard('admin_account')->user()->store_id;
        $waiters = Waiter::where('store_id' ,$store_id)->orderBy('name')->get();
        return view('admin.waiters.index', [
            'waiters' => $waiters
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
    public function store(WaiterReqeust $request)
    {
        Waiter::create($request->all());        
        return redirect()->back()->with('msg', 'Waiter successfully created');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $waiter = Waiter::findorfail($id);
        return view('admin.waiters.edit', [
            'waiter' => $waiter
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(WaiterReqeust $request, $id)
    {
        Waiter::findorfail($id)->update($request->all());
        return redirect('/admin/waiters')->with('msg', 'Updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Waiter::findorfail(intval($id))->delete();
        return redirect('/admin/waiters');
    }
}
