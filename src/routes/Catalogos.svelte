<script lang="ts">
  import { getProcedimientos, getEspecialidades, getEntidades } from '$lib/api';
  import type { Procedimiento, Especialidad, Entidad } from '$lib/types';
  import SearchInput from '$lib/components/SearchInput.svelte';
  import Pagination from '$lib/components/Pagination.svelte';
  import OrbLoader from '$lib/components/OrbLoader.svelte';

  let activeTab = $state('procedimientos');
  let search = $state('');

  let procedimientos = $state<Procedimiento[]>([]);
  let procMeta = $state<{ page: number; total_pages: number; total: number } | null>(null);
  let procPage = $state(1);

  let especialidades = $state<Especialidad[]>([]);
  let entidades = $state<Entidad[]>([]);

  let loading = $state(false);
  let error = $state('');

  async function loadProcedimientos() {
    loading = true;
    error = '';
    try {
      const res = await getProcedimientos(search, procPage);
      procedimientos = res.data;
      procMeta = res.meta;
    } catch (e) {
      error = e instanceof Error ? e.message : 'Error';
    } finally {
      loading = false;
    }
  }

  async function loadEspecialidades() {
    loading = true;
    error = '';
    try {
      const res = await getEspecialidades(search);
      especialidades = res.data;
    } catch (e) {
      error = e instanceof Error ? e.message : 'Error';
    } finally {
      loading = false;
    }
  }

  async function loadEntidades() {
    loading = true;
    error = '';
    try {
      const res = await getEntidades(search);
      entidades = res.data;
    } catch (e) {
      error = e instanceof Error ? e.message : 'Error';
    } finally {
      loading = false;
    }
  }

  function load() {
    if (activeTab === 'procedimientos') loadProcedimientos();
    else if (activeTab === 'especialidades') loadEspecialidades();
    else loadEntidades();
  }

  load();

  function switchTab(id: string) {
    activeTab = id;
    search = '';
    load();
  }

  function handleSearch(v: string) {
    search = v;
    load();
  }
</script>

<div class="space-y-5">
  <h1 class="text-2xl md:text-3xl font-bold text-gray-900">Catálogos</h1>

  <div class="bg-white rounded-2xl shadow-modern border border-gray-100/80 overflow-hidden">
    <div class="border-b border-gray-100 px-4 overflow-x-auto">
      <nav class="flex gap-1 -mb-px min-w-max">
        {#each [{ id: 'procedimientos', label: 'Procedimientos' }, { id: 'especialidades', label: 'Especialidades' }, { id: 'entidades', label: 'Entidades' }] as tab}
          <button
            class="px-4 py-3 text-sm font-medium whitespace-nowrap border-b-2 transition-colors
              {activeTab === tab.id ? 'border-primary-600 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700'}"
            onclick={() => switchTab(tab.id)}
          >
            {tab.label}
          </button>
        {/each}
      </nav>
    </div>

    <div class="p-4 border-b border-gray-100">
      <div class="max-w-md">
        <SearchInput value={search} onSearch={handleSearch} placeholder="Buscar..." />
      </div>
    </div>

    {#if loading}
      <div class="flex justify-center py-16">
        <OrbLoader size={56} state="shaping" />
      </div>
    {:else if error}
      <div class="p-4">
        <div class="bg-red-50 border border-red-200 text-red-700 px-5 py-4 rounded-2xl flex items-center gap-3">
          <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          {error}
        </div>
      </div>
    {:else}
      <div class="overflow-x-auto table-responsive">
        {#if activeTab === 'procedimientos'}
          <table class="w-full text-sm">
            <thead class="bg-gray-50/80 border-b">
              <tr>
                <th class="text-left px-5 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider">Código</th>
                <th class="text-left px-5 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider">Nombre</th>
                <th class="text-left px-5 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider">Duración</th>
                <th class="text-left px-5 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider">Tipo</th>
                <th class="text-left px-5 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider">Color</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100/80">
              {#each procedimientos as p}
                <tr class="hover:bg-gray-50/50 transition-colors">
                  <td class="px-5 py-3 font-mono text-xs text-gray-700" data-label="Código">{p.codigo || '-'}</td>
                  <td class="px-5 py-3 font-medium text-gray-900" data-label="Nombre">{p.nombre || '-'}</td>
                  <td class="px-5 py-3 text-gray-700" data-label="Duración">{p.duracion || '-'}</td>
                  <td class="px-5 py-3 text-gray-700" data-label="Tipo">{p.tipocita || '-'}</td>
                  <td class="px-5 py-3" data-label="Color">
                    {#if p.color}
                      <span class="inline-block w-5 h-5 rounded-lg shadow-inner" style="background-color: {p.color}"></span>
                    {:else}
                      <span class="text-gray-400">-</span>
                    {/if}
                  </td>
                </tr>
              {/each}
            </tbody>
          </table>
          {#if procMeta}
            <div class="px-5 py-4 border-t border-gray-100">
              <Pagination page={procMeta.page} totalPages={procMeta.total_pages} onPageChange={(p: number) => { procPage = p; loadProcedimientos(); }} />
            </div>
          {/if}
        {:else if activeTab === 'especialidades'}
          <table class="w-full text-sm">
            <thead class="bg-gray-50/80 border-b">
              <tr>
                <th class="text-left px-5 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider">Nombre</th>
                <th class="text-left px-5 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider">Código</th>
                <th class="text-left px-5 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider">Descripción</th>
                <th class="text-left px-5 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider">Grupo</th>
                <th class="text-left px-5 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider">Activa</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100/80">
              {#each especialidades as e}
                <tr class="hover:bg-gray-50/50 transition-colors">
                  <td class="px-5 py-3 font-medium text-gray-900" data-label="Nombre">{e.nombre || '-'}</td>
                  <td class="px-5 py-3 font-mono text-xs text-gray-700" data-label="Código">{e.codigo || '-'}</td>
                  <td class="px-5 py-3 text-gray-700" data-label="Descripción">{e.descripcion || '-'}</td>
                  <td class="px-5 py-3 text-gray-700" data-label="Grupo">{e.grupo || '-'}</td>
                  <td class="px-5 py-3" data-label="Activa">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {e.activa === 'S' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600'}">
                      {e.activa === 'S' ? 'Sí' : 'No'}
                    </span>
                  </td>
                </tr>
              {/each}
            </tbody>
          </table>
        {:else}
          <table class="w-full text-sm">
            <thead class="bg-gray-50/80 border-b">
              <tr>
                <th class="text-left px-5 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider">Nombre</th>
                <th class="text-left px-5 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider">Código</th>
                <th class="text-left px-5 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider">NIT</th>
                <th class="text-left px-5 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider">Ciudad</th>
                <th class="text-left px-5 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider">Teléfono</th>
                <th class="text-left px-5 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider">Email</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100/80">
              {#each entidades as e}
                <tr class="hover:bg-gray-50/50 transition-colors">
                  <td class="px-5 py-3 font-medium text-gray-900" data-label="Nombre">{e.nombres || '-'}</td>
                  <td class="px-5 py-3 font-mono text-xs text-gray-700" data-label="Código">{e.nocodigo || '-'}</td>
                  <td class="px-5 py-3 text-gray-700" data-label="NIT">{e.nit || '-'}</td>
                  <td class="px-5 py-3 text-gray-700" data-label="Ciudad">{e.ciudad || '-'}</td>
                  <td class="px-5 py-3 text-gray-700" data-label="Teléfono">{e.telefono || '-'}</td>
                  <td class="px-5 py-3 text-gray-700" data-label="Email">{e.email || '-'}</td>
                </tr>
              {/each}
            </tbody>
          </table>
        {/if}
      </div>
    {/if}
  </div>
</div>
