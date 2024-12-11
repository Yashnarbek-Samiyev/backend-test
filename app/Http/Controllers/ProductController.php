<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ProductController extends Controller
{
    public function calculateMaterials(Request $request)
    {
        try {
            $request = request()->validate([
                'products' => 'required|array',
                'products.*.id' => 'required|exists:products,id',
                'products.*.quantity' => 'required|numeric|min:1',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'error' => 'Validation failed',
                'messages' => $e->errors()
            ], 422);
        }

        $result = [];
        $usedWarehouses = [];

        foreach ($request['products'] as $productRequest) {
            $product = Product::with('materials')->find($productRequest['id']);
            $productMaterials = [];

            foreach ($product->materials as $material) {
                $neededQuantity = $material->pivot->quantity * $productRequest['quantity'];
                $remainingQuantity = $neededQuantity;
                $materialWarehouses = [];

                $warehouses = Warehouse::where('material_id', $material->id)
                    //->whereNotIn('id', $usedWarehouses)
                    ->where('remainder', '>', 0)
                    ->orderBy('price')
                    ->get();


                foreach ($warehouses as $warehouse) {
                    if ($remainingQuantity <= 0) break;

                    $usedQty = $usedQuantities[$warehouse->id] ?? 0;
                    $availableQty = $warehouse->remainder - $usedQty;

                    if ($availableQty <= 0) continue;

                    $usableQuantity = min($availableQty, $remainingQuantity);
                    $remainingQuantity -= $usableQuantity;

                    $usedQuantities[$warehouse->id] = ($usedQuantities[$warehouse->id] ?? 0) + $usableQuantity;

                    $materialWarehouses[] = [
                        'warehouse_id' => $warehouse->id,
                        'material_name' => $material->name,
                        'qty' => (float)$usableQuantity,
                        'price' => (float)$warehouse->price,
                    ];

                }

                if ($remainingQuantity > 0) {
                    $materialWarehouses[] = [
                        'warehouse_id' => null,
                        'material_name' => $material->name,
                        'qty' => (int)$remainingQuantity,
                        'price' => null,
                    ];
                }

                $productMaterials = array_merge($productMaterials, $materialWarehouses);
            }

            $result[] = [
                'product_name' => $product->name,
                'product_qty' => (int)$productRequest['quantity'],
                'product_materials' => $productMaterials,
            ];
        }

        return response()->json(['result' => $result]);
    }
}
