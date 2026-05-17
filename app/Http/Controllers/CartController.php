<?php

namespace App\Http\Controllers;

use App\Http\Requests\Cart\StoreCartItemRequest;
use App\Http\Requests\Cart\UpdateCartItemRequest;
use App\Http\Resources\CartItemResource;
use App\Http\Resources\CartResource;
use App\Services\CartService;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function __construct(protected CartService $service) {}
    public function index(Request $request)
    {
        $cart = $this->service->getOrCreateCart($request->user()->id, true);

        return new CartResource($cart);
    }

    public function store(StoreCartItemRequest $request)
    {
        $item = $this->service->addItem($request->user()->id, $request);

        return (new CartItemResource($item))->response()->setStatusCode(201);
    }

    public function update(UpdateCartItemRequest $request, int $id)
    {
        $item = $this->service->updateItem($request->user()->id, $id, $request);

        return new CartItemResource($item);
    }

    public function destroy(Request $request, int $id)
    {
        $this->service->removeItem($request->user()->id, $id);

        return response()->json([
            'message' => 'Item removed.',
        ]);
    }
}
