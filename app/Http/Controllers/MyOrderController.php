<?php

namespace App\Http\Controllers;

use App\Http\Requests\BookingTransactionRequest;
use App\Http\Resources\TransactionResource;
use App\Services\BookingTransactionService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MyOrderController extends Controller
{
    private $bookingTransactionService;

    // Constructor injection of the BookingTransactionService
    public function __construct(BookingTransactionService $bookingTransactionService)
    {
        $this->bookingTransactionService = $bookingTransactionService;
    }

    // Method to get all orders for the authenticated user
    public function index()
    {
        $user = Auth::user();
        $userId = $user->id;
        $orders = $this->bookingTransactionService->getAllForUser($userId);
        return response()->json(TransactionResource::collection($orders));
    }

    // Method to get a specific order by ID for the authenticated user
    public function show(int $id)
    {
        $user = Auth::user();
        $userId = $user->id;

        try {
            $order = $this->bookingTransactionService->getById($id, $userId);
            return response()->json(new TransactionResource($order));
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Order not found'], 404);
        }
    }

    // Method to create a new order for the authenticated user
    public function store(BookingTransactionRequest $request)
    {
        $transaction = $this->bookingTransactionService->create($request->validated());
        $transaction = $this->bookingTransactionService->getById($transaction->id, Auth::id());
        return response()->json(new TransactionResource($transaction), 201);
    }
}
