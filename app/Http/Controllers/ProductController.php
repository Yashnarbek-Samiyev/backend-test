<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Warehouse;

class ProductController extends Controller
{
    public function calculateMaterials(Request $request)
    {
        $products = $request->input('products');
        $result = [];
        $allocatedMaterials = [];

        foreach ($products as $productData) {
            $product = Product::with('materials')->findOrFail($productData['product_id']);
            $quantity = $productData['quantity'];
            $materials = $product->materials;

            $productResult = [
                'product_name' => $product->name,
                'product_qty' => $quantity,
                'product_materials' => []
            ];

            foreach ($materials as $material) {
                $neededQty = $material->pivot->quantity * $quantity;
                if ($product->name == 'Koylak') {
                    if (in_array($material->name, ['Mato', 'Tugma', 'Ip'])) {
                        $neededQty = $this->calculateKoylakMaterials($quantity, $material);
                    } else {
                        continue;
                    }
                }
                if ($product->name == 'Shim') {
                    if (in_array($material->name, ['Mato', 'Ip', 'Zamok'])) {
                        $neededQty = $this->calculateShimMaterials($quantity, $material);
                    } else {
                        continue;
                    }
                }
                $warehouseData = $this->getMaterialFromWarehouses($material, $neededQty, $allocatedMaterials);
                $productResult['product_materials'] = array_merge($productResult['product_materials'], $warehouseData);
            }
            $result[] = $productResult;
        }
        return response()->json(['result' => $result]);
    }
    private function calculateKoylakMaterials($quantity, $material)
    {
        if ($material->name == 'Mato') {
            return 0.8 * $quantity;
        }
        if ($material->name == 'Tugma') {
            return 5 * $quantity;
        }
        if ($material->name == 'Ip') {
            return 10 * $quantity;
        }
        return 0;
    }
    private function calculateShimMaterials($quantity, $material)
    {
        if ($material->name == 'Mato') {
            return 1.4 * $quantity;
        }
        if ($material->name == 'Ip') {
            return 15 * $quantity;
        }
        if ($material->name == 'Zamok') {
            return 1 * $quantity;
        }
        return 0;
    }
    private function getMaterialFromWarehouses($material, $neededQty, &$allocatedMaterials)
    {
        $warehouses = Warehouse::where('material_id', $material->id)
            ->orderBy('price')
            ->get();
        $warehouseData = [];
        $remainingQty = $neededQty;

        foreach ($warehouses as $warehouse) {
            if ($remainingQty <= 0) {
                break;
            }
            $remainingWarehouseQty = $warehouse->remainder;
            $allocatedQty = $allocatedMaterials[$material->id][$warehouse->id] ?? 0;
            $availableQty = $remainingWarehouseQty - $allocatedQty;

            if ($availableQty <= 0) {
                continue;
            }

            $qtyToTake = min($availableQty, $remainingQty);
            $remainingQty -= $qtyToTake;

            $warehouseData[] = [
                'warehouse_id' => $warehouse->id,
                'material_name' => $material->name,
                'qty' => $qtyToTake,
                'price' => $warehouse->price
            ];

            if (!isset($allocatedMaterials[$material->id])) {
                $allocatedMaterials[$material->id] = [];
            }
            $allocatedMaterials[$material->id][$warehouse->id] = ($allocatedMaterials[$material->id][$warehouse->id] ?? 0) + $qtyToTake;
        }

        if ($remainingQty > 0) {
            $warehouseData[] = [
                'warehouse_id' => null,
                'material_name' => $material->name,
                'qty' => $remainingQty,
                'price' => null
            ];
        }
        return $warehouseData;
        }
    }
