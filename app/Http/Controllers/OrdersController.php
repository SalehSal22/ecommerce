<?php

namespace App\Http\Controllers;

use App\Http\Resources\OrderResource;
use App\Services\OrderService;
use Illuminate\Http\Request;

class OrdersController extends Controller
{
    public function __construct(protected OrderService $service) {}

    public function index(Request $request)
    {
        $orders = $this->service->listForUser($request->user()->id);

        return OrderResource::collection($orders);
    }

    public function show(Request $request, int $id)
    {
        $order = $this->service->getForUser($request->user()->id, $id);

        return new OrderResource($order);
    }

    public function store(Request $request)
    {
        $order = $this->service->placeOrder($request->user()->id);

        return (new OrderResource($order))->response()->setStatusCode(201);
    }
}
