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
  <div class="flex items-center justify-center gap-1">
    <button
      class="w-9 h-9 rounded-lg flex items-center justify-center text-sm border border-gray-200 hover:bg-gray-50 disabled:opacity-30 disabled:cursor-not-allowed transition-colors"
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
        <span class="w-9 h-9 flex items-center justify-center text-sm text-gray-400">...</span>
      {:else}
        <button
          class="w-9 h-9 rounded-lg flex items-center justify-center text-sm font-medium transition-all
            {p === page ? 'bg-primary-600 text-white shadow-sm' : 'border border-gray-200 hover:bg-gray-50 text-gray-700'}"
          onclick={() => onPageChange(p as number)}
        >
          {p}
        </button>
      {/if}
    {/each}

    <button
      class="w-9 h-9 rounded-lg flex items-center justify-center text-sm border border-gray-200 hover:bg-gray-50 disabled:opacity-30 disabled:cursor-not-allowed transition-colors"
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
