<script lang="ts">
  import { getPersonal, getPersonalFicha } from '$lib/api';
  import type { HojaVida } from '$lib/types';
  import SearchInput from '$lib/components/SearchInput.svelte';
  import Pagination from '$lib/components/Pagination.svelte';
  import Badge from '$lib/components/Badge.svelte';

  let { onNavigate = () => {} } = $props();

  let search = $state('');
  let page = $state(1);
  let data = $state<HojaVida[]>([]);
  let meta = $state<{ page: number; per_page: number; total: number; total_pages: number } | null>(null);
  let loading = $state(true);
  let error = $state('');
  let selectedId = $state<number | null>(null);
  let ficha = $state<HojaVida | null>(null);
  let loadingFicha = $state(false);

  async function load() {
    loading = true;
    error = '';
    try {
      const res = await getPersonal(search, page);
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

  async function selectPersonal(ind: number, identificacion: number) {
    selectedId = ind;
    loadingFicha = true;
    try {
      const res = await getPersonalFicha(ind, identificacion);
      ficha = res.data;
    } catch (e) {
      error = e instanceof Error ? e.message : 'Error';
    } finally {
      loadingFicha = false;
    }
  }

  function nombreCompleto(h: HojaVida): string {
    return [h.nombres, h.apellidos].filter(Boolean).join(' ') || h.nombresp || '';
  }
</script>

<div class="space-y-4">
  <div>
    <h1 class="text-2xl font-bold text-gray-900">Personal / Hojas de Vida</h1>
    {#if meta}
      <p class="text-sm text-gray-500 mt-1">{meta.total} registros</p>
    {/if}
  </div>

  <div class="max-w-md">
    <SearchInput value={search} onSearch={handleSearch} placeholder="Buscar por nombre, identificación o especialidad..." />
  </div>

  {#if loading}
    <div class="space-y-2">
      {#each Array(5) as _}
        <div class="bg-white rounded-lg p-4 shadow-sm border animate-pulse"><div class="h-4 bg-gray-200 rounded w-full"></div></div>
      {/each}
    </div>
  {:else if error && !ficha}
    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">{error}</div>
  {:else}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
      <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b">
              <tr>
                <th class="text-left px-4 py-3 font-medium text-gray-600">Nombre</th>
                <th class="text-left px-4 py-3 font-medium text-gray-600">Identificación</th>
                <th class="text-left px-4 py-3 font-medium text-gray-600">Especialidad</th>
                <th class="text-left px-4 py-3 font-medium text-gray-600">Estado</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
              {#each data as hv}
                <tr class="hover:bg-blue-50 cursor-pointer transition-colors {selectedId === hv.ind ? 'bg-blue-50' : ''}"
                    onclick={() => selectPersonal(hv.ind, hv.identificacion)}>
                  <td class="px-4 py-2.5 font-medium">{nombreCompleto(hv)}</td>
                  <td class="px-4 py-2.5">{hv.identificacion}</td>
                  <td class="px-4 py-2.5">{hv.especialidad || '-'}</td>
                  <td class="px-4 py-2.5">
                    <Badge text={hv.activo === 'S' ? 'Activo' : 'Inactivo'} color={hv.activo === 'S' ? 'green' : 'gray'} />
                  </td>
                </tr>
              {/each}
            </tbody>
          </table>
        </div>
        {#if meta}
          <div class="px-4 py-3 border-t">
            <Pagination page={meta.page} totalPages={meta.total_pages} onPageChange={handlePage} />
          </div>
        {/if}
      </div>

      <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        {#if loadingFicha}
          <div class="p-6 space-y-3 animate-pulse">
            <div class="h-6 bg-gray-200 rounded w-1/2"></div>
            <div class="h-4 bg-gray-200 rounded w-3/4"></div>
            <div class="h-4 bg-gray-200 rounded w-2/3"></div>
          </div>
        {:else if ficha}
          <div class="p-6 space-y-4">
            <div class="flex items-start gap-4">
              {#if ficha.tiene_foto}
                <img src="data:image/jpeg;base64,..." alt="" class="w-20 h-20 rounded-lg object-cover bg-gray-200" />
              {/if}
              <div>
                <h3 class="text-lg font-bold text-gray-900">{nombreCompleto(ficha)}</h3>
                <p class="text-sm text-gray-500">ID: {ficha.identificacion}</p>
                <p class="text-sm text-gray-500">{ficha.especialidad || 'Sin especialidad'}</p>
              </div>
            </div>

            <div class="grid grid-cols-2 gap-3 text-sm">
              <div><span class="font-medium text-gray-600">Teléfono:</span> {ficha.telefono || '-'}</div>
              <div><span class="font-medium text-gray-600">Email:</span> {ficha.email || '-'}</div>
              <div><span class="font-medium text-gray-600">Ciudad:</span> {ficha.ciudad || '-'}</div>
              <div><span class="font-medium text-gray-600">Estado civil:</span> {ficha.estadocivil || '-'}</div>
              <div><span class="font-medium text-gray-600">T. Profesional:</span> {ficha.tarjeta_profesional || '-'}</div>
              <div><span class="font-medium text-gray-600">Otorgado por:</span> {ficha.otorgadopor || '-'}</div>
            </div>

            {#if ficha.bitacora}
              <div>
                <p class="font-medium text-gray-600 text-sm mb-1">Bitácora</p>
                <p class="text-sm text-gray-700 bg-gray-50 p-3 rounded">{ficha.bitacora}</p>
              </div>
            {/if}
          </div>
        {:else}
          <div class="p-6 text-center text-gray-400">
            <svg class="w-12 h-12 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
            </svg>
            <p class="text-sm">Selecciona un empleado para ver su ficha</p>
          </div>
        {/if}
      </div>
    </div>
  {/if}
</div>
