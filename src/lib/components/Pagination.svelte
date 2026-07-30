<script lang="ts">
  let { page = 1, totalPages = 1, onPageChange = (p: number) => {} } = $props();

  let pages = $derived.by(() => {
    const arr: (number | string)[] = [];
    const maxVisible = 5;
    if (totalPages <= maxVisible) {
      for (let i = 1; i <= totalPages; i++) arr.push(i);
    } else {
      arr.push(1);
      if (page > 3) arr.push('...');
      const start = Math.max(2, page - 1);
      const end = Math.min(totalPages - 1, page + 1);
      for (let i = start; i <= end; i++) arr.push(i);
      if (page < totalPages - 2) arr.push('...');
      arr.push(totalPages);
    }
    return arr;
  });
</script>

{#if totalPages > 1}
  <div class="flex items-center justify-center gap-1.5">
    <button
      class="w-10 h-10 rounded-xl flex items-center justify-center text-sm text-slate-600 focus-ring
        bg-white/70 backdrop-blur-md border border-white/80 shadow-[var(--shadow-soft)]
        hover:bg-white hover:text-primary-700 active:scale-[0.96]
        disabled:opacity-35 disabled:cursor-not-allowed disabled:hover:bg-white/70
        transition-all duration-200 ease-out"
      disabled={page <= 1}
      onclick={() => onPageChange(page - 1)}
      aria-label="Página anterior"
    >
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
      </svg>
    </button>

    {#each pages as p}
      {#if p === '...'}
        <span class="w-10 h-10 flex items-center justify-center text-sm text-slate-500" aria-hidden="true">...</span>
      {:else}
        <button
          class="num w-10 h-10 rounded-xl flex items-center justify-center text-sm font-semibold focus-ring
            transition-all duration-200 ease-out active:scale-[0.96]
            {p === page
              ? 'bg-gradient-to-br from-primary-600 to-primary-700 text-white shadow-[var(--shadow-glow-primary)]'
              : 'bg-white/70 backdrop-blur-md border border-white/80 shadow-[var(--shadow-soft)] text-slate-700 hover:bg-white hover:text-primary-700'}"
          aria-current={p === page ? 'page' : undefined}
          onclick={() => onPageChange(p as number)}
        >
          {p}
        </button>
      {/if}
    {/each}

    <button
      class="w-10 h-10 rounded-xl flex items-center justify-center text-sm text-slate-600 focus-ring
        bg-white/70 backdrop-blur-md border border-white/80 shadow-[var(--shadow-soft)]
        hover:bg-white hover:text-primary-700 active:scale-[0.96]
        disabled:opacity-35 disabled:cursor-not-allowed disabled:hover:bg-white/70
        transition-all duration-200 ease-out"
      disabled={page >= totalPages}
      onclick={() => onPageChange(page + 1)}
      aria-label="Página siguiente"
    >
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
      </svg>
    </button>
  </div>
{/if}
