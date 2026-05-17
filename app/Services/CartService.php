<?php

namespace App\Services;

use App\Http\Requests\Cart\StoreCartItemRequest;
use App\Http\Requests\Cart\UpdateCartItemRequest;
use App\Models\Cart;
use App\Models\CartItem;
use Illuminate\Support\Facades\Cache;

class CartService
{
    public function getOrCreateCart(int $userId, bool $withItems = false): Cart
    {
        $cart = Cart::firstOrCreate(['user_id' => $userId]);

        if ($withItems) {
            $cart->load('items.product');
        }

        return $cart;
    }

    public function addItem(int $userId, StoreCartItemRequest $request): CartItem
    {

        $validated = $request->validated();
        $cart = $this->getOrCreateCart($userId);


        
        $item = $cart->items()->where('product_id', $validated['product_id'])->first();
        if ($item) {
            $item->quantity += $validated['quantity'];
            $item->save();
        } else {
            $item = $cart->items()->create($validated);
        }

        $item->load('product');

        return $item;
    }

    public function updateItem(int $userId, int $itemId, UpdateCartItemRequest $request): CartItem
    {
        $validated = $request->validated();
        $cart = $this->getOrCreateCart($userId);

        $item = $cart->items()->where('id', $itemId)->firstOrFail();
        $item->update([
            'quantity' => $validated['quantity'],
        ]);
        $item->load('product');

        return $item;
    }

    public function removeItem(int $userId, int $itemId): void
    {
        $cart = $this->getOrCreateCart($userId);

        $item = $cart->items()->where('id', $itemId)->firstOrFail();
        $item->delete();
    }
}
