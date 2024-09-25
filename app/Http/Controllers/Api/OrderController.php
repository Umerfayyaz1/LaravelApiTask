<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    public function placeOrder(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $product = Product::find($request->product_id);

        if ($product->quantity < $request->quantity) {
            return response()->json(['message' => 'Insufficient quantity'], 400);
        }

        $order = Order::create([
            'user_id' => Auth::id(), // Set the authenticated user
            'product_id' => $request->product_id,
            'quantity' => $request->quantity,
        ]);

        $product->decrement('quantity', $request->quantity); // Reduce product quantity

        return response()->json(['message' => 'Order placed successfully', 'order' => $order], 201);
    }

    public function getAllOrders()
    {
        return Order::with('product', 'user')->get(); // Fetch all orders with product and user details
    }

    public function markOrderAsPlaced($id)
    {
        $order = Order::findOrFail($id);
        $order->status = 'placed';
        $order->save();

        return response()->json(['message' => 'Order marked as placed'], 200);
    }

    public function updateOrderStatus(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        $validatedData=Validator::make($request->all(),
        [
            'status' => 'required|string|in:dispatched,delivered',
        ]);

        if($validatedData->fails()){
            return response()->json([
                'status'=>false,
                'message'=>'validation error',
                'errors'=> $validatedData->errors()
            ],401);
        }

        $order->delivery_status = $request->status; // Update the delivery_status field
        $order->save();

        return response()->json(['message' => 'Order status updated'], 200);
    }


    public function getUserOrders()
    {
        $userId = Auth::id();
        return Order::where('user_id', $userId)->with('product')->get(); // Fetch user's orders
    }
}
