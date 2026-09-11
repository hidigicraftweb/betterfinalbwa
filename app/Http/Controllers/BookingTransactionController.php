<?php

namespace App\Http\Controllers;

use App\Http\Resources\TransactionResource;
use App\Services\BookingTransactionService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class BookingTransactionController extends Controller
{
    private $bookingTransactionService;

    // Constructor injection of the BookingTransactionService
    public function __construct(BookingTransactionService $bookingTransactionService)
    {
        $this->bookingTransactionService = $bookingTransactionService;
    }

    // Method to get all booking transactions
    public function index()
    {
        $transactions = $this->bookingTransactionService->getAll();
        return response()->json(TransactionResource::collection($transactions));
    }

    // Method to get a booking transaction by ID for manager
    public function show(int $id)
    {
        try {
            $transaction = $this->bookingTransactionService->getByIdForManager($id);
            return response()->json(new TransactionResource($transaction));
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Booking transaction not found'], 404);
        }
    }

    public function updateStatus(Request $request, int $id)
    {
        $validated = $request->validate([
            'status' => 'required|in:Approved,Rejected',
        ]);

        try {
            $transaction = $this->bookingTransactionService->updateStatus($id, $validated['status']);
            return response()->json([
                'message' => 'Transaction status updated successfully', 
                'data' => new TransactionResource($transaction)
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Transaction not found'], 404);
        }
    }
}
