<script lang="ts">
  let { message = '', type = 'error', onclose = () => {} } = $props();

  const colors: Record<string, string> = {
    error: 'bg-red-50 border-red-200 text-red-800',
    success: 'bg-emerald-50 border-emerald-200 text-emerald-800',
    info: 'bg-blue-50 border-blue-200 text-blue-800',
  };

  $effect(() => {
    if (message) {
      const t = setTimeout(() => onclose(), 5000);
      return () => clearTimeout(t);
    }
  });
</script>

{#if message}
  <div class="fixed top-4 right-4 z-[100] max-w-md animate-slide-in">
    <div class="flex items-center gap-3 px-5 py-3.5 rounded-2xl border shadow-lg {colors[type] || colors.error}">
      <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
        {#if type === 'error'}
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        {:else if type === 'success'}
          <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
        {:else}
          <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        {/if}
      </svg>
      <p class="text-sm font-medium flex-1">{message}</p>
      <button onclick={() => onclose()} class="text-current opacity-50 hover:opacity-100 transition-opacity p-1" aria-label="Cerrar">&times;</button>
    </div>
  </div>
{/if}

<style>
  @keyframes slide-in {
    from { transform: translateX(100%) scale(0.95); opacity: 0; }
    to { transform: translateX(0) scale(1); opacity: 1; }
  }
  .animate-slide-in {
    animation: slide-in 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  }
</style>
