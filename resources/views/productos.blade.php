@extends('layouts.app')

@section('title', 'Productos - Sistema de Inventario')

@section('content')
    <div class="mb-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Gestión de Productos</h1>
            <button id="toggleFormBtn" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                ➕ Nuevo Producto
            </button>
        </div>

        <!-- FORMULARIO (inicialmente oculto) -->
        <div id="productFormContainer" class="hidden mb-8 p-6 bg-gray-50 rounded-lg border">
            <h2 id="formTitle" class="text-xl font-semibold mb-4">Crear Nuevo Producto</h2>
            
            <form id="productForm">
                <input type="hidden" id="productId" value="">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Nombre -->
                    <div>
                        <label class="block text-sm font-medium mb-1">Nombre del Producto</label>
                        <input type="text" id="productName" class="border rounded px-3 py-2 w-full focus:ring-2 focus:ring-blue-500" required>
                    </div>

                    <!-- Categoría -->
                    <div>
                        <label class="block text-sm font-medium mb-1">Categoría</label>
                        <select id="productCategory" class="border rounded px-3 py-2 w-full focus:ring-2 focus:ring-blue-500" required>
                            <option value="">Selecciona una categoría</option>
                        </select>
                    </div>

                    <!-- Precio -->
                    <div>
                        <label class="block text-sm font-medium mb-1">Precio</label>
                        <input type="number" step="0.01" min="0" id="productPrice" class="border rounded px-3 py-2 w-full focus:ring-2 focus:ring-blue-500" required>
                    </div>

                    <!-- Stock -->
                    <div>
                        <label class="block text-sm font-medium mb-1">Stock</label>
                        <input type="number" min="0" id="productStock" class="border rounded px-3 py-2 w-full focus:ring-2 focus:ring-blue-500" required>
                    </div>

                    <!-- Descripción -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium mb-1">Descripción</label>
                        <textarea id="productDescription" rows="3" class="border rounded px-3 py-2 w-full focus:ring-2 focus:ring-blue-500"></textarea>
                    </div>
                </div>

                <div class="flex gap-3 mt-4">
                    <button type="submit" id="submitBtn" class="bg-green-500 text-white px-6 py-2 rounded hover:bg-green-600">
                        💾 Guardar Producto
                    </button>
                    <button type="button" id="cancelBtn" class="bg-gray-500 text-white px-6 py-2 rounded hover:bg-gray-600">
                        ❌ Cancelar
                    </button>
                </div>
            </form>
        </div>

        <!-- BARRA DE BÚSQUEDA -->
        <div class="mb-6">
            <div class="flex gap-4 items-center">
                <div class="flex-1">
                    <input type="text" id="searchInput" placeholder="🔍 Buscar productos..." 
                           class="border rounded px-4 py-2 w-full focus:ring-2 focus:ring-blue-500">
                </div>
                <button id="refreshBtn" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
                    🔄 Actualizar
                </button>
            </div>
        </div>

        <!-- TABLA DE PRODUCTOS -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full border-collapse">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="border-b border-gray-300 px-4 py-3 text-left font-semibold">ID</th>
                            <th class="border-b border-gray-300 px-4 py-3 text-left font-semibold">Nombre</th>
                            <th class="border-b border-gray-300 px-4 py-3 text-left font-semibold">Categoría</th>
                            <th class="border-b border-gray-300 px-4 py-3 text-left font-semibold">Precio</th>
                            <th class="border-b border-gray-300 px-4 py-3 text-left font-semibold">Stock</th>
                            <th class="border-b border-gray-300 px-4 py-3 text-left font-semibold">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="productsTableBody">
                        <!-- Los productos se cargarán aquí dinámicamente -->
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                                <div class="flex flex-col items-center">
                                    <div class="animate-spin w-6 h-6 border-2 border-blue-500 border-t-transparent rounded-full mb-2"></div>
                                    Cargando productos...
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- MODAL DE CONFIRMACIÓN -->
    <div id="confirmModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white p-6 rounded-lg max-w-md w-full mx-4">
            <h3 class="text-lg font-semibold mb-4">Confirmar Eliminación</h3>
            <p class="text-gray-600 mb-6">¿Estás seguro de que deseas eliminar este producto? Esta acción no se puede deshacer.</p>
            <div class="flex gap-3 justify-end">
                <button id="cancelDelete" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
                    Cancelar
                </button>
                <button id="confirmDelete" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">
                    Eliminar
                </button>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    // Variables globales
    let products = [];
    let categories = [];
    let editingProductId = null;
    let deleteProductId = null;

    // Inicializar cuando carga la página
    document.addEventListener('DOMContentLoaded', function() {
        loadCategories();
        loadProducts();
        setupEventListeners();
    });

    // Configurar event listeners
    function setupEventListeners() {
        document.getElementById('toggleFormBtn').addEventListener('click', toggleForm);
        document.getElementById('productForm').addEventListener('submit', handleProductSubmit);
        document.getElementById('cancelBtn').addEventListener('click', cancelForm);
        document.getElementById('refreshBtn').addEventListener('click', loadProducts);
        document.getElementById('searchInput').addEventListener('input', filterProducts);
        
        // Modal
        document.getElementById('cancelDelete').addEventListener('click', closeModal);
        document.getElementById('confirmDelete').addEventListener('click', deleteProduct);
    }

    // Cargar categorías
    async function loadCategories() {
        try {
            const response = await fetch('/api/categories');
            categories = await response.json();
            
            const select = document.getElementById('productCategory');
            select.innerHTML = '<option value="">Selecciona una categoría</option>';
            
            categories.forEach(category => {
                const option = document.createElement('option');
                option.value = category.id;
                option.textContent = category.name;
                select.appendChild(option);
            });
        } catch (error) {
            console.error('Error loading categories:', error);
            showNotification('Error al cargar categorías', 'error');
        }
    }

    // Cargar productos
    async function loadProducts() {
        try {
            const response = await fetch('/api/products');
            products = await response.json();
            renderProducts(products);
        } catch (error) {
            console.error('Error loading products:', error);
            showNotification('Error al cargar productos', 'error');
        }
    }

    // Renderizar productos en la tabla
    function renderProducts(productsToRender) {
        const tbody = document.getElementById('productsTableBody');
        
        if (productsToRender.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                        📦 No hay productos registrados
                    </td>
                </tr>
            `;
            return;
        }

        tbody.innerHTML = productsToRender.map(product => {
            const category = categories.find(cat => cat.id === product.category_id);
            return `
                <tr class="hover:bg-gray-50 border-b border-gray-200">
                    <td class="px-4 py-3">${product.id}</td>
                    <td class="px-4 py-3 font-medium">${product.name}</td>
                    <td class="px-4 py-3">${category ? category.name : 'Sin categoría'}</td>
                    <td class="px-4 py-3">$${parseFloat(product.price).toFixed(2)}</td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-1 rounded text-sm ${product.stock > 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}">
                            ${product.stock}
                        </span>
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex gap-2">
                            <button onclick="editProduct(${product.id})" 
                                    class="bg-blue-500 text-white px-3 py-1 rounded text-sm hover:bg-blue-600">
                                ✏️ Editar
                            </button>
                            <button onclick="showDeleteModal(${product.id})" 
                                    class="bg-red-500 text-white px-3 py-1 rounded text-sm hover:bg-red-600">
                                🗑️ Eliminar
                            </button>
                        </div>
                    </td>
                </tr>
            `;
        }).join('');
    }

    // Alternar formulario
    function toggleForm() {
        const container = document.getElementById('productFormContainer');
        const btn = document.getElementById('toggleFormBtn');
        
        if (container.classList.contains('hidden')) {
            container.classList.remove('hidden');
            btn.textContent = '❌ Cerrar Formulario';
            btn.className = 'bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600';
        } else {
            cancelForm();
        }
    }

    // Cancelar formulario
    function cancelForm() {
        const container = document.getElementById('productFormContainer');
        const btn = document.getElementById('toggleFormBtn');
        
        container.classList.add('hidden');
        btn.textContent = '➕ Nuevo Producto';
        btn.className = 'bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600';
        
        // Limpiar formulario
        document.getElementById('productForm').reset();
        document.getElementById('productId').value = '';
        document.getElementById('formTitle').textContent = 'Crear Nuevo Producto';
        document.getElementById('submitBtn').textContent = '💾 Guardar Producto';
        editingProductId = null;
    }

    // Manejar envío del formulario
    async function handleProductSubmit(e) {
        e.preventDefault();
        
        const productData = {
            name: document.getElementById('productName').value,
            category_id: document.getElementById('productCategory').value,
            price: document.getElementById('productPrice').value,
            stock: document.getElementById('productStock').value,
            description: document.getElementById('productDescription').value
        };

        try {
            let response;
            if (editingProductId) {
                // Actualizar producto existente
                response = await fetch(`/api/products/${editingProductId}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(productData)
                });
            } else {
                // Crear nuevo producto
                response = await fetch('/api/products', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(productData)
                });
            }

            if (response.ok) {
                showNotification(editingProductId ? 'Producto actualizado exitosamente!' : 'Producto guardado exitosamente!', 'success');
                cancelForm();
                loadProducts();
            } else {
                showNotification('Error al guardar el producto', 'error');
            }
        } catch (error) {
            console.error('Error:', error);
            showNotification('Error de conexión', 'error');
        }
    }

    // Editar producto
    function editProduct(id) {
        const product = products.find(p => p.id === id);
        if (!product) return;

        editingProductId = id;
        
        // Llenar formulario con datos del producto
        document.getElementById('productId').value = product.id;
        document.getElementById('productName').value = product.name;
        document.getElementById('productCategory').value = product.category_id;
        document.getElementById('productPrice').value = product.price;
        document.getElementById('productStock').value = product.stock;
        document.getElementById('productDescription').value = product.description || '';
        
        // Cambiar título y botón
        document.getElementById('formTitle').textContent = 'Editar Producto';
        document.getElementById('submitBtn').textContent = '💾 Actualizar Producto';
        
        // Mostrar formulario
        const container = document.getElementById('productFormContainer');
        const btn = document.getElementById('toggleFormBtn');
        
        container.classList.remove('hidden');
        btn.textContent = '❌ Cerrar Formulario';
        btn.className = 'bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600';
    }

    // Mostrar modal de eliminación
    function showDeleteModal(id) {
        deleteProductId = id;
        document.getElementById('confirmModal').classList.remove('hidden');
    }

    // Cerrar modal
    function closeModal() {
        document.getElementById('confirmModal').classList.add('hidden');
        deleteProductId = null;
    }

    // Eliminar producto
    async function deleteProduct() {
        if (!deleteProductId) return;

        try {
            const response = await fetch(`/api/products/${deleteProductId}`, {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json'
                }
            });

            if (response.ok) {
                showNotification('Producto eliminado exitosamente!', 'success');
                loadProducts();
            } else {
                showNotification('Error al eliminar el producto', 'error');
            }
        } catch (error) {
            console.error('Error:', error);
            showNotification('Error de conexión', 'error');
        } finally {
            closeModal();
        }
    }

    // Filtrar productos
    function filterProducts() {
        const searchTerm = document.getElementById('searchInput').value.toLowerCase();
        const filteredProducts = products.filter(product => 
            product.name.toLowerCase().includes(searchTerm) ||
            product.description?.toLowerCase().includes(searchTerm)
        );
        renderProducts(filteredProducts);
    }

    // Mostrar notificaciones
    function showNotification(message, type = 'info') {
        // Crear notificación temporal
        const notification = document.createElement('div');
        notification.className = `fixed bottom-4 right-4 px-6 py-3 rounded-lg text-white z-50 ${
            type === 'success' ? 'bg-green-500' : type === 'error' ? 'bg-red-500' : 'bg-blue-500'
        }`;
        notification.textContent = message;
        
        document.body.appendChild(notification);
        
        // Remover después de 3 segundos
        setTimeout(() => {
            notification.remove();
        }, 3000);
    }
</script>
@endsection