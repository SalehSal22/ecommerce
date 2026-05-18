<?php

namespace App\Services;

use App\Jobs\recieptEmail;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function listForUser(int $userId): Collection
    {
        return Order::with('items.product')
            ->where('user_id', $userId)
            ->latest()
            ->get();
    }

    public function getForUser(int $userId, int $orderId): Order
    {
        return Order::with('items.product')
            ->where('user_id', $userId)
            ->where('id', $orderId)
            ->firstOrFail();
    }

    public function placeOrder(int $userId): Order
    {
        $cart = Cart::with('items.product')
            ->where('user_id', $userId)
            ->first();

        if (!$cart || $cart->items->isEmpty()) {
            abort(422, 'Cart is empty.');
        }
        $locks = [];
        try {


            $items = $cart->items()->orderBy('product_id')->get();
            foreach ($items as $item) {
                $lock = Cache::lock('product_' . $item->product->id, 13);

                $lock->block(7);

                $locks[] = $lock;
            }
            $order = DB::transaction(function () use ($items, $cart, $userId) {

                $total = 0;

                $order = Order::create([
                    'user_id' => $userId,
                    'total' => 0,
                    'status' => 'placed',
                ]);

                foreach ($items as $item) {
                    $product = Product::lockForUpdate()->findOrFail($item->product_id);

                    if ($product->stock < $item->quantity) {
                        abort(422, 'Insufficient stock for product: ' . $product->name);
                    }

                    $product->stock -= $item->quantity;
                    $product->save();

                    $price = $product->price;
                    $subtotal = $price * $item->quantity;
                    $total += $subtotal;

                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $product->id,
                        'quantity' => $item->quantity,
                        'price' => $price,
                        'subtotal' => $subtotal,
                    ]);
                }

                $order->update(['total' => $total]);

                $cart->items()->delete();

                return $order;
            });

            $order->load('items.product');
            recieptEmail::dispatch($order);
            return $order;
        } finally {
            foreach ($locks as $lock) {
                try {
                    $lock->release();
                } catch (\Throwable) {
                }
            }
        }
    }
}
