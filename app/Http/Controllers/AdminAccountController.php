<?php

namespace App\Http\Controllers;

use App\AdminAccount;
use App\Http\Requests\AdminAccountRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminAccountController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $admin_accounts = AdminAccount::orderBy('username')->get();
        return view('admin.admin_accounts.index', [
            'admin_accounts' => $admin_accounts
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
    public function store(AdminAccountRequest $request)
    {        
        AdminAccount::create($request->all());        
        return redirect()->back()->with('msg', 'Admin Account successfully created.');      
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\AdminAccount  $adminAccount
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // $admin_account = AdminAccount::findorfail($id);
        
        // return view('admin.admin_accounts.show', [            
        //     'admin_account' => $admin_account
        // ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\AdminAccount  $adminAccount
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $admin_account = AdminAccount::findorfail($id);
        return view('admin.admin_accounts.edit', [
            'admin_account' => $admin_account
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\AdminAccount  $adminAccount
     * @return \Illuminate\Http\Response
     */
    public function update(AdminAccountRequest $request, $id)
    {
        AdminAccount::findorfail($id)->update($request->all());
        return redirect('/admin/admin_accounts')->with('msg', 'Updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\AdminAccount  $adminAccount
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        AdminAccount::findorfail(intval($id))->delete();
        return redirect('/admin/admin_accounts');
    }
}
