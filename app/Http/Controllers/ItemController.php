<?php

namespace App\Http\Controllers;

use App\Http\Requests\ItemRequest;
use App\Item;
use App\MenuGroup;
use Exception;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $items = Item::orderby('created_at', 'DESC')->paginate(100);
        $menu_groups= MenuGroup::all();
        return view('admin.items.index', [
            "items" => $items,
            "menu_groups" => $menu_groups
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
    public function store(ItemRequest $request)
    {        
        $item = new Item();
        $item->name = $request->name;
        $item->cost = $request->cost;
        if (!empty($request->is_general_item) && $request->is_general_item == 1)
        {
            $item->is_general_item = 1;
        }
        if (empty($request->is_general_item))
        {
            $item->menu_group_id = $request->menu_group_id;
        }
        $item->save();
                
        return redirect()->back()->with('msg', 'Item Created Successfully.');
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
        $item = Item::findorfail($id);
        $menu_groups = MenuGroup::all();
        return view('admin.items.edit', [
            "menu_groups" => $menu_groups,
            "item" => $item
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ItemRequest $request, $id)
    {
        $item = Item::findorfail($id);        
        $item->name = $request->name;
        $item->cost = $request->cost;

        if (!empty($request->is_general_item) && $request->is_general_item == 1)
        {
            $item->is_general_item = 1;
            $item->menu_group_id = null;
        }
        if (empty($request->is_general_item))
        {
            $item->is_general_item = 0;
            $item->menu_group_id = $request->menu_group_id;
        }
        $item->save();        
        return redirect()->back()->with('msg', 'Updated Successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Item::findorfail(intval($id))->delete();
        return redirect('/admin/items');
    }
}
