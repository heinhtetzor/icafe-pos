<?php

namespace App\Http\Controllers;

use App\Customer;
use App\Http\Requests\CustomerRequest;
use App\Order;
use Exception;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $customers = Customer::orderBy('name')->get();
        return view('admin.customers.index', [
            "customers" => $customers
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.customers.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CustomerRequest $request)
    {
        try {
            Customer::create([
                "name" => $request->name,
                "business" => $request->business,
                "phone" => $request->phone,
                "address" => $request->address,
                "notes" => $request->notes,
                "status" => $request->status,
            ]);

            return redirect()->back()->with('msg', 'Customer successfylly created');
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //show orders from that customer
        try {
            $customer = Customer::findOrFail($id);

            return view('admin.customers.show', [
                "customer" => $customer
            ]);
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $customer = Customer::findOrFail($id);
        return view('admin.customers.edit', [
            "customer" => $customer
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(CustomerRequest $request, $id)
    {
        Customer::findOrFail($id)->update($request->all());
        return redirect('/admin/customers')->with('msg', 'Updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $ordersByCustomer = Order::where('customer_id', $id)->take(10);
            if ($ordersByCustomer) {
                throw new Exception("ဖျက်လို့မရပါ");
            }
 
            Customer::findOrFail(intval($id))->delete();
            return redirect("/admin/customers");
        }
        catch (Exception $e) {
            return redirect()->back()->with("error", $e->getMessage());
        }
    }
}
