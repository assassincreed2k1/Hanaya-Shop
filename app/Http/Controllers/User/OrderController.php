<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Order\Order;
use App\Models\Order\OrderDetail;
use App\Models\Product\Product;
use App\Models\Cart\Cart;
use Illuminate\Support\Facades\Session;


class OrderController extends Controller
{
    public function index(){
        $userId = Auth::id();
        $orders = Order::with('orderDetail.product')
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('page.order.index', compact('orders'));
    }

    public function show($orderId)
    {
        $order = Order::with('orderDetail.product')->findOrFail($orderId);
        return view('page.order.show', compact('order'));
    }

    public function cancel($orderId)
{
    $order = Order::findOrFail($orderId);

    DB::beginTransaction();
    try {
        foreach ($order->orderDetail as $detail) {
            $product = $detail->product;
            $product->stock_quantity += $detail->quantity;
            $product->save();
        }
        $order->status = 'canceled';
        $order->save();

        DB::commit();

        return redirect()->route('order.index')->with('success', 'Order has been cancelled successfully.');
    } catch (\Exception $e) {
        DB::rollBack();
        return redirect()->back()->with('error', 'An error occurred while cancelling the order: ' . $e->getMessage());
    }
}


}
