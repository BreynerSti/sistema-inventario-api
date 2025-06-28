@extends('layouts.app')

@section('content')
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Gestión de Categorías</h1>
    </div>

    <!-- Formulario para agregar categoría -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h2 class="text-lg font-semibold mb-4">Nueva Categoría</h2>
        <form id="categoryForm">
            <div class="flex gap-4">
                <input type="text" id="categoryName" placeholder="Nombre de la categoría"
                    class="border rounded px-3 py-2 flex-1" required>
                <button type="submit" class="bg-green-500 text-white px-6 py-2 rounded hover:bg-green-600">
                    Guardar
                </button>
            </div>
        </form>
    </div>

    <!-- Lista de categorías existentes -->
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold mb-4">Categorías Existentes</h2>
        <div id="categoriesList" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <!-- Las categorías se cargarán aquí con JavaScript -->
        </div>
    </div>

    <!-- Modal para editar categoría -->
    <div id="editModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center">
        <div class="bg-white rounded-lg p-6 w-96">
            <h3 class="text-lg font-semibold mb-4">Editar Categoría</h3>
            <form id="editForm">
                <input type="hidden" id="editCategoryId">
                <input type="text" id="editCategoryName" placeholder="Nombre de la categoría"
                    class="border rounded px-3 py-2 w-full mb-4" required>
                <div class="flex gap-4">
                    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                        Actualizar
                    </button>
                    <button type="button" onclick="closeEditModal()"
                        class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
                        Cancelar
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        // Variables globales
        let categories = [];

        // Cargar categorías al iniciar la página
        document.addEventListener('DOMContentLoaded', function() {
            loadCategories();
            setupEventListeners();
        });

        // Configurar event listeners
        function setupEventListeners() {
            // Formulario para nueva categoría
            document.getElementById('categoryForm').addEventListener('submit', function(e) {
                e.preventDefault();
                saveCategory();
            });

            // Formulario para editar categoría
            document.getElementById('editForm').addEventListener('submit', function(e) {
                e.preventDefault();
                updateCategory();
            });

            // Cerrar modal al hacer clic fuera de él
            document.getElementById('editModal').addEventListener('click', function(e) {
                if (e.target === this) {
                    closeEditModal();
                }
            });
        }

        // Cargar categorías desde la API
        async function loadCategories() {
            try {
                const response = await fetch('/api/categories');
                if (response.ok) {
                    categories = await response.json();
                    renderCategories();
                } else {
                    showMessage('Error al cargar las categorías', 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                showMessage('Error de conexión', 'error');
            }
        }

        // Renderizar categorías en el DOM
        function renderCategories() {
            const categoriesList = document.getElementById('categoriesList');

            if (categories.length === 0) {
                categoriesList.innerHTML =
                    '<p class="text-gray-500 col-span-full text-center py-8">No hay categorías registradas</p>';
                return;
            }

            categoriesList.innerHTML = categories.map(category => `
        <div class="border rounded-lg p-4 bg-gray-50 hover:bg-gray-100 transition-colors">
            <div class="flex justify-between items-center">
                <div>
                    <h3 class="font-semibold text-gray-800">${escapeHtml(category.name)}</h3>
                    <p class="text-sm text-gray-500">ID: ${category.id}</p>
                </div>
                <div class="flex gap-2">
                    <button onclick="editCategory(${category.id}, '${escapeHtml(category.name)}')" 
                            class="bg-blue-500 text-white px-3 py-1 rounded text-sm hover:bg-blue-600">
                        Editar
                    </button>
                    <button onclick="deleteCategory(${category.id})" 
                            class="bg-red-500 text-white px-3 py-1 rounded text-sm hover:bg-red-600">
                        Eliminar
                    </button>
                </div>
            </div>
        </div>
    `).join('');
        }

        // Guardar nueva categoría
        async function saveCategory() {
            const categoryName = document.getElementById('categoryName').value.trim();

            if (!categoryName) {
                showMessage('Por favor ingresa un nombre para la categoría', 'error');
                return;
            }

            try {
                const response = await fetch('/api/categories', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                            'content')
                    },
                    body: JSON.stringify({
                        name: categoryName
                    })
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    showMessage(data.message, 'success');
                    document.getElementById('categoryName').value = '';
                    loadCategories(); // Recargar la lista
                } else {
                    const errorMessage = data.message || 'Error al guardar la categoría';
                    showMessage(errorMessage, 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                showMessage('Error de conexión al guardar', 'error');
            }
        }

        // Abrir modal de edición
        function editCategory(id, name) {
            document.getElementById('editCategoryId').value = id;
            document.getElementById('editCategoryName').value = name;
            document.getElementById('editModal').classList.remove('hidden');
        }

        // Cerrar modal de edición
        function closeEditModal() {
            document.getElementById('editModal').classList.add('hidden');
            document.getElementById('editForm').reset();
        }

        // Actualizar categoría
        async function updateCategory() {
            const categoryId = document.getElementById('editCategoryId').value;
            const categoryName = document.getElementById('editCategoryName').value.trim();

            if (!categoryName) {
                showMessage('Por favor ingresa un nombre para la categoría', 'error');
                return;
            }

            try {
                const response = await fetch(`/api/categories/${categoryId}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                            'content')
                    },
                    body: JSON.stringify({
                        name: categoryName
                    })
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    showMessage(data.message, 'success');
                    closeEditModal();
                    loadCategories(); // Recargar la lista
                } else {
                    const errorMessage = data.message || 'Error al actualizar la categoría';
                    showMessage(errorMessage, 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                showMessage('Error de conexión al actualizar', 'error');
            }
        }

        // Eliminar categoría
        async function deleteCategory(id) {
            if (!confirm('¿Estás seguro de que quieres eliminar esta categoría?')) {
                return;
            }

            try {
                const response = await fetch(`/api/categories/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                            'content')
                    }
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    showMessage(data.message, 'success');
                    loadCategories(); // Recargar la lista
                } else {
                    const errorMessage = data.message || 'Error al eliminar la categoría';
                    showMessage(errorMessage, 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                showMessage('Error de conexión al eliminar', 'error');
            }
        }

        // Mostrar mensajes al usuario
        function showMessage(message, type = 'info') {
            // Crear el elemento del mensaje
            const messageDiv = document.createElement('div');
            messageDiv.className = `fixed bottom-4 right-4 px-6 py-3 rounded-lg shadow-lg z-50 ${
        type === 'success' ? 'bg-green-500 text-white' :
        type === 'error' ? 'bg-red-500 text-white' :
        'bg-blue-500 text-white'
    }`;
            messageDiv.textContent = message;

            // Agregar al DOM
            document.body.appendChild(messageDiv);

            // Remover después de 3 segundos
            setTimeout(() => {
                messageDiv.remove();
            }, 3000);
        }

        // Función auxiliar para escapar HTML
        function escapeHtml(text) {
            const map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return text.replace(/[&<>"']/g, function(m) {
                return map[m];
            });
        }
    </script>
@endsection
