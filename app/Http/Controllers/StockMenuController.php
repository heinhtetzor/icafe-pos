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
    public function index()
    {
        $stock_menus = StockMenu::all();
        return view('admin.stockmenus.index', [
            "stock_menus" => $stock_menus
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
