<?php

namespace App\Http\Controllers;

use App\Kitchen;
use App\MenuGroup;
use App\MenuGroupKitchen;
use App\Http\Requests\KitchenRequest;
use Illuminate\Http\Request;

class KitchenController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $store_id = Auth()->guard('admin_account')->user()->store_id;
        $kitchens=Kitchen::where('store_id', $store_id)->orderBy('username')->get();
        $menu_groups=MenuGroup::where('store_id', $store_id)->get();
        return view('admin.kitchens.index', [
            'kitchens'=>$kitchens,
            'menu_groups'=>$menu_groups
        ]);
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
    public function store(KitchenRequest $request)
    {        
        $kitchen=Kitchen::create($request->except('menu_groups'));
        $kitchen->menu_groups()->attach($request->menu_groups);     
        return redirect()->back()->with('msg', 'Kitchen successfully created.');      
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Kitchen  $kitchen
     * @return \Illuminate\Http\Response
     */
    public function show(Kitchen $kitchen)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Kitchen  $kitchen
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $kitchen=Kitchen::findorfail($id);                
        $menu_groups=MenuGroup::all();        
        return view("admin.kitchens.edit", [
            "kitchen"=>$kitchen,            
            "menu_groups"=>$menu_groups
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Kitchen  $kitchen
     * @return \Illuminate\Http\Response
     */
    public function update(KitchenRequest $request, $id)
    {                
        $kitchen=Kitchen::findorfail($id);
        if($request->input('password')) {
            $kitchen->update($request->except('menu_groups'));
        }
        $kitchen->update($request->except('menu_groups', 'password'));
        $kitchen->menu_groups()->sync($request->menu_groups);
        return redirect('/admin/kitchens')->with('msg', 'Updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Kitchen  $kitchen
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Kitchen::findorfail($id)->delete();
        return redirect('/admin/kitchens');
    }
}
