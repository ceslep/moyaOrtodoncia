<script lang="ts">
  import { searchPacientes } from '$lib/api';
  import type { Paciente } from '$lib/types';
  import SearchInput from '$lib/components/SearchInput.svelte';
  import Pagination from '$lib/components/Pagination.svelte';
  import Badge from '$lib/components/Badge.svelte';
  import OrbLoader from '$lib/components/OrbLoader.svelte';
  import ThiingsIcon from '$lib/components/ThiingsIcon.svelte';
  import GlassCard from '$lib/components/GlassCard.svelte';

  let { onNavigate = (view: string, params?: Record<string, unknown>) => {} } = $props();

  let search = $state('');
  let page = $state(1);
  let data = $state<Paciente[]>([]);
  let meta = $state<{ page: number; per_page: number; total: number; total_pages: number } | null>(null);
  let loading = $state(true);
  let error = $state('');

  async function load() {
    loading = true;
    error = '';
    try {
      const res = await searchPacientes(search, page);
      data = res.data;
      meta = res.meta;
    } catch (e) {
      error = e instanceof Error ? e.message : 'Error al cargar';
    } finally {
      loading = false;
    }
  }

  load();

  function handleSearch(v: string) {
    search = v;
    page = 1;
    load();
  }

  function handlePage(p: number) {
    page = p;
    load();
  }

  function estadoBadge(estado: string | null) {
    if (estado === 'ACTIVO') return { text: 'Activo', color: 'green' as const };
    if (estado === 'INACTIVO') return { text: 'Inactivo', color: 'red' as const };
    return { text: estado || 'N/A', color: 'gray' as const };
  }

  function nombreCompleto(p: Paciente): string {
    return [p.nombre1, p.nombre2, p.apellido1, p.apellido2].filter(Boolean).join(' ') || p.nombres || '';
  }

  function fmt(n: number | null): string {
    if (n === null || n === undefined) return '$0';
    return '$' + n.toLocaleString('es-CO');
  }

  function initials(p: Paciente): string {
    const n = nombreCompleto(p);
    const parts = n.split(' ').filter(Boolean);
    if (parts.length >= 2) return (parts[0][0] + parts[1][0]).toUpperCase();
    return n.charAt(0).toUpperCase();
  }
</script>

<div class="space-y-6">
  <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
    <div class="flex items-center gap-3">
      <h1 class="text-2xl md:text-3xl font-bold tracking-tight text-slate-900">Pacientes</h1>
      {#if meta}
        <span class="num inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full
          bg-primary-600/10 border border-primary-600/15 text-xs font-semibold text-primary-700">
          {meta.total.toLocaleString()}
          <span class="font-medium text-primary-700/70">registros</span>
        </span>
      {/if}
    </div>

    <div class="w-full sm:max-w-sm">
      <SearchInput value={search} onSearch={handleSearch} placeholder="Buscar por nombre, ID o historia..." />
    </div>
  </div>

  {#if loading}
    <div class="space-y-6">
      <div class="flex justify-center py-6">
        <OrbLoader size={56} state="searching" />
      </div>
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        {#each [0, 1, 2, 3, 4, 5] as i}
          <div class="skeleton h-[176px] rounded-2xl" style="animation-delay: {i * 70}ms"></div>
        {/each}
      </div>
    </div>
  {:else if error}
    <div class="rounded-2xl border border-red-200/80 bg-red-50/80 backdrop-blur-xl px-5 py-4
      text-red-800 shadow-[var(--shadow-soft)] flex items-center gap-3">
      <svg class="w-5 h-5 flex-shrink-0 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
      </svg>
      <span class="text-sm font-medium">{error}</span>
    </div>
  {:else if data.length === 0}
    <GlassCard padding="px-6 py-14" class="text-center">
      <div class="mx-auto w-14 h-14 rounded-2xl bg-slate-500/8 border border-slate-500/12 flex items-center justify-center mb-4">
        <svg class="w-7 h-7 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
          <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
        </svg>
      </div>
      <p class="text-sm font-semibold text-slate-700">Sin resultados</p>
      <p class="mt-1 text-sm text-slate-500">Ajusta el termino de busqueda para encontrar pacientes.</p>
    </GlassCard>
  {:else}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 list-optimized">
      {#each data as p, i}
        <button
          class="group glass-panel rounded-2xl p-4 sm:p-5 text-left animate-rise focus-ring
            transition-[transform,box-shadow] duration-200 ease-out
            hover:-translate-y-0.5 hover:shadow-lift active:translate-y-0 active:scale-[0.99]"
          style="--i: {i % 12}"
          onclick={() => onNavigate('ficha', { ind: p.ind })}
        >
          <div class="flex items-start gap-3">
            <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-primary-500 to-primary-700
              flex items-center justify-center text-white font-bold text-sm flex-shrink-0 overflow-hidden
              shadow-[var(--shadow-glow-primary)]
              transition-transform duration-200 ease-out group-hover:scale-105">
              {#if p.tiene_foto}
                <ThiingsIcon name="patient" size={24} alt="" />
              {:else}
                {initials(p)}
              {/if}
            </div>
            <div class="flex-1 min-w-0">
              <p class="font-semibold text-slate-900 truncate transition-colors group-hover:text-primary-700">{nombreCompleto(p)}</p>
              <p class="num text-xs text-slate-500 mt-0.5 truncate">#{p.historia} · {p.identificacion}</p>
            </div>
            <Badge {...estadoBadge(p.estado)} dot />
          </div>

          <div class="mt-4 grid grid-cols-2 gap-2 text-xs">
            <div class="flex items-center gap-2 rounded-xl bg-white/60 border border-white/80 px-2.5 py-2 text-slate-700">
              <svg class="w-3.5 h-3.5 text-slate-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
              </svg>
              <span class="num truncate">{p.edad || '-'} años</span>
            </div>
            <div class="flex items-center gap-2 rounded-xl bg-white/60 border border-white/80 px-2.5 py-2 text-slate-700">
              <svg class="w-3.5 h-3.5 text-slate-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z" />
              </svg>
              <span class="num truncate">{p.telefono_movil || p.telefono_residencia1 || '-'}</span>
            </div>
          </div>

          <!-- Saldo: deuda = pill roja + peso alto; en cero = texto tenue sin adorno -->
          <div class="mt-4 pt-3.5 border-t border-slate-200/70 flex items-center justify-between gap-2">
            <span class="text-[11px] font-semibold uppercase tracking-wider text-slate-500">Saldo</span>
            {#if p.saldo && p.saldo > 0}
              <span class="num inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full
                bg-red-500/12 border border-red-600/25 text-sm font-bold text-red-700">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                </svg>
                {fmt(p.saldo)}
              </span>
            {:else}
              <span class="num text-sm font-medium text-slate-500">{fmt(p.saldo)}</span>
            {/if}
          </div>
        </button>
      {/each}
    </div>

    {#if meta}
      <div class="pt-2">
        <Pagination page={meta.page} totalPages={meta.total_pages} onPageChange={handlePage} />
      </div>
    {/if}
  {/if}
</div>
