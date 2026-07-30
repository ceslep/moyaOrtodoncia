<script lang="ts">
  let { tabs = [], activeTab = $bindable(''), onTabChange = (id: string) => {} } = $props();

  function select(id: string) {
    activeTab = id;
    onTabChange(id);
  }
</script>

<div class="border-b border-gray-100 overflow-x-auto">
  <div class="flex gap-1 -mb-px min-w-max px-4" role="tablist">
    {#each tabs as tab}
      <button
        role="tab"
        class="px-4 py-3 text-sm font-medium whitespace-nowrap border-b-2 transition-all
          {activeTab === tab.id
            ? 'border-primary-600 text-primary-600'
            : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'}"
        onclick={() => select(tab.id)}
      >
        {tab.label}
        {#if tab.count !== undefined}
          <span class="ml-1.5 text-xs px-1.5 py-0.5 rounded-full
            {activeTab === tab.id ? 'bg-primary-100 text-primary-700' : 'bg-gray-100 text-gray-500'}">
            {tab.count}
          </span>
        {/if}
      </button>
    {/each}
  </div>
</div>
