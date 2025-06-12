<?php

namespace App\Http\Controllers;

use App\Models\Currency;
use Illuminate\Http\Request;

class CurrencyController extends Controller
{
    public function index()
    {
        return response()->json(Currency::all());
    }

    public function show($id)
    {
        $currency = Currency::find($id);
        if (!$currency) {
            return response()->json(['message' => 'Currency not found'], 404);
        }

        return response()->json($currency);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:10|unique:currencies',
            'name' => 'required|string|max:100',
            'symbol' => 'required|string|max:10',
            'type' => 'required|string|max:10',
            'decimal_places' => 'required|numeric|min:0',
        ]);

        $validated['is_active'] = 1;

        $currency = Currency::create($validated);

        return response()->json($currency, 201);
    }
    

    public function update(Request $request, $id)
    {
        $currency = Currency::find($id);
        if (!$currency) {
            return response()->json(['message' => 'Currency not found'], 404);
        }

        $validated = $request->validate([
            'code' => 'sometimes|required|string|max:10|unique:currencies,code,' . $id . ',currency_id',
            'name' => 'sometimes|required|string|max:100',
        ]);
        $currency->update($validated);

        return response()->json($currency);
    }

    public function destroy($id)
    {
        $currency = Currency::find($id);
        if (!$currency) {
            return response()->json(['message' => 'Currency not found'], 404);
        }

        $currency->delete();

        return response()->json(['message' => 'Currency deleted successfully']);
    }
}
