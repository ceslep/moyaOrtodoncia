<script lang="ts">
  import { getDashboard } from '$lib/api';
  import type { DashboardResumen } from '$lib/types';
  import Badge from '$lib/components/Badge.svelte';
  import OrbLoader from '$lib/components/OrbLoader.svelte';
  import ThiingsIcon from '$lib/components/ThiingsIcon.svelte';

  let { onNavigate = (view: string, params?: Record<string, unknown>) => {} } = $props();

  let data = $state<DashboardResumen | null>(null);
  let loading = $state(true);
  let error = $state('');

  async function load() {
    loading = true;
    error = '';
    try {
      const res = await getDashboard();
      data = res.data;
    } catch (e) {
      error = e instanceof Error ? e.message : 'Error al cargar';
    } finally {
      loading = false;
    }
  }

  load();

  function fmt(n: number | null): string {
    if (n === null || n === undefined) return '$0';
    return '$' + n.toLocaleString('es-CO');
  }

  function estadoBadge(cita: DashboardResumen['proximas_citas'][0]) {
    if (cita.asistio === 'S') return { text: 'Atendida', color: 'green' as const };
    if (cita.confirmo === 'S') return { text: 'Confirmada', color: 'blue' as const };
    return { text: 'Pendiente', color: 'yellow' as const };
  }
</script>

<div class="space-y-5 md:space-y-6">
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
    <div>
      <h1 class="text-2xl md:text-3xl font-bold text-gray-900">Dashboard</h1>
      <p class="text-sm text-gray-500 mt-0.5">Resumen general de la clinica</p>
    </div>
    <button
      class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-medium text-blue-600 bg-blue-50 rounded-xl hover:bg-blue-100 transition-colors self-start"
      onclick={load}
    >
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
      </svg>
      Actualizar
    </button>
  </div>

  {#if loading}
    <div class="flex justify-center py-16">
      <OrbLoader size={64} state="working" />
    </div>
  {:else if error}
    <div class="bg-red-50 border border-red-200 text-red-700 px-5 py-4 rounded-2xl">
      {error}
    </div>
  {:else if data}
    <!-- Stats principales -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 sm:gap-4">
      <button
        class="bg-gradient-to-br from-blue-600 to-blue-800 rounded-2xl p-4 sm:p-5 text-white text-left hover:shadow-lg hover:scale-[1.02] transition-all duration-200 min-h-[110px]"
        onclick={() => onNavigate('pacientes')}
      >
        <div class="flex items-center justify-between mb-3">
          <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center overflow-hidden">
            <ThiingsIcon name="patient" size={26} alt="Pacientes" />
          </div>
          <span class="text-3xl sm:text-4xl font-bold">{data.pacientes_activos.toLocaleString()}</span>
        </div>
        <p class="text-blue-100 text-sm font-medium">Pacientes Activos</p>
      </button>

      <button
        class="bg-gradient-to-br from-emerald-500 to-emerald-700 rounded-2xl p-4 sm:p-5 text-white text-left hover:shadow-lg hover:scale-[1.02] transition-all duration-200 min-h-[110px]"
        onclick={() => onNavigate('agenda')}
      >
        <div class="flex items-center justify-between mb-3">
          <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center overflow-hidden">
            <ThiingsIcon name="calendar" size={26} alt="Citas" />
          </div>
          <span class="text-3xl sm:text-4xl font-bold">{data.citas_hoy}</span>
        </div>
        <p class="text-emerald-100 text-sm font-medium">Citas Hoy</p>
      </button>

      <button
        class="bg-gradient-to-br from-purple-500 to-purple-700 rounded-2xl p-4 sm:p-5 text-white text-left hover:shadow-lg hover:scale-[1.02] transition-all duration-200 min-h-[110px]"
        onclick={() => onNavigate('agenda')}
      >
        <div class="flex items-center justify-between mb-3">
          <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center overflow-hidden">
            <ThiingsIcon name="calendar-plus" size={26} alt="Citas Semana" />
          </div>
          <span class="text-3xl sm:text-4xl font-bold">{data.citas_semana}</span>
        </div>
        <p class="text-purple-100 text-sm font-medium">Citas Semana</p>
      </button>
    </div>

    <!-- Stats secundarios -->
    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 sm:gap-4">
      <div class="bg-white rounded-2xl p-4 border border-gray-200 hover:shadow-md transition-shadow">
        <div class="flex items-center gap-2.5 mb-2">
          <div class="w-9 h-9 rounded-xl bg-indigo-50 flex items-center justify-center overflow-hidden">
            <ThiingsIcon name="stethoscope" size={20} alt="Citas Manana" />
          </div>
          <p class="text-xs sm:text-sm text-gray-500 font-medium">Citas Manana</p>
        </div>
        <p class="text-2xl sm:text-3xl font-bold text-gray-900">{data.citas_manana}</p>
      </div>

      <div class="bg-white rounded-2xl p-4 border border-gray-200 hover:shadow-md transition-shadow">
        <div class="flex items-center gap-2.5 mb-2">
          <div class="w-9 h-9 rounded-xl bg-emerald-50 flex items-center justify-center overflow-hidden">
            <ThiingsIcon name="coins" size={20} alt="Abonos Hoy" />
          </div>
          <p class="text-xs sm:text-sm text-gray-500 font-medium">Abonos Hoy</p>
        </div>
        <p class="text-2xl sm:text-3xl font-bold text-gray-900">{fmt(data.abonos_hoy)}</p>
      </div>

      <div class="bg-white rounded-2xl p-4 border border-gray-200 hover:shadow-md transition-shadow col-span-2 sm:col-span-1">
        <div class="flex items-center gap-2.5 mb-2">
          <div class="w-9 h-9 rounded-xl bg-teal-50 flex items-center justify-center overflow-hidden">
            <ThiingsIcon name="dentist" size={20} alt="Nuevos" />
          </div>
          <p class="text-xs sm:text-sm text-gray-500 font-medium">Nuevos este Mes</p>
        </div>
        <p class="text-2xl sm:text-3xl font-bold text-gray-900">{data.nuevos_mes}</p>
      </div>
    </div>

    <!-- Proximas citas -->
    {#if data.proximas_citas.length > 0}
      <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
        <div class="px-4 sm:px-5 py-3.5 sm:py-4 border-b border-gray-100 flex items-center gap-2.5">
          <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center overflow-hidden">
            <ThiingsIcon name="calendar" size={18} alt="Citas" />
          </div>
          <h2 class="font-semibold text-gray-900 text-sm sm:text-base">Proximas Citas</h2>
        </div>

        <div class="overflow-x-auto">
          <table class="w-full text-sm">
            <thead class="bg-gray-50 hidden sm:table-header-group">
              <tr>
                <th class="text-left px-5 py-3 font-semibold text-gray-500 text-xs uppercase tracking-wider">Fecha</th>
                <th class="text-left px-5 py-3 font-semibold text-gray-500 text-xs uppercase tracking-wider">Hora</th>
                <th class="text-left px-5 py-3 font-semibold text-gray-500 text-xs uppercase tracking-wider">Paciente</th>
                <th class="text-left px-5 py-3 font-semibold text-gray-500 text-xs uppercase tracking-wider">Procedimiento</th>
                <th class="text-left px-5 py-3 font-semibold text-gray-500 text-xs uppercase tracking-wider">Estado</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
              {#each data.proximas_citas as cita}
                <tr class="hover:bg-gray-50 cursor-pointer"
                    onclick={() => onNavigate('ficha', { ind: cita.paciente })}>
                  <td class="px-4 sm:px-5 py-3 hidden sm:table-cell">{cita.fecha}</td>
                  <td class="px-4 sm:px-5 py-3 hidden sm:table-cell">{cita.horas}</td>
                  <td class="px-4 sm:px-5 py-3 font-medium text-gray-900">{cita.nombres}</td>
                  <td class="px-4 sm:px-5 py-3 text-gray-600 hidden md:table-cell">{cita.procedimiento}</td>
                  <td class="px-4 sm:px-5 py-3">
                    <div class="flex items-center gap-2">
                      <span class="text-xs text-gray-500 sm:hidden">{cita.fecha} {cita.horas}</span>
                      <Badge {...estadoBadge(cita)} />
                    </div>
                  </td>
                </tr>
              {/each}
            </tbody>
          </table>
        </div>
      </div>
    {/if}
  {/if}
</div>
