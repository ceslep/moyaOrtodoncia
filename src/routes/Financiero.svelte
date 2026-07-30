<script lang="ts">
  import { getAbonosGlobales } from '$lib/api';
  import type { Abono } from '$lib/types';
  import SearchInput from '$lib/components/SearchInput.svelte';
  import Pagination from '$lib/components/Pagination.svelte';
  import OrbLoader from '$lib/components/OrbLoader.svelte';

  let { onNavigate = (view: string, params?: Record<string, unknown>) => {} } = $props();

  let desde = $state('');
  let hasta = $state('');
  let formaPago = $state('');
  let page = $state(1);
  let data = $state<Abono[]>([]);
  let meta = $state<{ page: number; per_page: number; total: number; total_pages: number } | null>(null);
  let loading = $state(true);
  let error = $state('');

  async function load() {
    loading = true;
    error = '';
    try {
      const res = await getAbonosGlobales({ desde, hasta, forma_de_pago: formaPago }, page);
      data = res.data;
      meta = res.meta;
    } catch (e) {
      error = e instanceof Error ? e.message : 'Error al cargar';
    } finally {
      loading = false;
    }
  }

  load();

  function handlePage(p: number) {
    page = p;
    load();
  }

  function fmt(n: number | null): string {
    if (n === null || n === undefined) return '$0';
    return '$' + n.toLocaleString('es-CO');
  }

  function fmtDate(date: string | null): string {
    if (!date) return '-';
    const [y, m, d] = date.split('-');
    return `${d}/${m}/${y}`;
  }

  function resetFilters() {
    desde = '';
    hasta = '';
    formaPago = '';
    page = 1;
    load();
  }
</script>

<div class="space-y-5">
  <div>
    <h1 class="text-2xl md:text-3xl font-bold text-gray-900">Financiero - Abonos</h1>
    {#if meta}
      <p class="text-sm text-gray-500 mt-1">{meta.total.toLocaleString()} abonos encontrados</p>
    {/if}
  </div>

  <div class="bg-white rounded-2xl shadow-modern border border-gray-100/80 p-4 md:p-5">
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
      <div>
        <label for="fdesde" class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Desde</label>
        <input id="fdesde" type="date" bind:value={desde} class="w-full mt-1.5 px-3 py-2.5 border border-gray-200 rounded-xl text-sm bg-gray-50/50 transition-all" />
      </div>
      <div>
        <label for="fhasta" class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Hasta</label>
        <input id="fhasta" type="date" bind:value={hasta} class="w-full mt-1.5 px-3 py-2.5 border border-gray-200 rounded-xl text-sm bg-gray-50/50 transition-all" />
      </div>
      <div>
        <label for="fpago" class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Forma de Pago</label>
        <select id="fpago" bind:value={formaPago} class="w-full mt-1.5 px-3 py-2.5 border border-gray-200 rounded-xl text-sm bg-gray-50/50 transition-all">
          <option value="">Todas</option>
          <option value="EFECTIVO">Efectivo</option>
          <option value="TARJETA">Tarjeta</option>
          <option value="TRANSFERENCIA">Transferencia</option>
          <option value="CHEQUE">Cheque</option>
        </select>
      </div>
      <div class="flex items-end gap-2">
        <button class="px-5 py-2.5 bg-primary-600 text-white rounded-xl text-sm font-medium hover:bg-primary-700 transition-colors shadow-sm" onclick={() => { page = 1; load(); }}>
          Buscar
        </button>
        <button class="px-5 py-2.5 border border-gray-200 rounded-xl text-sm font-medium text-gray-600 hover:bg-gray-50 transition-colors" onclick={resetFilters}>
          Limpiar
        </button>
      </div>
    </div>
  </div>

  {#if loading}
    <div class="flex justify-center py-16">
      <OrbLoader size={56} state="working" />
    </div>
  {:else if error}
    <div class="bg-red-50 border border-red-200 text-red-700 px-5 py-4 rounded-2xl flex items-center gap-3">
      <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
      </svg>
      {error}
    </div>
  {:else}
    <div class="bg-white rounded-2xl shadow-modern border border-gray-100/80 overflow-hidden">
      <div class="overflow-x-auto table-responsive">
        <table class="w-full text-sm">
          <thead class="bg-gray-50/80 border-b border-gray-100">
            <tr>
              <th class="text-left px-5 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider">Fecha</th>
              <th class="text-left px-5 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider">Paciente</th>
              <th class="text-left px-5 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider">Identificación</th>
              <th class="text-left px-5 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider">Valor</th>
              <th class="text-left px-5 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider">Forma de Pago</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100/80">
            {#each data as a}
              <tr class="hover:bg-gray-50/50 transition-colors">
                <td class="px-5 py-3 text-gray-700" data-label="Fecha">{fmtDate(a.fecha)}</td>
                <td class="px-5 py-3 font-medium text-primary-600 cursor-pointer hover:underline" data-label="Paciente"
                    onclick={() => onNavigate('ficha', { ind: a.paciente })}>
                  {a.paciente}
                </td>
                <td class="px-5 py-3 text-gray-700" data-label="Identificación">{a.identificacion || '-'}</td>
                <td class="px-5 py-3 font-semibold text-health-600" data-label="Valor">{fmt(a.valor_abono)}</td>
                <td class="px-5 py-3 text-gray-700" data-label="Forma de Pago">{a.forma_de_pago || '-'}</td>
              </tr>
            {/each}
          </tbody>
        </table>
      </div>
      {#if meta}
        <div class="px-5 py-4 border-t border-gray-100">
          <Pagination page={meta.page} totalPages={meta.total_pages} onPageChange={handlePage} />
        </div>
      {/if}
    </div>
  {/if}
</div>
