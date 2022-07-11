<?php

namespace App\Http\Controllers;

use App\StockMenu;
use Illuminate\Http\Request;

class StockMenuController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $stock_menus = StockMenu::where('status', StockMenu::STATUS_ACTIVE);
        $stock_menus->when(!is_null ($request->sortByBalance), function ($q) use ($request) {
            $q->orderBy('balance', $request->sortByBalance);
        });
        // $stock_menus->when(!is_null ($request->sortByAlpha), function ($q) use ($request) {
        //     $q->whereHas('menu', function ($r) use ($request) {
        //         $r->orderBy('name', "ASC");
        //     });
        // });
        $stock_menus->when(!is_null ($request->search), function ($q) use ($request) {
            $q->whereHas('menu', function ($r) use ($request) {
                $r->where('name', 'LIKE', '%'.$request->search.'%');
            });
        });
        $stock_menus->when(!is_null ($request->menuGroupId), function ($q) use ($request) {
            $q->whereHas('menu', function ($r) use ($request) {
                $r->where('menu_group_id', $request->menuGroupId);
            });
        });
        return view('admin.stockmenus.index', [
            "stock_menus" => $stock_menus->get()
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
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\StockMenu  $stockMenu
     * @return \Illuminate\Http\Response
     */
    public function show(StockMenu $stockMenu)
    {
        $stock_menu_entries = $stockMenu->stockMenuEntries()->orderBy('created_at', 'desc')->paginate(20);
        return view('admin.stockmenus.show', [
            "stock_menu" => $stockMenu->load('menu'),
            "stock_menu_entries" => $stock_menu_entries
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\StockMenu  $stockMenu
     * @return \Illuminate\Http\Response
     */
    public function edit(StockMenu $stockMenu)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\StockMenu  $stockMenu
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, StockMenu $stockMenu)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\StockMenu  $stockMenu
     * @return \Illuminate\Http\Response
     */
    public function destroy(StockMenu $stockMenu)
    {
        //
    }
}
