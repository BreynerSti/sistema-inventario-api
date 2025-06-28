@extends('layouts.app')

@section('title', 'Dashboard - Sistema de Inventario')

@section('content')
    <div class="grid grid-cols-2 gap-8 mb-8 max-w-6xl mx-auto">
        <!-- Total Productos -->
        <div class="bg-blue-100 p-8 rounded-xl shadow-lg hover:shadow-xl transition-shadow duration-300">
            <div class="flex items-center">
                <div class="p-4 rounded-full bg-blue-500 text-white mr-6 flex items-center justify-center">
                    <i class="fas fa-box text-3xl"></i>
                </div>
                <div class="flex-1">
                    <p class="text-blue-600 text-lg font-medium mb-2">Total Productos</p>
                    <p id="total-productos" class="text-4xl font-bold text-gray-900">Cargando...</p>
                </div>
            </div>
        </div>

        <!-- Total Categorías -->
        <div class="bg-green-100 p-8 rounded-xl shadow-lg hover:shadow-xl transition-shadow duration-300">
            <div class="flex items-center">
                <div class="p-4 rounded-full bg-green-500 text-white mr-6 flex items-center justify-center">
                    <i class="fas fa-tags text-3xl"></i>
                </div>
                <div class="flex-1">
                    <p class="text-green-600 text-lg font-medium mb-2">Total Categorías</p>
                    <p id="total-categorias" class="text-4xl font-bold text-gray-900">Cargando...</p>
                </div>
            </div>
        </div>

        <!-- Producto con Menor Stock -->
        <div class="bg-red-100 p-8 rounded-xl shadow-lg hover:shadow-xl transition-shadow duration-300">
            <div class="flex items-center">
                <div class="p-4 rounded-full bg-red-500 text-white mr-6 flex items-center justify-center">
                    <i class="fas fa-exclamation-triangle text-3xl"></i>
                </div>
                <div class="flex-1">
                    <p class="text-red-600 text-lg font-medium mb-2">Producto con Menor Stock</p>
                    <p id="producto-menor-stock" class="text-2xl font-bold text-gray-900 mb-1">Cargando...</p>
                    <p class="text-base text-gray-600">
                        Stock: <span id="stock-mas-bajo" class="font-bold text-red-600">Cargando...</span>
                    </p>
                </div>
            </div>
        </div>

        <!-- Valor Total Inventario -->
        <div class="bg-purple-100 p-8 rounded-xl shadow-lg hover:shadow-xl transition-shadow duration-300">
            <div class="flex items-center">
                <div class="p-4 rounded-full bg-purple-500 text-white mr-6 flex items-center justify-center">
                    <i class="fas fa-dollar-sign text-3xl"></i>
                </div>
                <div class="flex-1">
                    <p class="text-purple-600 text-lg font-medium mb-2">Valor Inventario</p>
                    <p id="valor-inventario" class="text-4xl font-bold text-gray-900">Cargando...</p>
                </div>
            </div>
        </div>
    </div>

    @section('scripts')
        <script>
            async function cargarTotalProductos() {
                try {
                    const response = await fetch('/api/dashboard/metricas');
                    const data = await response.json();

                    if (data.success) {
                        document.getElementById('total-productos').textContent = data.data.totalProductos;
                        document.getElementById('total-categorias').textContent = data.data.totalCategorias;
                        document.getElementById('valor-inventario').textContent = '$' + data.data.valorInventario;
                        
                        // Mostrar producto con menor stock
                        document.getElementById('producto-menor-stock').textContent = data.data.productoMenosStock;
                        document.getElementById('stock-mas-bajo').textContent = data.data.stockMasBajo;
                    } else {
                        document.getElementById('total-productos').textContent = 'Error';
                        document.getElementById('total-categorias').textContent = 'Error';
                        document.getElementById('valor-inventario').textContent = 'Error';
                        document.getElementById('producto-menor-stock').textContent = 'Error';
                        document.getElementById('stock-mas-bajo').textContent = 'Error';
                    }
                } catch (error) {
                    console.error('Error:', error);
                    document.getElementById('total-productos').textContent = 'Error';
                    document.getElementById('total-categorias').textContent = 'Error';
                    document.getElementById('valor-inventario').textContent = 'Error';
                    document.getElementById('producto-menor-stock').textContent = 'Error';
                    document.getElementById('stock-mas-bajo').textContent = 'Error';
                }
            }

            // Ejecutar cuando cargue la página
            document.addEventListener('DOMContentLoaded', cargarTotalProductos);
        </script>
    @endsection
@endsection