<?php

namespace App\Http\Controllers;

use App\Http\Requests\MenuRequest;
use App\Menu;
use App\MenuGroup;
use App\OrderMenu;
use App\StockMenu;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MenuController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $menu_groups = MenuGroup::orderBy('name')->get();
        $menus = Menu::orderBy('name')->get();
        return view('admin.menus.index', [
            'menus' => $menus,
            'menu_groups' => $menu_groups
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
    public function store(MenuRequest $request)
    {
        try {
            DB::beginTransaction();
    
            $data = $request->all();
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image');
                $imageName = $imagePath->getClientOriginalName();
                $fileNameToStore = time(). '-menu-' .$imageName;
                $request->file('image')->storeAs('public/menu_images', $fileNameToStore);
                $data['image'] = $fileNameToStore;
            }
            
            $menu = Menu::create($data);
            
            if (!empty($request->is_stock_menu)) {
                StockMenu::createStockMenu($menu);
            }
    
            DB::commit();
            return redirect()->back()->with('msg', 'Menu successfully created');
        }
        catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with("error", $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Menu  $menu
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Menu  $menu
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $menu_groups = MenuGroup::orderBy('name')->get();
        $menu = Menu::findorfail($id);
        return view('admin.menus.edit', [
            'menu' => $menu,
            'menu_groups' => $menu_groups
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Menu  $menu
     * @return \Illuminate\Http\Response
     */
    public function update(MenuRequest $request, $id)
    {
        try {
            DB::beginTransaction();
            $data = $request->all();
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image');
                $imageName = $imagePath->getClientOriginalName();
                $fileNameToStore = time(). '-menu-' .$imageName;
                $request->file('image')->storeAs('public/menu_images', $fileNameToStore);
                $data['image'] = $fileNameToStore;
            }
            $menu = Menu::findorfail($id);
            $menu->update($data);
            
            if (!empty($request->is_stock_menu)) {            
                StockMenu::createStockMenu($menu);
            }
            else {
                StockMenu::disableStockMenu($menu);
            }
            DB::commit();
            return redirect()->back()->with('msg', 'Menu successfully updated');

        }
        catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with("error", $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Menu  $menu
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $oms = OrderMenu::where('order_id', 3434)->take(10);
            if ($oms) {
                throw new Exception("ဖျက်လို့မရပါ");
            }
            Menu::findorfail(intval($id))->delete();
            return redirect('/admin/menus');
        }
        catch (Exception $e) {
            return redirect()->back()->with("error", $e->getMessage());
        }
    }
}
