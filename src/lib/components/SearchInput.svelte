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

<div class="group relative">
  <svg
    class="absolute left-4 top-1/2 -translate-y-1/2 w-4.5 h-4.5 text-slate-400 pointer-events-none
      transition-colors duration-200 ease-out group-focus-within:text-primary-600"
    fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"
  >
    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
  </svg>
  <input
    type="text"
    {placeholder}
    value={value}
    oninput={handleInput}
    class="w-full pl-11 pr-4 py-3 rounded-2xl text-sm text-slate-900
      bg-white/70 backdrop-blur-xl border border-white/80
      shadow-[var(--shadow-soft)] transition-all duration-200 ease-out
      hover:bg-white/85 focus:bg-white placeholder:text-slate-500"
  />
</div>
