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
  class="fixed top-4 left-4 z-[60] lg:hidden bg-white/80 backdrop-blur-md rounded-xl p-2.5 shadow-lg border border-gray-200/50 active:scale-95 transition-transform"
  onclick={() => sidebarOpen = !sidebarOpen}
  aria-label="Toggle menu"
>
  <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
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

<aside class="fixed top-0 left-0 h-full w-[280px] lg:w-72 bg-gradient-to-b from-[#1e3a8a] to-[#1e40af] text-white z-50 flex flex-col
  transition-transform duration-300 ease-out shadow-2xl
  {sidebarOpen ? 'translate-x-0' : '-translate-x-full'} lg:translate-x-0">

  <div class="p-5 pb-4 border-b border-white/10">
    <div class="flex items-center gap-3">
      <div class="w-11 h-11 rounded-xl bg-white/15 flex items-center justify-center flex-shrink-0 overflow-hidden">
        <ThiingsIcon name="tooth" size={28} alt="Moya Ortodoncia" />
      </div>
      <div>
        <h1 class="text-base font-bold tracking-wide leading-tight">Moya Ortodoncia</h1>
        <p class="text-blue-300/70 text-xs mt-0.5">Sistema de Consultas</p>
      </div>
    </div>
  </div>

  <nav class="flex-1 px-3 py-3 space-y-1 overflow-y-auto">
    {#each menuItems as item}
      {@const isActive = currentView === item.id}
      <button
        class="w-full flex items-center gap-3 px-3 py-3 rounded-xl text-sm font-medium transition-all duration-200
          {isActive
            ? 'bg-white/15 text-white shadow-lg shadow-black/10'
            : 'text-blue-200/80 hover:bg-white/8 hover:text-white'}"
        onclick={() => navigate(item.id)}
      >
        <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0 overflow-hidden
          {isActive ? 'bg-white/15' : 'bg-white/5'}">
          <ThiingsIcon name={item.icon} size={22} alt={item.label} />
        </div>
        {item.label}
        {#if isActive}
          <div class="ml-auto w-1.5 h-1.5 rounded-full bg-emerald-400"></div>
        {/if}
      </button>
    {/each}
  </nav>

  <div class="p-3 mx-3 mb-3 rounded-xl bg-white/5 border border-white/10">
    <div class="flex items-center gap-2">
      <div class="w-2 h-2 rounded-full bg-emerald-400"></div>
      <p class="text-blue-200/60 text-xs">Solo lectura</p>
    </div>
  </div>
</aside>
