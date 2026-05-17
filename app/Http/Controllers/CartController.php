<?php

namespace App\Http\Controllers;

use App\Http\Requests\Cart\StoreCartItemRequest;
use App\Http\Requests\Cart\UpdateCartItemRequest;
use App\Http\Resources\CartItemResource;
use App\Http\Resources\CartResource;
use App\Services\CartService;
use DomainException;
use Illuminate\Http\Request;
use Throwable;

class CartController extends Controller
{
    public function __construct(protected CartService $service) {}
    public function index(Request $request)
    {
        try {
            $cart = $this->service->getOrCreateCart($request->user()->id, true);

            return new CartResource($cart);
        } catch (DomainException $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 422);
        } catch (Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unable to fetch cart.',
            ], 500);
        }
    }

    public function store(StoreCartItemRequest $request)
    {
        try {
            $item = $this->service->addItem($request->user()->id, $request);

            return (new CartItemResource($item))->response()->setStatusCode(201);
        } catch (DomainException $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 422);
        } catch (Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unable to add item.',
            ], 500);
        }
    }

    public function update(UpdateCartItemRequest $request, int $id)
    {
        try {
            $item = $this->service->updateItem($request->user()->id, $id, $request);

            return new CartItemResource($item);
        } catch (DomainException $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 422);
        } catch (Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unable to update item.',
            ], 500);
        }
    }

    public function destroy(Request $request, int $id)
    {
        try {
            $this->service->removeItem($request->user()->id, $id);

            return response()->json([
                'message' => 'Item removed.',
            ]);
        } catch (DomainException $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 422);
        } catch (Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unable to remove item.',
            ], 500);
        }
    }
}
