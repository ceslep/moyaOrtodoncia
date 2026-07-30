<script lang="ts">
  import ThiingsIcon from './ThiingsIcon.svelte';

  let { currentView = $bindable(), params = $bindable() } = $props();

  const menuItems = [
    { id: 'dashboard', label: 'Dashboard', icon: 'dashboard' },
    { id: 'pacientes', label: 'Pacientes', icon: 'patient' },
    { id: 'agenda', label: 'Agenda / Citas', icon: 'calendar' },
    { id: 'financiero', label: 'Financiero', icon: 'finances' },
    { id: 'catalogos', label: 'Catálogos', icon: 'folder' },
  ];

  let sidebarOpen = $state(false);

  function navigate(view: string) {
    currentView = view;
    params = {};
    sidebarOpen = false;
  }
</script>

<button
  class="fixed top-4 right-4 z-[60] lg:hidden bg-white/75 backdrop-blur-xl rounded-xl p-2.5
    shadow-[var(--shadow-float)] border border-white/70 focus-ring
    active:scale-95 transition-transform duration-200 ease-out"
  onclick={() => sidebarOpen = !sidebarOpen}
  aria-label="Toggle menu"
  aria-expanded={sidebarOpen}
>
  <svg class="w-6 h-6 text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
    {#if sidebarOpen}
      <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
    {:else}
      <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
    {/if}
  </svg>
</button>

{#if sidebarOpen}
  <button
    class="fixed inset-0 bg-black/40 backdrop-blur-sm z-40 lg:hidden"
    onclick={() => sidebarOpen = false}
    aria-label="Cerrar menú"
  ></button>
{/if}

<aside class="fixed top-0 left-0 h-full w-[280px] lg:w-72 z-50 flex flex-col text-white
  bg-gradient-to-b from-blue-950 via-primary-700 to-indigo-950
  border-r border-white/10 shadow-[0_24px_64px_-16px_rgba(2,6,23,0.55)]
  transition-transform duration-300 ease-out
  {sidebarOpen ? 'translate-x-0' : '-translate-x-full'} lg:translate-x-0">

  <!-- Halo ambiental: rompe el plano del gradiente sin competir con el contenido -->
  <div class="pointer-events-none absolute inset-0 overflow-hidden" aria-hidden="true">
    <span class="absolute -top-20 -left-10 w-56 h-56 rounded-full bg-blue-400/20 blur-3xl"></span>
    <span class="absolute bottom-10 -right-16 w-52 h-52 rounded-full bg-emerald-400/12 blur-3xl"></span>
  </div>

  <div class="relative p-5 pb-4 border-b border-white/10 bg-white/5 backdrop-blur-xl">
    <div class="flex items-center gap-3">
      <div class="w-11 h-11 rounded-xl bg-white/15 border border-white/20 flex items-center justify-center flex-shrink-0 overflow-hidden">
        <ThiingsIcon name="tooth" size={28} alt="Moya Ortodoncia" />
      </div>
      <div>
        <h1 class="text-base font-bold tracking-tight leading-tight">Moya Ortodoncia</h1>
        <p class="text-blue-200/80 text-xs mt-0.5">Sistema de Consultas</p>
      </div>
    </div>
  </div>

  <nav class="relative flex-1 px-3 py-4 space-y-1.5 overflow-y-auto">
    {#each menuItems as item}
      {@const isActive = currentView === item.id}
      <button
        class="group relative w-full flex items-center gap-3 pl-4 pr-3 py-3 rounded-xl text-sm font-semibold
          focus-ring-light transition-all duration-200 ease-out
          {isActive
            ? 'bg-white/15 text-white shadow-[0_8px_24px_-10px_rgba(0,0,0,0.6)]'
            : 'text-blue-100/85 hover:bg-white/10 hover:text-white'}"
        aria-current={isActive ? 'page' : undefined}
        onclick={() => navigate(item.id)}
      >
        <!-- Indicador activo: barra lateral con glow -->
        <span
          class="absolute left-0 top-1/2 -translate-y-1/2 w-1 rounded-r-full bg-emerald-400
            transition-all duration-200 ease-out
            {isActive ? 'h-7 opacity-100 shadow-[0_0_12px_2px_rgba(52,211,153,0.6)]' : 'h-3 opacity-0'}"
        ></span>
        <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0 overflow-hidden
          transition-transform duration-200 ease-out group-hover:scale-105
          {isActive ? 'bg-white/20 border border-white/25' : 'bg-white/8'}">
          <ThiingsIcon name={item.icon} size={22} alt={item.label} />
        </div>
        <span class="truncate">{item.label}</span>
      </button>
    {/each}
  </nav>

  <div class="relative p-3 mx-3 mb-4 rounded-xl bg-white/8 backdrop-blur-md border border-white/15">
    <div class="flex items-center gap-2">
      <span class="relative flex w-2 h-2">
        <span class="absolute inset-0 rounded-full bg-emerald-400 animate-pulse-dot"></span>
        <span class="relative w-2 h-2 rounded-full bg-emerald-400"></span>
      </span>
      <p class="text-blue-100/80 text-xs font-medium">Solo lectura</p>
    </div>
  </div>
</aside>
