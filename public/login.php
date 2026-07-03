<?php
// En un sistema real se validarían las variables de session previas.
?>
<!DOCTYPE html>
<html lang="es" class="dark">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Nexus ERP - Acceso Restringido</title>
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      darkMode: 'class',
      theme: { extend: { fontFamily: { sans: ['Outfit', 'sans-serif'] }, colors: { brand: { 500: '#0ea5e9', 600: '#0284c7' } } } }
    }
  </script>
  <style>
    body { background-color: #0f172a; }
    .glass-panel { background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px); border: 1px solid rgba(255, 255, 255, 0.08); }
    .gradient-bg { background: radial-gradient(circle at top right, rgba(14, 165, 233, 0.15), transparent 40%), radial-gradient(circle at bottom left, rgba(139, 92, 246, 0.15), transparent 40%); }
    .input-led:focus-within { box-shadow: 0 0 0 2px rgba(14, 165, 233, 0.4), 0 0 20px rgba(14, 165, 233, 0.2); }
  </style>
</head>
<body class="h-screen w-full overflow-hidden text-slate-200 antialiased gradient-bg flex">

  <!-- Desktop Split Layout -->
  <div class="hidden lg:flex lg:w-1/2 relative flex-col justify-between p-12 border-r border-slate-800/50 glass-panel z-10">
    <div>
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-brand-600 to-purple-600 flex items-center justify-center text-white shadow-lg shadow-brand-500/30">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
        </div>
        <span class="text-2xl font-bold tracking-tight text-white">Nexus Global</span>
      </div>
    </div>
    
    <div class="space-y-6 max-w-lg">
      <h1 class="text-5xl font-extrabold tracking-tight text-transparent bg-clip-text bg-gradient-to-r from-white to-slate-400">Panel de Control <br />Empresarial.</h1>
      <p class="text-lg text-slate-400 leading-relaxed">Centralice sus operaciones, monitorice métricas en tiempo real y gestione su puesto de comando con seguridad de grado militar y una estética incomparable.</p>
    </div>

    <div>
      <p class="text-sm font-medium text-slate-500">&copy; 2026 Nexus Systems. Security Level 4.</p>
    </div>
    
    <!-- Abstract Tech Elements -->
    <div class="absolute right-0 top-1/2 -translate-y-1/2 translate-x-1/2 w-[600px] h-[600px] bg-brand-500/10 rounded-full blur-[120px] pointer-events-none"></div>
  </div>

  <!-- Formulario Login -->
  <div class="w-full lg:w-1/2 h-full flex flex-col justify-center items-center relative p-6 sm:p-12 z-20">
    <div class="w-full max-w-md space-y-8">
      
      <div class="text-center lg:text-left space-y-2">
        <h2 class="text-3xl font-bold text-white tracking-tight">Iniciar Sesión</h2>
        <p class="text-slate-400 font-medium">Autenticación requerida para acceder al sistema.</p>
      </div>

      <form class="space-y-6 glass-panel p-8 rounded-3xl" onsubmit="event.preventDefault(); window.location.href='dashboard.php';">
        <div class="space-y-1.5">
          <label class="text-sm font-semibold text-slate-300 ml-1">ID Organizacional</label>
          <div class="relative input-led rounded-2xl transition-all">
             <input type="text" required class="w-full pl-12 pr-4 py-4 rounded-2xl bg-slate-900/50 border border-slate-700 outline-none text-white font-medium placeholder-slate-600 transition-colors" placeholder="admin@nexus.corp">
             <svg class="w-5 h-5 absolute left-4 top-4.5 text-brand-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
          </div>
        </div>

        <div class="space-y-1.5">
          <label class="text-sm font-semibold text-slate-300 ml-1">Código de Acceso</label>
          <div class="relative input-led rounded-2xl transition-all">
             <input type="password" required class="w-full pl-12 pr-4 py-4 rounded-2xl bg-slate-900/50 border border-slate-700 outline-none text-white font-medium placeholder-slate-600 transition-colors" placeholder="••••••••">
             <svg class="w-5 h-5 absolute left-4 top-4.5 text-brand-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
          </div>
        </div>

        <div class="pt-2">
          <button type="submit" class="w-full bg-gradient-to-r from-brand-600 to-brand-500 hover:from-brand-500 hover:to-brand-400 text-white font-bold py-4 px-4 rounded-2xl shadow-lg shadow-brand-500/25 transition-all outline-none focus:ring-4 focus:ring-brand-500/50 flex justify-center items-center gap-2 group">
            Ingresar al Panel
            <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
          </button>
        </div>
      </form>

      <!-- Return Button Super Clean -->
      <a href="formulario.php" class="w-full flex justify-center items-center gap-2 py-4 px-6 rounded-2xl border border-slate-800 bg-slate-900/30 hover:bg-slate-800/80 hover:border-slate-700 text-slate-400 hover:text-white transition-all font-medium backdrop-blur-md group">
        <svg class="w-5 h-5 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        Regresar al Formulario Público
      </a>

    </div>
  </div>

</body>
</html>
