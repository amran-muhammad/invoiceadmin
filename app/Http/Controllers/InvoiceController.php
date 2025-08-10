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

    public function index(Request $request)
    {
        $query = Invoice::query();

        if ($request->invoice_number) {
            $query->where('invoice_number', 'like', "%{$request->invoice_number}%");
        }
        if ($request->mobile) {
            $query->where('mobile', 'like', "%{$request->mobile}%");
        }
        if ($request->client_name) {
            $query->where('client_name', 'like', "%{$request->client_name}%");
        }
        if ($request->item_name) {
            $query->whereRaw("LOWER(JSON_EXTRACT(items, '$[*].name')) LIKE ?",['%' . strtolower($request->item_name) . '%']);
        }
       
        $query->where('shop_id', $request->user()->id);
        

        return response()->json($query->latest()->paginate(10));
    }


    public function show($id)
    {
        return response()->json(Invoice::findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $invoice = Invoice::findOrFail($id);
        $invoice->update($request->all());
        return response()->json($invoice);
    }

    public function destroy($id)
    {
        Invoice::findOrFail($id)->delete();
        return response()->json(['message' => 'Deleted successfully']);
    }
}
