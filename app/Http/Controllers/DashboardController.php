<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    // Método para obtener todas las métricas de una vez
    public function getMetricas()
    {
        $totalProductos = Product::count();
        $totalCategorias = Category::count();
        
        // Corregido: comparar stock con stock_minimo como columnas
        $stockBajo = Product::where('stock', '<=', 5)->count();
        
        $valorInventario = Product::sum(DB::raw('price * stock'));
        
        // Obtener el producto con menor stock
        $productoMenosStock = Product::orderBy('stock', 'asc')->first();
        $stockMasBajo = $productoMenosStock ? $productoMenosStock->stock : 0;

        return response()->json([
            'success' => true,
            'data' => [
                'totalProductos' => $totalProductos,
                'totalCategorias' => $totalCategorias,
                'stockBajo' => $stockBajo,
                'valorInventario' => number_format($valorInventario, 2),
                'stockMasBajo' => $stockMasBajo,
                'productoMenosStock' => $productoMenosStock ? $productoMenosStock->name : 'N/A'
            ]
        ]);
    }

    // Métodos individuales
    public function getTotalProductos()
    {
        $total = Product::count();
        
        return response()->json([
            'success' => true,
            'total' => $total
        ]);
    }

    public function getTotalCategorias()
    {
        $total = Category::count();
        
        return response()->json([
            'success' => true,
            'total' => $total
        ]);
    }

    public function getStockBajo()
    {
        $stockBajo = Product::whereRaw('stock <= stock_minimo')->count();
        
        return response()->json([
            'success' => true,
            'stockBajo' => $stockBajo
        ]);
    }

    public function getProductoMenorStock()
    {
        $productoMenosStock = Product::orderBy('stock', 'asc')->first();
        
        return response()->json([
            'success' => true,
            'data' => [
                'producto' => $productoMenosStock ? $productoMenosStock->name : 'N/A',
                'stock' => $productoMenosStock ? $productoMenosStock->stock : 0
            ]
        ]);
    }
}