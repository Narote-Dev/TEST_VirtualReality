<?php

namespace App\Http\Controllers;

use App\Models\Transfer;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransferController extends Controller
{
    public function transfer(Request $request)
    {
        $validated = $request->validate([
            'to_user_id' => 'required|exists:users,user_id',
            'currency_id' => 'required|exists:currencies,currency_id',
            'type' => '|required|string|max:100',
            'amount' => 'required|numeric|min:0.01',
        ]);

        $fromUser = $request->user();

        DB::beginTransaction();

        try {
            $fromWallet = Wallet::where('user_id', $fromUser->user_id)
                                ->where('currency_id', $validated['currency_id'])
                                ->lockForUpdate()
                                ->first();

            $toWallet = Wallet::where('user_id', $validated['to_user_id'])
                              ->where('currency_id', $validated['currency_id'])
                              ->lockForUpdate()
                              ->first();

            if (!$fromWallet || !$toWallet) {
                throw new \Exception("Wallet not found");
            }

            if ($fromWallet->balance < $validated['amount']) {
                throw new \Exception("Insufficient funds");
            }

            // หักเงิน
            $fromWallet->balance -= $validated['amount'];
            $fromWallet->save();

            // เพิ่มเงิน
            $toWallet->balance += $validated['amount'];
            $toWallet->save();

            $feePercent = 0.0025; // 0.25%
            $feeAmount  = $validated['amount'] * $feePercent;

            // บันทึกในตาราง transfer
            $transfer = Transfer::create([
                'from_user_id' => $fromUser->user_id,
                'to_user_id' => $validated['to_user_id'],
                'currency_id' => $validated['currency_id'],
                'amount' => $validated['amount'],
                'fee_amount' => $feeAmount,
                'type' => $validated['type'],
                'status' => 'completed',
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Transfer successful',
                'transfer' => $transfer
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Transfer failed',
                'error' => $e->getMessage()
            ], 400);
        }
    }

}
