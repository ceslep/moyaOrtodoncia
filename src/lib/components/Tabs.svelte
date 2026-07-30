<script lang="ts">
  let { tabs = [], activeTab = $bindable(''), onTabChange = (id: string) => {} } = $props();

  function select(id: string) {
    activeTab = id;
    onTabChange(id);
  }
</script>

<div class="border-b border-slate-200/70 bg-white/50 backdrop-blur-md overflow-x-auto">
  <div class="flex gap-1 min-w-max px-3 sm:px-4 py-2" role="tablist">
    {#each tabs as tab}
      {@const isActive = activeTab === tab.id}
      <button
        role="tab"
        aria-selected={isActive}
        class="relative px-4 py-2.5 rounded-xl text-sm font-semibold whitespace-nowrap focus-ring
          transition-all duration-200 ease-out
          {isActive
            ? 'bg-primary-600/10 text-primary-700'
            : 'text-slate-500 hover:text-slate-800 hover:bg-slate-500/8'}"
        onclick={() => select(tab.id)}
      >
        {tab.label}
        {#if tab.count !== undefined}
          <span class="ml-1.5 num text-[11px] font-bold px-1.5 py-0.5 rounded-full
            {isActive ? 'bg-primary-600/15 text-primary-700' : 'bg-slate-500/10 text-slate-500'}">
            {tab.count}
          </span>
        {/if}
        {#if isActive}
          <!-- Indicador activo: barra corta bajo la pill -->
          <span
            class="pointer-events-none absolute left-1/2 -translate-x-1/2 bottom-0 h-0.5 w-7 rounded-full
              bg-gradient-to-r from-primary-600 to-health-600"
          ></span>
        {/if}
      </button>
    {/each}
  </div>
</div>
