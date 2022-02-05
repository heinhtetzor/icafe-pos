<?php

namespace App\Http\Controllers;

use App\Table;
use Illuminate\Http\Request;

class PosController extends Controller
{
    public function index (Request $request)
    {
        return view('pos.index');
    }

    public function create (Request $request, $id)
    {
        $table = Table::find($id);
        $table->setIsProcessing(true);
        if (is_null ($table)) {
            return "Table not found";
        }
        
        return view('pos.create');
    }
}
