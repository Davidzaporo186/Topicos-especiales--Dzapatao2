<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductApiController extends Controller
{
    public function index(): JsonResponse
    {
        $products = Product::all();

        return response()->json($products, 200);
    }

    public function show(string $id): JsonResponse
    {
        $product = Product::findOrFail($id);

        return response()->json($product, 200);
    }

    /**
     * Actividad: crear un producto nuevo vía POST.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string',
            'price' => 'required|numeric',
        ]);

        $product = Product::create([
            'name' => $request->name,
            'description' => $request->description ?? '',
            'price' => $request->price,
        ]);

        return response()->json($product, 201);
    }
}
