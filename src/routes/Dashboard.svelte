<script lang="ts">
  import { getDashboard } from '$lib/api';
  import type { DashboardResumen } from '$lib/types';
  import Badge from '$lib/components/Badge.svelte';
  import OrbLoader from '$lib/components/OrbLoader.svelte';
  import ThiingsIcon from '$lib/components/ThiingsIcon.svelte';
  import MetricCard from '$lib/components/MetricCard.svelte';
  import GlassCard from '$lib/components/GlassCard.svelte';

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

<div class="space-y-6">
  <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
    <div>
      <h1 class="text-2xl md:text-3xl font-bold tracking-tight text-slate-900">Dashboard</h1>
      <p class="text-sm text-slate-500 mt-1">Resumen general de la clinica</p>
    </div>
    <button
      class="group inline-flex items-center gap-2 px-4 py-2.5 self-start rounded-xl focus-ring
        text-sm font-semibold text-white
        bg-gradient-to-br from-primary-600 to-primary-700
        shadow-[var(--shadow-glow-primary)]
        transition-all duration-200 ease-out
        hover:from-primary-500 hover:to-primary-700 hover:-translate-y-0.5
        active:translate-y-0 active:scale-[0.98]
        disabled:opacity-70 disabled:cursor-wait disabled:translate-y-0"
      onclick={load}
      disabled={loading}
    >
      <svg
        class="w-4 h-4 transition-transform duration-300 ease-out {loading ? 'animate-spin' : 'group-hover:rotate-180'}"
        fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"
      >
        <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
      </svg>
      {loading ? 'Actualizando...' : 'Actualizar'}
    </button>
  </div>

  {#if loading}
    <!-- Skeleton con la misma retícula que el contenido real: sin salto de layout -->
    <div class="space-y-6">
      <div class="flex justify-center py-6">
        <OrbLoader size={64} state="working" />
      </div>
      <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        {#each [0, 1, 2] as i}
          <div class="skeleton h-[132px] rounded-2xl" style="animation-delay: {i * 80}ms"></div>
        {/each}
      </div>
      <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
        {#each [0, 1, 2] as i}
          <div class="skeleton h-[112px] rounded-2xl" style="animation-delay: {i * 80}ms"></div>
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
  {:else if data}
    <!-- Stats principales -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
      <MetricCard
        icon="patient"
        label="Pacientes Activos"
        value={data.pacientes_activos.toLocaleString()}
        tone="primary"
        delay={0}
        onclick={() => onNavigate('pacientes')}
      />
      <MetricCard
        icon="calendar"
        label="Citas Hoy"
        value={data.citas_hoy}
        tone="health"
        empty={!data.citas_hoy}
        hint={data.citas_hoy ? '' : 'Sin citas programadas hoy'}
        delay={1}
        onclick={() => onNavigate('agenda')}
      />
      <MetricCard
        icon="calendar-plus"
        label="Citas Semana"
        value={data.citas_semana}
        tone="accent"
        empty={!data.citas_semana}
        hint={data.citas_semana ? '' : 'Sin citas esta semana'}
        delay={2}
        onclick={() => onNavigate('agenda')}
      />
    </div>

    <!-- Stats secundarios: mismo lenguaje visual, jerarquia menor -->
    <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
      <MetricCard
        icon="stethoscope"
        label="Citas Manana"
        value={data.citas_manana}
        tone="indigo"
        empty={!data.citas_manana}
        hint={data.citas_manana ? '' : 'Agenda libre'}
        delay={3}
      />
      <MetricCard
        icon="coins"
        label="Abonos Hoy"
        value={fmt(data.abonos_hoy)}
        tone="emerald"
        empty={!data.abonos_hoy}
        hint={data.abonos_hoy ? '' : 'Sin abonos registrados'}
        delay={4}
      />
      <MetricCard
        icon="dentist"
        label="Nuevos este Mes"
        value={data.nuevos_mes}
        tone="teal"
        empty={!data.nuevos_mes}
        hint={data.nuevos_mes ? '' : 'Aun sin ingresos'}
        delay={5}
        class="col-span-2 sm:col-span-1"
      />
    </div>

    <!-- Proximas citas -->
    {#if data.proximas_citas.length > 0}
      <GlassCard padding="p-0" delay={6} class="overflow-hidden">
        <div class="px-4 sm:px-5 py-4 border-b border-slate-200/70 flex items-center gap-3">
          <div class="w-9 h-9 rounded-xl bg-primary-600/10 border border-primary-600/15 flex items-center justify-center overflow-hidden">
            <ThiingsIcon name="calendar" size={20} alt="" />
          </div>
          <h2 class="font-semibold tracking-tight text-slate-900 text-sm sm:text-base">Proximas Citas</h2>
          <span class="num ml-auto text-xs font-semibold text-slate-500 bg-slate-500/10 px-2 py-1 rounded-full">
            {data.proximas_citas.length}
          </span>
        </div>

        <div class="overflow-x-auto">
          <table class="w-full text-sm">
            <thead class="hidden sm:table-header-group bg-slate-50/80 backdrop-blur-sm">
              <tr>
                <th class="text-left px-5 py-3 font-semibold text-slate-500 text-[11px] uppercase tracking-wider">Fecha</th>
                <th class="text-left px-5 py-3 font-semibold text-slate-500 text-[11px] uppercase tracking-wider">Hora</th>
                <th class="text-left px-5 py-3 font-semibold text-slate-500 text-[11px] uppercase tracking-wider">Paciente</th>
                <th class="text-left px-5 py-3 font-semibold text-slate-500 text-[11px] uppercase tracking-wider">Procedimiento</th>
                <th class="text-left px-5 py-3 font-semibold text-slate-500 text-[11px] uppercase tracking-wider">Estado</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-200/60">
              {#each data.proximas_citas as cita}
                <tr class="group cursor-pointer odd:bg-white/40 transition-colors duration-150 ease-out hover:bg-primary-600/6"
                    onclick={() => onNavigate('ficha', { ind: cita.paciente })}>
                  <td class="num px-4 sm:px-5 py-3.5 text-slate-600 hidden sm:table-cell">{cita.fecha}</td>
                  <td class="num px-4 sm:px-5 py-3.5 text-slate-600 hidden sm:table-cell">{cita.horas}</td>
                  <td class="px-4 sm:px-5 py-3.5 font-semibold text-slate-900 group-hover:text-primary-700 transition-colors">{cita.nombres}</td>
                  <td class="px-4 sm:px-5 py-3.5 text-slate-600 hidden md:table-cell">{cita.procedimiento}</td>
                  <td class="px-4 sm:px-5 py-3.5">
                    <div class="flex items-center gap-2">
                      <span class="num text-xs text-slate-500 sm:hidden">{cita.fecha} {cita.horas}</span>
                      <Badge {...estadoBadge(cita)} dot />
                    </div>
                  </td>
                </tr>
              {/each}
            </tbody>
          </table>
        </div>
      </GlassCard>
    {/if}
  {/if}
</div>
