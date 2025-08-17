<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class AdminUserController extends Controller
{
     public function index(Request $request)
    {
        $query = User::query();

        if ($request->name) {
            $query->where('name', 'like', "%{$request->name}%");
        }
        if ($request->mobile) {
            $query->where('mobile', 'like', "%{$request->mobile}%");
        }
        if ($request->email) {
            $query->where('email', 'like', "%{$request->email}%");
        }
    
        return response()->json($query->latest()->paginate(10));
    }

    // Update the item
    public function update(Request $request, $id)
    {
        $item = User::findOrFail($id); // Retrieve the item
        $item->status = $request->input('status');
        // Update other fields as needed
        $item->save(); // Save the updated item

        return response()->json([
            'message' => 'updated successfully!',
            'item' => $item
        ], 200);
    }


}