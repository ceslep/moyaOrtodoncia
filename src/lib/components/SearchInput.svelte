<script lang="ts">
  let { value = $bindable(''), placeholder = 'Buscar...', onSearch = (v: string) => {} } = $props();
  let timeout: ReturnType<typeof setTimeout> | undefined;

  function handleInput(e: Event) {
    const target = e.target as HTMLInputElement;
    value = target.value;
    clearTimeout(timeout);
    timeout = setTimeout(() => onSearch(value), 300);
  }
</script>

<div class="relative">
  <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
  </svg>
  <input
    type="text"
    {placeholder}
    value={value}
    oninput={handleInput}
    class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm bg-gray-50/50 focus:bg-white transition-all placeholder:text-gray-400"
  />
</div>
