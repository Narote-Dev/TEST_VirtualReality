<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index()
    {
        // ดึงรายการธุรกรรมทั้งหมดพร้อม user, currency
        $transactions = Transaction::with(['buyer', 'seller', 'currency'])->get();

        return response()->json($transactions);
    }

    public function show($id)
    {
        $transaction = Transaction::with(['buyer', 'seller', 'currency'])->find($id);

        if (!$transaction) {
            return response()->json(['message' => 'Transaction not found'], 404);
        }

        return response()->json($transaction);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'order_id' => 'nullable|integer',
            'buyer_id' => 'required|exists:users,id',
            'seller_id' => 'required|exists:users,id',
            'currency_id' => 'required|exists:currencies,id',
            'amount' => 'required|numeric|min:0',
            'price' => 'required|numeric|min:0',
            'fee_amount' => 'nullable|numeric|min:0',
            'status' => 'required|string',
        ]);

        $validated['total_amount'] = $validated['amount'] * $validated['price'];

        $transaction = Transaction::create($validated);

        return response()->json($transaction, 201);
    }
}
