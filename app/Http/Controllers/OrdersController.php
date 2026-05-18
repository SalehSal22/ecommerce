<?php

namespace App\Http\Controllers;

use App\Http\Resources\OrderResource;
use App\Services\OrderService;
use DomainException;
use Illuminate\Http\Request;
use Throwable;

class OrdersController extends Controller
{
    public function __construct(protected OrderService $service) {}

    public function index(Request $request)
    {
        try {
            $orders = $this->service->listForUser($request->user()->id);

            return OrderResource::collection($orders);
        } catch (DomainException $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 422);
        } catch (Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unable to list orders.',
            ], 500);
        }
    }

    public function show(Request $request, int $id)
    {
        try {
            $order = $this->service->getForUser($request->user()->id, $id);

            return new OrderResource($order);
        } catch (DomainException $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 422);
        } catch (Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unable to fetch order.',
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $order = $this->service->placeOrder($request->user()->id);

            return (new OrderResource($order))->response()->setStatusCode(201);
        } catch (DomainException $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 422);
        }
    }
}
