<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Wallet;
use App\Models\Currency;

class WalletController extends Controller
{
    /**
     * Get all user wallets
     */
    public function index(Request $request)
    {
        try {
            $wallets = $request->user()->wallets()
                ->with('currency')
                ->get()
                ->map(function ($wallet) {
                    return [
                        'id' => $wallet->id,
                        'currency' => $wallet->currency,
                        'balance' => $wallet->balance,
                        'frozen_balance' => $wallet->frozen_balance,
                        'available_balance' => $wallet->available_balance,
                        'wallet_address' => $wallet->wallet_address,
                        'created_at' => $wallet->created_at,
                        'updated_at' => $wallet->updated_at,
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => ['wallets' => $wallets]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch wallets',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get specific wallet by currency
     */
    public function show(Request $request, $currencyId)
    {
        try {
            $wallet = $request->user()->wallets()
                ->with('currency')
                ->where('currency_id', $currencyId)
                ->first();

            if (!$wallet) {
                return response()->json([
                    'success' => false,
                    'message' => 'Wallet not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'wallet' => [
                        'id' => $wallet->id,
                        'currency' => $wallet->currency,
                        'balance' => $wallet->balance,
                        'frozen_balance' => $wallet->frozen_balance,
                        'available_balance' => $wallet->available_balance,
                        'wallet_address' => $wallet->wallet_address,
                        'created_at' => $wallet->created_at,
                        'updated_at' => $wallet->updated_at,
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch wallet',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create wallet for a currency
     */
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'currency_id' => 'required|exists:currencies,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Check if wallet already exists
            $existingWallet = $request->user()->wallets()
                ->where('currency_id', $request->currency_id)
                ->first();

            if ($existingWallet) {
                return response()->json([
                    'success' => false,
                    'message' => 'Wallet already exists for this currency'
                ], 400);
            }

            $currency = Currency::findOrFail($request->currency_id);

            $wallet = Wallet::create([
                'user_id' => $request->user()->id,
                'currency_id' => $request->currency_id,
                'balance' => 0,
                'frozen_balance' => 0,
                'wallet_address' => $currency->isCrypto() ? $this->generateWalletAddress($currency->code) : null,
            ]);

            $wallet->load('currency');

            return response()->json([
                'success' => true,
                'message' => 'Wallet created successfully',
                'data' => ['wallet' => $wallet]
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create wallet',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get wallet balance
     */
    public function balance(Request $request, $currencyId)
    {
        try {
            $wallet = $request->user()->wallets()
                ->with('currency')
                ->where('currency_id', $currencyId)
                ->first();

            if (!$wallet) {
                return response()->json([
                    'success' => false,
                    'message' => 'Wallet not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'currency' => $wallet->currency,
                    'balance' => $wallet->balance,
                    'frozen_balance' => $wallet->frozen_balance,
                    'available_balance' => $wallet->available_balance,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch balance',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get wallet transaction history
     */
    public function history(Request $request, $currencyId)
    {
        try {
            $wallet = $request->user()->wallets()
                ->where('currency_id', $currencyId)
                ->first();

            if (!$wallet) {
                return response()->json([
                    'success' => false,
                    'message' => 'Wallet not found'
                ], 404);
            }

            // Get transactions related to this wallet
            $transactions = $request->user()->transactions()
                ->with(['currency', 'buyer', 'seller', 'order'])
                ->where('currency_id', $currencyId)
                ->orderBy('created_at', 'desc')
                ->paginate(20);

            // Get transfers related to this wallet
            $transfers = $request->user()->sentTransfers()
                ->with(['currency', 'fromUser', 'toUser'])
                ->where('currency_id', $currencyId)
                ->union(
                    $request->user()->receivedTransfers()
                        ->with(['currency', 'fromUser', 'toUser'])
                        ->where('currency_id', $currencyId)
                )
                ->orderBy('created_at', 'desc')
                ->paginate(20);

            return response()->json([
                'success' => true,
                'data' => [
                    'transactions' => $transactions,
                    'transfers' => $transfers,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch wallet history',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate wallet address for crypto currencies
     */
    private function generateWalletAddress($currencyCode)
    {
        $prefixes = [
            'BTC' => ['1', '3', 'bc1'],
            'ETH' => ['0x'],
            'XRP' => ['r'],
            'DOGE' => ['D'],
        ];

        $prefix = $prefixes[$currencyCode][0] ?? '';
        $randomString = substr(md5(uniqid() . time()), 0, 30);
        
        return $prefix . $randomString;
    }
}