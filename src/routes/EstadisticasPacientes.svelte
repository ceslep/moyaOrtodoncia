<script lang="ts">
  import { onMount, onDestroy } from 'svelte';
  import { getEstadisticasPacientes } from '$lib/api';
  import type { EstadisticasPaciente } from '$lib/types';
  import GlassCard from '$lib/components/GlassCard.svelte';
  import OrbLoader from '$lib/components/OrbLoader.svelte';
  import ThiingsIcon from '$lib/components/ThiingsIcon.svelte';
  import { Chart, registerables } from 'chart.js';
  import ChartDataLabels from 'chartjs-plugin-datalabels';
  import L from 'leaflet';
  import 'leaflet/dist/leaflet.css';
  import { findCoordByNombre, findCoordByCodigo } from '$lib/data/municipios-coords';

  Chart.register(...registerables, ChartDataLabels);

  let { onNavigate = (view: string, params?: Record<string, unknown>) => {} } = $props();

  let data = $state<EstadisticasPaciente | null>(null);
  let loading = $state(true);
  let error = $state('');

  const charts: Chart[] = [];
  let map: L.Map | null = null;

  const meses = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];

  const palette = [
    '#3b82f6', '#10b981', '#8b5cf6', '#f59e0b', '#ef4444',
    '#06b6d4', '#ec4899', '#14b8a6', '#f97316', '#6366f1',
    '#84cc16', '#e11d48', '#0ea5e9', '#a855f7', '#22c55e'
  ];

  function getPalette(n: number): string[] {
    return Array.from({ length: n }, (_, i) => palette[i % palette.length]);
  }

  async function load() {
    loading = true;
    error = '';
    try {
      const res = await getEstadisticasPacientes();
      data = res.data;
      setTimeout(renderCharts, 100);
    } catch (e) {
      error = e instanceof Error ? e.message : 'Error al cargar estadisticas';
    } finally {
      loading = false;
    }
  }

  function renderCharts() {
    charts.forEach(c => c.destroy());
    charts.length = 0;
    if (!data) return;

    charts.push(makeBarChart('chartCiudad', data.por_ciudad.slice(0, 12).map(d => d.ciudad), data.por_ciudad.slice(0, 12).map(d => d.cantidad), 'Pacientes por Ciudad'));
    charts.push(makeDoughnut('chartGenero', data.por_genero.map(d => d.genero), data.por_genero.map(d => d.cantidad)));
    charts.push(makeBarChart('chartEdad', data.por_edad.map(d => d.rango_edad), data.por_edad.map(d => d.cantidad), 'Distribucion por Edad'));
    charts.push(makeHorizontalBar('chartOcupacion', data.por_ocupacion.map(d => d.ocupacion), data.por_ocupacion.map(d => d.cantidad)));
    charts.push(makeDoughnut('chartEstadoCivil', data.por_estado_civil.map(d => d.estado_civil), data.por_estado_civil.map(d => d.cantidad)));
    charts.push(makeLineChart('chartAnio', data.por_anio.map(d => String(d.anio)), data.por_anio.map(d => d.cantidad), 'Pacientes Nuevos por Anio'));
    charts.push(makeBarChart('chartMes', data.por_mes.map(d => meses[d.mes - 1] || String(d.mes)), data.por_mes.map(d => d.cantidad), 'Pacientes Nuevos por Mes (Anio Actual)'));
    charts.push(makeDoughnut('chartPlan', data.por_plan.map(d => d.plan_pago), data.por_plan.map(d => d.cantidad)));

    initMap();
  }

  function initMap() {
    if (!data) return;
    const container = document.getElementById('mapaCalor');
    if (!container) return;

    if (map) { map.remove(); map = null; }

    map = L.map(container, { scrollWheelZoom: true }).setView([4.5, -74.0], 6);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution: '&copy; OpenStreetMap',
      maxZoom: 18,
    }).addTo(map);

    const ciudades = data.por_ciudad;
    if (ciudades.length === 0) return;
    const maxVal = Math.max(...ciudades.map(d => d.cantidad));

    const markers: L.CircleMarker[] = [];

    for (const ciudad of ciudades) {
      let coord = findCoordByNombre(ciudad.ciudad);
      if (!coord) {
        const parts = ciudad.ciudad.split('/');
        if (parts.length > 1) coord = findCoordByNombre(parts[0]);
      }
      if (!coord) continue;

      const ratio = maxVal > 0 ? ciudad.cantidad / maxVal : 0;
      const radius = 8 + ratio * 22;
      const color = ratio > 0.7 ? '#dc2626' : ratio > 0.5 ? '#f97316' : ratio > 0.3 ? '#eab308' : ratio > 0.15 ? '#22c55e' : '#06b6d4';

      const circle = L.circleMarker([coord.lat, coord.lng], {
        radius,
        fillColor: color,
        color: '#fff',
        weight: 2,
        opacity: 1,
        fillOpacity: 0.75,
      }).addTo(map);

      circle.bindPopup(`<div style="text-align:center"><b>${ciudad.ciudad}</b><br><span style="font-size:18px;font-weight:bold">${ciudad.cantidad}</span> pacientes</div>`);
      markers.push(circle);
    }

    if (markers.length > 0) {
      const group = L.featureGroup(markers);
      map.fitBounds(group.getBounds().pad(0.1));
    }
  }

  function makeBarChart(id: string, labels: string[], values: number[], title: string): Chart {
    const ctx = document.getElementById(id) as HTMLCanvasElement;
    const total = values.reduce((a, b) => a + b, 0);
    return new Chart(ctx, {
      type: 'bar',
      data: {
        labels,
        datasets: [{
          label: title,
          data: values,
          backgroundColor: getPalette(values.length),
          borderRadius: 6,
          borderSkipped: false,
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: { display: false },
          datalabels: {
            anchor: 'end',
            align: 'top',
            color: '#334155',
            font: { weight: 'bold', size: 11 },
            formatter: (v: number) => v.toLocaleString(),
          }
        },
        scales: {
          y: { beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { font: { size: 11 } } },
          x: { grid: { display: false }, ticks: { maxRotation: 45, font: { size: 10 } } }
        }
      },
      plugins: [ChartDataLabels]
    });
  }

  function makeHorizontalBar(id: string, labels: string[], values: number[]): Chart {
    const ctx = document.getElementById(id) as HTMLCanvasElement;
    const total = values.reduce((a, b) => a + b, 0);
    return new Chart(ctx, {
      type: 'bar',
      data: {
        labels,
        datasets: [{
          label: 'Pacientes',
          data: values,
          backgroundColor: getPalette(values.length),
          borderRadius: 6,
          borderSkipped: false,
        }]
      },
      options: {
        indexAxis: 'y',
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: { display: false },
          datalabels: {
            anchor: 'end',
            align: 'end',
            color: '#334155',
            font: { weight: 'bold', size: 11 },
            formatter: (v: number) => `${v} (${((v / total) * 100).toFixed(1)}%)`,
          }
        },
        scales: {
          x: { beginAtZero: true, grid: { color: '#f1f5f9' } },
          y: { grid: { display: false }, ticks: { font: { size: 11 } } }
        }
      },
      plugins: [ChartDataLabels]
    });
  }

  function makeDoughnut(id: string, labels: string[], values: number[]): Chart {
    const ctx = document.getElementById(id) as HTMLCanvasElement;
    const total = values.reduce((a, b) => a + b, 0);
    return new Chart(ctx, {
      type: 'doughnut',
      data: {
        labels,
        datasets: [{
          data: values,
          backgroundColor: getPalette(values.length),
          borderWidth: 2,
          borderColor: '#ffffff',
          hoverOffset: 8,
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: { position: 'bottom', labels: { padding: 14, font: { size: 11 } } },
          datalabels: {
            color: '#fff',
            font: { weight: 'bold', size: 12 },
            formatter: (v: number) => {
              const pct = (v / total) * 100;
              return pct >= 5 ? `${pct.toFixed(1)}%` : '';
            },
          }
        }
      },
      plugins: [ChartDataLabels]
    });
  }

  function makeLineChart(id: string, labels: string[], values: number[], title: string): Chart {
    const ctx = document.getElementById(id) as HTMLCanvasElement;
    return new Chart(ctx, {
      type: 'line',
      data: {
        labels,
        datasets: [{
          label: title,
          data: values,
          borderColor: '#3b82f6',
          backgroundColor: 'rgba(59,130,246,0.1)',
          fill: true,
          tension: 0.4,
          pointBackgroundColor: '#3b82f6',
          pointBorderColor: '#fff',
          pointBorderWidth: 2,
          pointRadius: 5,
          pointHoverRadius: 7,
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: { display: false },
          datalabels: {
            anchor: 'end',
            align: 'top',
            color: '#3b82f6',
            font: { weight: 'bold', size: 11 },
            formatter: (v: number) => v.toLocaleString(),
          }
        },
        scales: {
          y: { beginAtZero: true, grid: { color: '#f1f5f9' } },
          x: { grid: { display: false } }
        }
      },
      plugins: [ChartDataLabels]
    });
  }

  onMount(load);
  onDestroy(() => { charts.forEach(c => c.destroy()); if (map) { map.remove(); map = null; } });
</script>

<div class="space-y-6">
  <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
    <div>
      <h1 class="text-2xl md:text-3xl font-bold tracking-tight text-slate-900">Estadisticas de Pacientes</h1>
      <p class="text-sm text-slate-500 mt-1">Analisis demographic y geografico de pacientes activos</p>
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
      <svg class="w-4 h-4 transition-transform duration-300 ease-out {loading ? 'animate-spin' : 'group-hover:rotate-180'}"
        fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
      </svg>
      {loading ? 'Cargando...' : 'Actualizar'}
    </button>
  </div>

  {#if loading}
    <div class="space-y-6">
      <div class="flex justify-center py-6">
        <OrbLoader size={64} state="working" />
      </div>
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        {#each [0, 1, 2, 3] as i}
          <div class="skeleton h-[300px] rounded-2xl" style="animation-delay: {i * 80}ms"></div>
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
    <!-- Resumen general -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
      <GlassCard padding="p-4" delay={0}>
        <div class="flex items-center gap-3">
          <div class="w-10 h-10 rounded-xl bg-primary-600/10 flex items-center justify-center">
            <ThiingsIcon name="patient" size={22} alt="" />
          </div>
          <div>
            <p class="text-2xl font-bold text-slate-900">{data.resumen.total_pacientes.toLocaleString()}</p>
            <p class="text-xs text-slate-500 font-medium">Total Pacientes</p>
          </div>
        </div>
      </GlassCard>
      <GlassCard padding="p-4" delay={1}>
        <div class="flex items-center gap-3">
          <div class="w-10 h-10 rounded-xl bg-emerald-600/10 flex items-center justify-center">
            <ThiingsIcon name="hospital" size={22} alt="" />
          </div>
          <div>
            <p class="text-2xl font-bold text-slate-900">{data.resumen.total_ciudades}</p>
            <p class="text-xs text-slate-500 font-medium">Ciudades</p>
          </div>
        </div>
      </GlassCard>
      <GlassCard padding="p-4" delay={2}>
        <div class="flex items-center gap-3">
          <div class="w-10 h-10 rounded-xl bg-violet-600/10 flex items-center justify-center">
            <ThiingsIcon name="charts" size={22} alt="" />
          </div>
          <div>
            <p class="text-2xl font-bold text-slate-900">{data.resumen.edad_promedio}</p>
            <p class="text-xs text-slate-500 font-medium">Edad Promedio</p>
          </div>
        </div>
      </GlassCard>
      <GlassCard padding="p-4" delay={3}>
        <div class="flex items-center gap-3">
          <div class="w-10 h-10 rounded-xl bg-amber-600/10 flex items-center justify-center">
            <ThiingsIcon name="calendar" size={22} alt="" />
          </div>
          <div>
            <p class="text-lg font-bold text-slate-900">{data.resumen.primer_paciente || 'N/A'}</p>
            <p class="text-xs text-slate-500 font-medium">Primer Paciente</p>
          </div>
        </div>
      </GlassCard>
    </div>

    <!-- Fila 1: Ciudad + Genero -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
      <GlassCard padding="p-0" delay={4} class="overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-200/70 flex items-center gap-3">
          <div class="w-8 h-8 rounded-lg bg-primary-600/10 flex items-center justify-center">
            <ThiingsIcon name="hospital" size={18} alt="" />
          </div>
          <h2 class="font-semibold text-slate-900 text-sm">Pacientes por Ciudad</h2>
        </div>
        <div class="p-4" style="height: 320px">
          <canvas id="chartCiudad"></canvas>
        </div>
      </GlassCard>

      <GlassCard padding="p-0" delay={5} class="overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-200/70 flex items-center gap-3">
          <div class="w-8 h-8 rounded-lg bg-emerald-600/10 flex items-center justify-center">
            <ThiingsIcon name="group" size={18} alt="" />
          </div>
          <h2 class="font-semibold text-slate-900 text-sm">Distribucion por Genero</h2>
        </div>
        <div class="p-4 flex items-center justify-center" style="height: 320px">
          <div style="width: 260px; height: 260px">
            <canvas id="chartGenero"></canvas>
          </div>
        </div>
      </GlassCard>
    </div>

    <!-- Fila 2: Edad + Ocupacion -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
      <GlassCard padding="p-0" delay={6} class="overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-200/70 flex items-center gap-3">
          <div class="w-8 h-8 rounded-lg bg-violet-600/10 flex items-center justify-center">
            <ThiingsIcon name="charts" size={18} alt="" />
          </div>
          <h2 class="font-semibold text-slate-900 text-sm">Distribucion por Edad</h2>
        </div>
        <div class="p-4" style="height: 320px">
          <canvas id="chartEdad"></canvas>
        </div>
      </GlassCard>

      <GlassCard padding="p-0" delay={7} class="overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-200/70 flex items-center gap-3">
          <div class="w-8 h-8 rounded-lg bg-amber-600/10 flex items-center justify-center">
            <ThiingsIcon name="wallet" size={18} alt="" />
          </div>
          <h2 class="font-semibold text-slate-900 text-sm">Top 15 Ocupaciones</h2>
        </div>
        <div class="p-4" style="height: 360px">
          <canvas id="chartOcupacion"></canvas>
        </div>
      </GlassCard>
    </div>

    <!-- Fila 3: Estado Civil + Plan -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
      <GlassCard padding="p-0" delay={8} class="overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-200/70 flex items-center gap-3">
          <div class="w-8 h-8 rounded-lg bg-pink-600/10 flex items-center justify-center">
            <ThiingsIcon name="profile" size={18} alt="" />
          </div>
          <h2 class="font-semibold text-slate-900 text-sm">Estado Civil</h2>
        </div>
        <div class="p-4 flex items-center justify-center" style="height: 320px">
          <div style="width: 260px; height: 260px">
            <canvas id="chartEstadoCivil"></canvas>
          </div>
        </div>
      </GlassCard>

      <GlassCard padding="p-0" delay={9} class="overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-200/70 flex items-center gap-3">
          <div class="w-8 h-8 rounded-lg bg-cyan-600/10 flex items-center justify-center">
            <ThiingsIcon name="money" size={18} alt="" />
          </div>
          <h2 class="font-semibold text-slate-900 text-sm">Planes de Pago</h2>
        </div>
        <div class="p-4 flex items-center justify-center" style="height: 320px">
          <div style="width: 260px; height: 260px">
            <canvas id="chartPlan"></canvas>
          </div>
        </div>
      </GlassCard>
    </div>

    <!-- Fila 4: Tendencia anual + mensual -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
      <GlassCard padding="p-0" delay={10} class="overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-200/70 flex items-center gap-3">
          <div class="w-8 h-8 rounded-lg bg-indigo-600/10 flex items-center justify-center">
            <ThiingsIcon name="calendar-plus" size={18} alt="" />
          </div>
          <h2 class="font-semibold text-slate-900 text-sm">Tendencia de Ingreso por Anio</h2>
        </div>
        <div class="p-4" style="height: 320px">
          <canvas id="chartAnio"></canvas>
        </div>
      </GlassCard>

      <GlassCard padding="p-0" delay={11} class="overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-200/70 flex items-center gap-3">
          <div class="w-8 h-8 rounded-lg bg-teal-600/10 flex items-center justify-center">
            <ThiingsIcon name="calendar" size={18} alt="" />
          </div>
          <h2 class="font-semibold text-slate-900 text-sm">Ingresos por Mes (Anio Actual)</h2>
        </div>
        <div class="p-4" style="height: 320px">
          <canvas id="chartMes"></canvas>
        </div>
      </GlassCard>
    </div>

    <!-- Mapa de calor real con Leaflet -->
    <GlassCard padding="p-0" delay={12} class="overflow-hidden">
      <div class="px-5 py-4 border-b border-slate-200/70 flex items-center gap-3">
        <div class="w-8 h-8 rounded-lg bg-red-600/10 flex items-center justify-center">
          <ThiingsIcon name="hospital" size={18} alt="" />
        </div>
        <h2 class="font-semibold text-slate-900 text-sm">Mapa de Calor por Municipio</h2>
        <span class="ml-auto text-xs text-slate-400">Arrastra para explorar, click en circulos para ver detalles</span>
      </div>
      <div id="mapaCalor" class="w-full" style="height: 500px; z-index: 0;"></div>
      <!-- Leyenda -->
      <div class="px-5 py-3 border-t border-slate-200/70 flex flex-wrap items-center justify-center gap-3 text-[11px] text-slate-500">
        <span class="font-medium mr-1">Densidad:</span>
        <div class="flex items-center gap-1"><span class="w-3 h-3 rounded-full bg-cyan-400"></span> Baja</div>
        <div class="flex items-center gap-1"><span class="w-3 h-3 rounded-full bg-green-400"></span> Media-Baja</div>
        <div class="flex items-center gap-1"><span class="w-3 h-3 rounded-full bg-yellow-400"></span> Media</div>
        <div class="flex items-center gap-1"><span class="w-3 h-3 rounded-full bg-orange-400"></span> Alta</div>
        <div class="flex items-center gap-1"><span class="w-3 h-3 rounded-full bg-red-500"></span> Muy Alta</div>
      </div>
    </GlassCard>
  {/if}
</div>
