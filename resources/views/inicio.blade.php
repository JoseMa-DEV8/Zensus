<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>INE Datos Nacionales</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet">
  <style>
    body {
      font-family: 'Poppins', sans-serif;
    }
    .icon-hamburger {
      width: 28px;
      height: 22px;
      cursor: pointer;
    }
  </style>
</head>
<body class="bg-cover bg-center bg-no-repeat text-white relative" style="background-image: url('/fondos.png')">

  <!-- Header con logo y hamburguesa -->
  <header class="fixed top-0 left-0 w-full flex items-center justify-between px-6 py-4 z-50 bg-transparent">
    <a href="/">
      <img src="/yota.png" alt="Logo" class="h-24 object-contain">
    </a>
    <button onclick="toggleSidebar()" class="bg-gray-700 p-2 rounded">
      <svg class="icon-hamburger" viewBox="0 0 31 25" fill="none" xmlns="http://www.w3.org/2000/svg">
        <rect x="0.58" y="0.72" width="30" height="4" fill="#fff" />
        <rect x="0.58" y="10.72" width="30" height="4" fill="#fff" />
        <rect x="0.58" y="20.72" width="30" height="4" fill="#fff" />
      </svg>
    </button>
  </header>

  <!-- Sidebar desde la derecha -->
  <nav id="sidebar" class="fixed top-0 right-0 h-full w-64 bg-gray-700 bg-opacity-80 backdrop-blur-sm shadow-lg px-6 pt-24 z-40 transform translate-x-full transition-transform duration-300">
    <ul class="space-y-4">
      <li><a href="/" class="text-gray-100 hover:text-blue-400 font-semibold flex items-center gap-2"><span class="material-symbols-outlined">home</span>Home</a></li>
      <li><a href="/demografia" class="text-gray-100 hover:text-blue-400 font-semibold flex items-center gap-2"><span class="material-symbols-outlined">explore</span>Demografía</a></li>
      <li><a href="/economia/rentas" class="text-gray-100 hover:text-blue-400 font-semibold flex items-center gap-2"><span class="material-symbols-outlined">euro</span>Economía</a></li>
            <li><a href="/economia/empresas_sectores" class="text-gray-100 hover:text-blue-400 font-semibold flex items-center gap-2"><span class="material-symbols-outlined">work</span>Empresas</a></li>

    </ul>
  </nav>

  <!-- Contenido principal -->
  <main class="pt-40 px-6 flex items-center justify-center min-h-screen">
    <div class="bg-black bg-opacity-60 p-10 rounded-xl shadow-lg max-w-3xl w-full text-center">
      <h1 class="text-4xl font-bold mb-6">DATOS SOCIOECONÓMICOS DE ESPAÑA</h1>
      <p class="text-xl mb-8">Explora la industria, economía y población en España.</p>
      <a href="/demografia" class="inline-block bg-blue-600 text-white px-6 py-3 rounded hover:bg-blue-700 font-semibold">EXPLORAR</a>
    </div>
  </main>

  <script>
    function toggleSidebar() {
      const sidebar = document.getElementById('sidebar');
      sidebar.classList.toggle('translate-x-full');
      sidebar.classList.toggle('translate-x-0');
    }
  </script>

</body>
</html>