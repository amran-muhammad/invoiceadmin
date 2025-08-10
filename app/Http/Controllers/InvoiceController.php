<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Invoice;

class InvoiceController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'invoice_number' => 'required|string|unique:invoices',
            'client_name' => 'required|string',
            'items' => 'required|array',
            'total' => 'required|numeric',
        ]);
        $invoice = Invoice::create($request->all());

        return response()->json([
            'message' => 'Invoice saved successfully!',
            'invoice' => $invoice
        ], 201);
    }
}
