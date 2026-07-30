<script lang="ts">
  import { getCitasGlobales } from '$lib/api';
  import type { Cita } from '$lib/types';
  import SearchInput from '$lib/components/SearchInput.svelte';
  import Pagination from '$lib/components/Pagination.svelte';
  import Badge from '$lib/components/Badge.svelte';
  import OrbLoader from '$lib/components/OrbLoader.svelte';

  let { onNavigate = (view: string, params?: Record<string, unknown>) => {} } = $props();

  const today = new Date().toISOString().split('T')[0];
  let desde = $state(today);
  let hasta = $state(today);
  let especialista = $state('');
  let consultorio = $state('');
  let estado = $state('');
  let page = $state(1);
  let data = $state<Cita[]>([]);
  let meta = $state<{ page: number; per_page: number; total: number; total_pages: number } | null>(null);
  let loading = $state(true);
  let error = $state('');

  async function load() {
    loading = true;
    error = '';
    try {
      const res = await getCitasGlobales({ desde, hasta, especialista, consultorio, estado }, page);
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

  function estadoBadge(cita: Cita) {
    if (cita.asistio === 'S') return { text: 'Atendida', color: 'green' as const };
    if (cita.confirmo === 'S') return { text: 'Confirmada', color: 'blue' as const };
    if (cita.asistio === 'N') return { text: 'No asistió', color: 'red' as const };
    return { text: 'Pendiente', color: 'yellow' as const };
  }

  function resetFilters() {
    desde = '';
    hasta = '';
    especialista = '';
    consultorio = '';
    estado = '';
    page = 1;
    load();
  }
</script>

<div class="space-y-5">
  <div>
    <h1 class="text-2xl md:text-3xl font-bold text-gray-900">Agenda / Citas</h1>
    {#if meta}
      <p class="text-sm text-gray-500 mt-1">{meta.total.toLocaleString()} citas encontradas</p>
    {/if}
  </div>

  <div class="bg-white rounded-2xl shadow-modern border border-gray-100/80 p-4 md:p-5">
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
      <div>
        <label for="desde" class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Desde</label>
        <input id="desde" type="date" bind:value={desde} class="w-full mt-1.5 px-3 py-2.5 border border-gray-200 rounded-xl text-sm bg-gray-50/50 transition-all" />
      </div>
      <div>
        <label for="hasta" class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Hasta</label>
        <input id="hasta" type="date" bind:value={hasta} class="w-full mt-1.5 px-3 py-2.5 border border-gray-200 rounded-xl text-sm bg-gray-50/50 transition-all" />
      </div>
      <div>
        <label for="especialista" class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Especialista</label>
        <SearchInput value={especialista} onSearch={(v: string) => { especialista = v; }} placeholder="Nombre..." />
      </div>
      <div>
        <label for="consultorio" class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Consultorio</label>
        <SearchInput value={consultorio} onSearch={(v: string) => { consultorio = v; }} placeholder="Nro..." />
      </div>
      <div>
        <label for="estado" class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Estado</label>
        <select id="estado" bind:value={estado} class="w-full mt-1.5 px-3 py-2.5 border border-gray-200 rounded-xl text-sm bg-gray-50/50 transition-all">
          <option value="">Todos</option>
          <option value="asistio">Atendida</option>
          <option value="confirmo">Confirmada</option>
          <option value="pendiente">Pendiente</option>
        </select>
      </div>
    </div>
    <div class="flex gap-2 mt-4">
      <button class="px-5 py-2.5 bg-primary-600 text-white rounded-xl text-sm font-medium hover:bg-primary-700 transition-colors shadow-sm" onclick={() => { page = 1; load(); }}>
        Buscar
      </button>
      <button class="px-5 py-2.5 border border-gray-200 rounded-xl text-sm font-medium text-gray-600 hover:bg-gray-50 transition-colors" onclick={resetFilters}>
        Limpiar
      </button>
    </div>
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
    <div class="bg-white rounded-2xl shadow-modern border border-gray-100/80 overflow-hidden">
      <div class="overflow-x-auto table-responsive">
        <table class="w-full text-sm">
          <thead class="bg-gray-50/80 border-b border-gray-100">
            <tr>
              <th class="text-left px-5 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider">Fecha</th>
              <th class="text-left px-5 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider">Hora</th>
              <th class="text-left px-5 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider">Paciente</th>
              <th class="text-left px-5 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider">Procedimiento</th>
              <th class="text-left px-5 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider">Consultorio</th>
              <th class="text-left px-5 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider">Especialista</th>
              <th class="text-left px-5 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider">Tipo</th>
              <th class="text-left px-5 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider">Estado</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100/80">
            {#each data as c}
              <tr class="hover:bg-gray-50/50 transition-colors">
                <td class="px-5 py-3 text-gray-700" data-label="Fecha">{c.fecha}</td>
                <td class="px-5 py-3 text-gray-700" data-label="Hora">{c.horas}</td>
                <td class="px-5 py-3 font-medium text-primary-600 cursor-pointer hover:underline" data-label="Paciente"
                    onclick={() => onNavigate('ficha', { ind: c.paciente })}>
                  {c.paciente}
                </td>
                <td class="px-5 py-3 text-gray-700" data-label="Procedimiento">{c.procedimiento || '-'}</td>
                <td class="px-5 py-3 text-gray-700" data-label="Consultorio">{c.consultorio || '-'}</td>
                <td class="px-5 py-3 text-gray-700" data-label="Especialista">{c.especialista || '-'}</td>
                <td class="px-5 py-3 text-gray-700" data-label="Tipo">{c.tipo || '-'}</td>
                <td class="px-5 py-3" data-label="Estado"><Badge {...estadoBadge(c)} /></td>
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
