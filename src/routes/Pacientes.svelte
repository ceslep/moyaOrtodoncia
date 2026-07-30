<script lang="ts">
  import { searchPacientes } from '$lib/api';
  import type { Paciente } from '$lib/types';
  import SearchInput from '$lib/components/SearchInput.svelte';
  import Pagination from '$lib/components/Pagination.svelte';
  import Badge from '$lib/components/Badge.svelte';
  import OrbLoader from '$lib/components/OrbLoader.svelte';
  import ThiingsIcon from '$lib/components/ThiingsIcon.svelte';

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

<div class="space-y-5">
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
      <h1 class="text-2xl md:text-3xl font-bold text-gray-900">Pacientes</h1>
      {#if meta}
        <p class="text-sm text-gray-500 mt-1">{meta.total.toLocaleString()} registros</p>
      {/if}
    </div>
  </div>

  <div class="max-w-md">
    <SearchInput value={search} onSearch={handleSearch} placeholder="Buscar por nombre, ID o historia..." />
  </div>

  {#if loading}
    <div class="flex justify-center py-16">
      <OrbLoader size={56} state="searching" />
    </div>
  {:else if error}
    <div class="bg-red-50 border border-red-200 text-red-700 px-5 py-4 rounded-2xl flex items-center gap-3">
      <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
      </svg>
      {error}
    </div>
  {:else}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
      {#each data as p}
        <button
          class="bg-white rounded-2xl p-4 border border-gray-200 text-left hover:shadow-lg hover:scale-[1.01] transition-all duration-200 group"
          onclick={() => onNavigate('ficha', { ind: p.ind })}
        >
          <div class="flex items-start gap-3 mb-3">
            <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-blue-500 to-blue-700 flex items-center justify-center text-white font-bold text-sm flex-shrink-0 group-hover:scale-105 transition-transform overflow-hidden">
              {#if p.tiene_foto}
                <ThiingsIcon name="patient" size={24} alt="" />
              {:else}
                {initials(p)}
              {/if}
            </div>
            <div class="flex-1 min-w-0">
              <p class="font-semibold text-gray-900 truncate group-hover:text-primary-600 transition-colors">{nombreCompleto(p)}</p>
              <p class="text-xs text-gray-500 mt-0.5">#{p.historia} · {p.identificacion}</p>
            </div>
            <Badge {...estadoBadge(p.estado)} />
          </div>

          <div class="grid grid-cols-2 gap-3 text-xs">
            <div class="flex items-center gap-1.5 text-gray-600">
              <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
              </svg>
              <span>{p.edad || '-'} años</span>
            </div>
            <div class="flex items-center gap-1.5 text-gray-600">
              <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z" />
              </svg>
              <span>{p.telefono_movil || p.telefono_residencia1 || '-'}</span>
            </div>
          </div>

          <div class="mt-4 pt-3 border-t border-gray-100 flex items-center justify-between">
            <span class="text-xs text-gray-500">Saldo</span>
            <span class="text-sm font-bold {p.saldo && p.saldo > 0 ? 'text-red-600' : 'text-health-600'}">{fmt(p.saldo)}</span>
          </div>
        </button>
      {/each}
    </div>

    {#if meta}
      <div class="mt-6">
        <Pagination page={meta.page} totalPages={meta.total_pages} onPageChange={handlePage} />
      </div>
    {/if}
  {/if}
</div>
