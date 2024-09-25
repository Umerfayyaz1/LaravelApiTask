<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    // Fetch all products
    public function index()
    {
        $products = Product::all();
        return response()->json(['status' => true, 'products' => $products], 200);
    }

    // Show a single product
    public function show($id)
    {
        $product = Product::find($id);
        if (!$product) {
            return response()->json(['status' => false, 'message' => 'Product not found'], 404);
        }
        return response()->json(['status' => true, 'product' => $product], 200);
    }

    // Store a new product
    public function store(Request $request)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'quantity' => 'required|integer|min:1',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'errors' => $validator->errors()], 400);
        }

        // Handle image upload
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('uploads/images', 'public');
        }

        // Create product
        $product = Product::create([
            'name' => $request->name,
            'quantity' => $request->quantity,
            'description' => $request->description,
            'image' => $imagePath ? url('storage/' . $imagePath) : null,
        ]);

        return response()->json(['status' => true, 'message' => 'Product created successfully', 'product' => $product], 201);
    }

    // Update a product
    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        if (!$product) {
            return response()->json(['status' => false, 'message' => 'Product not found'], 404);
        }

        $validatedData = $request->validate([
            'name' => 'string|max:255',
            'quantity' => 'integer|min:0',
            'description' => 'string|nullable',
            'image' => 'image|mimes:jpeg,png,jpg,gif|max:2048|nullable', // Validate image
        ]);

        // Update product details
        $product->name = $request->input('name', $product->name);
        $product->quantity = $request->input('quantity', $product->quantity);
        $product->description = $request->input('description', $product->description);

        // Handle image upload
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('uploads/images', 'public');
            $product->image = url('storage/' . $imagePath); // Update the image path in DB
        }

        $product->save();

        return response()->json([
            'message' => 'Product updated successfully',
            'product' => $product,
        ], 200);
    }


    // Delete a product
    public function destroy($id)
    {
        $product = Product::find($id);
        if (!$product) {
            return response()->json(['status' => false, 'message' => 'Product not found'], 404);
        }

        // Delete product image if exists
        if ($product->image) {
            $imagePath = str_replace(url('storage/'), '', $product->image);
            Storage::disk('public')->delete($imagePath);
        }

        $product->delete();
        return response()->json(['status' => true, 'message' => 'Product deleted successfully'], 200);
    }
}
