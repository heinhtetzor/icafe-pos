<?php

namespace App\Http\Controllers;

use App\Http\Requests\MenuGroupRequest;
use App\Menu;
use App\MenuGroup;
use Exception;
use Illuminate\Http\Request;

class MenuGroupController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $store_id = Auth()->guard('admin_account')->user()->store_id;
        $menu_groups = MenuGroup::where('store_id', $store_id)->orderBy('name')->get();
        $menus = Menu::where('store_id', $store_id)->get();
        return view('admin.menugroups.index', [
            'menu_groups' => $menu_groups,
            'menus' => $menus,
            'selected_menu_group' => 'ALL'
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.menugroups.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(MenuGroupRequest $request)
    {
        MenuGroup::create($request->all());
        return redirect()->back()->with('msg', 'Menu Group successfully created.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\MenuGroup  $menuGroup
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $menu_group = MenuGroup::findorfail($id);
        $store_id = Auth()->guard('admin_account')->user()->store_id;
        $menu_groups = MenuGroup::where('store_id', $store_id)->orderBy('created_at', 'ASC')->get();
        $menus = Menu::where('menu_group_id', $id)->get();
        return view('admin.menugroups.show', [
            'menus' => $menus,
            'menu_group' => $menu_group,
            'menu_groups' => $menu_groups,
            'selected_menu_group' => $id
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\MenuGroup  $menuGroup
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $menu_group = MenuGroup::findorfail($id);
        return view('admin.menugroups.edit', [
            'menu_group' => $menu_group
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\MenuGroup  $menuGroup
     * @return \Illuminate\Http\Response
     */
    public function update(MenuGroupRequest $request, $id)
    {        

        if (empty($request->print_slip))
        {
            $print_slip = 0;            
        }
        if (!empty($request->print_slip))
        {
            $print_slip = 1;
        }


        $data = [
            "name" => $request->name,
            "print_slip" => $print_slip,
            "color" => $request->color,
            "status" => $request->status
        ];

        MenuGroup::findorfail($id)->update($data);
        return redirect()->back()->with('msg', 'Updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\MenuGroup  $menuGroup
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            MenuGroup::findorfail(intval($id))->delete();
            return redirect('/admin/menugroups');
        }
        catch (Exception $e) {
            return redirect()->back()->with('error', "ဖျက်လို့မရပါ");
        }
    }
}
