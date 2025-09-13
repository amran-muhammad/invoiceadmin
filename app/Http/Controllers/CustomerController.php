<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Customer::query();
        $query->where('shop_id', $request->user()->id);
        if ($request->name) {
            $query->where('name', 'like', "%{$request->name}%");
        }
        return response()->json($query->latest()->paginate(10));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $customer = Customer::create($request->all());

        return response()->json([
            'message' => 'Customer saved successfully!',
            'customer' => $customer
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Customer $customer)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Customer $customer)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request,$id)
    {
        $invoice = Customer::findOrFail($id);
        $invoice->update($request->all());
        return response()->json($invoice);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        Customer::findOrFail($id)->delete();
        return response()->json(['message' => 'Deleted successfully']);
    }
}
