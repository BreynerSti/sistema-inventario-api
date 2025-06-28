<!DOCTYPE html>
<html lang="es">

<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Sistema de Inventario')</title>
    <script src="https://cdn.tailwindcss.com"></script>
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body class="bg-white">
  
    <!-- Navegación -->
    <nav class="flex justify-between border-b-2">
        <div class="flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" class="w-12 h-12 m-4">
                <path
                    d="M50.7 58.5L0 160l208 0 0-128L93.7 32C75.5 32 58.9 42.3 50.7 58.5zM240 160l208 0L397.3 58.5C389.1 42.3 372.5 32 354.3 32L240 32l0 128zm208 32L0 192 0 416c0 35.3 28.7 64 64 64l320 0c35.3 0 64-28.7 64-64l0-224z" />
            </svg>
            <h1>Sistema de Inventario</h1>
        </div>

        <div class="flex space-x-4 p-4">
            <a href="{{ route('dashboard') }}"
                class="{{ request()->routeIs('dashboard') ? 'bg-blue-500 text-white' : 'bg-gray-300 hover:bg-gray-400 text-gray-700' }} px-3 py-0.5 rounded text-sm w-24 h-10 text-center flex items-center justify-center">Home</a>
            <a href="{{ route('categorias') }}"
                class="{{ request()->routeIs('categorias') ? 'bg-blue-500 text-white' : 'bg-gray-300 hover:bg-gray-400 text-gray-700' }} px-3 py-0.5 rounded text-sm w-24 h-10 text-center flex items-center justify-center">Categorías</a>
            <a href="{{ route('productos') }}"
                class="{{ request()->routeIs('productos') ? 'bg-blue-500 text-white' : 'bg-gray-300 hover:bg-gray-400 text-gray-700' }} px-3 py-0.5 rounded text-sm w-24 h-10 text-center flex items-center justify-center">Productos</a>
        </div>
    </nav>

    <!-- Contenido principal -->
    <main class="container mx-auto p-4">
        @yield('content')
    </main>

     @yield('scripts')
</body>

</html>
