<script lang="ts">
  import { getPaciente, getCitasPaciente, getCitasCanceladas, getEvoluciones, getAbonosPaciente, getPagos, getDetallesPagos, getFotoUrl } from '$lib/api';
  import type { Paciente, Cita, CitaCancelada, Evolucion, Abono, Pago, DetallePago } from '$lib/types';
  import Tabs from '$lib/components/Tabs.svelte';
  import Badge from '$lib/components/Badge.svelte';
  import Toast from '$lib/components/Toast.svelte';
  import OrbLoader from '$lib/components/OrbLoader.svelte';
  import ThiingsIcon from '$lib/components/ThiingsIcon.svelte';

  let { ind = 0, onNavigate = (view: string, params?: Record<string, unknown>) => {} } = $props();

  let paciente = $state<Paciente | null>(null);
  let loading = $state(true);
  let error = $state('');
  let activeTab = $state('datos');

  let citas = $state<Cita[]>([]);
  let canceladas = $state<CitaCancelada[]>([]);
  let evoluciones = $state<Evolucion[]>([]);
  let abonos = $state<Abono[]>([]);
  let pagos = $state<Pago[]>([]);
  let detallesPagos = $state<DetallePago[]>([]);
  let historias = $state<Record<string, unknown> | null>(null);

  let loadingTab = $state(false);
  let expandedSecciones = $state<Record<string, boolean>>({ datos: true });

  const tabs = [
    { id: 'datos', label: 'Datos' },
    { id: 'medico', label: 'Médico' },
    { id: 'citas', label: 'Citas' },
    { id: 'evoluciones', label: 'Evoluciones' },
    { id: 'financiero', label: 'Financiero' },
  ];

  async function loadPaciente() {
    loading = true;
    error = '';
    try {
      const res = await getPaciente(ind);
      paciente = res.data;
    } catch (e) {
      error = e instanceof Error ? e.message : 'Error al cargar paciente';
    } finally {
      loading = false;
    }
  }

  loadPaciente();

  async function loadTab(tabId: string) {
    if (!paciente) return;
    loadingTab = true;
    try {
      if (tabId === 'citas') {
        const [cRes, cancelRes] = await Promise.all([
          getCitasPaciente(ind),
          getCitasCanceladas(ind),
        ]);
        citas = cRes.data;
        canceladas = cancelRes.data;
      } else if (tabId === 'evoluciones') {
        const res = await getEvoluciones(ind);
        evoluciones = res.data;
      } else if (tabId === 'financiero') {
        const [aRes, pRes, dRes] = await Promise.all([
          getAbonosPaciente(ind),
          getPagos(ind),
          getDetallesPagos(ind),
        ]);
        abonos = aRes.data;
        pagos = pRes.data;
        detallesPagos = dRes.data;
      }
    } catch (e) {
      error = e instanceof Error ? e.message : 'Error al cargar';
    } finally {
      loadingTab = false;
    }
  }

  function handleTabChange(tabId: string) {
    activeTab = tabId;
    if (tabId !== 'datos' && tabId !== 'medico') {
      loadTab(tabId);
    }
  }

  function toggleSeccion(key: string) {
    expandedSecciones[key] = !expandedSecciones[key];
  }

  function nombreCompleto(p: Paciente): string {
    return [p.nombre1, p.nombre2, p.apellido1, p.apellido2].filter(Boolean).join(' ') || p.nombres || '';
  }

  function fmt(n: number | null | undefined): string {
    if (n === null || n === undefined) return '$0';
    return '$' + n.toLocaleString('es-CO');
  }

  function estadoBadge(estado: string | null) {
    if (estado === 'ACTIVO') return { text: 'Activo', color: 'green' as const };
    if (estado === 'INACTIVO') return { text: 'Inactivo', color: 'red' as const };
    return { text: estado || 'N/A', color: 'gray' as const };
  }

  function citaBadge(c: Cita) {
    if (c.asistio === 'S') return { text: 'Atendida', color: 'green' as const };
    if (c.confirmo === 'S') return { text: 'Confirmada', color: 'blue' as const };
    if (c.asistio === 'N') return { text: 'No asistió', color: 'red' as const };
    return { text: 'Pendiente', color: 'yellow' as const };
  }

  interface PadecimientoRow {
    num: string;
    enfermedad: string;
    si: boolean;
    no: boolean;
  }

  function parsePadecimientos(raw: string): PadecimientoRow[] {
    if (!raw) return [];
    const lines = raw.split('~').filter(l => l.trim() !== '');
    return lines.map(line => {
      const parts = line.split(';');
      return {
        num: parts[0]?.trim() || '',
        enfermedad: parts[1]?.trim() || '',
        si: (parts[2]?.trim().toUpperCase() || '') === 'X',
        no: (parts[3]?.trim().toUpperCase() || '') === 'X',
      };
    }).filter(r => r.enfermedad !== '' || r.si || r.no);
  }

  function initials(p: Paciente): string {
    const n = nombreCompleto(p);
    const parts = n.split(' ').filter(Boolean);
    if (parts.length >= 2) return (parts[0][0] + parts[1][0]).toUpperCase();
    return n.charAt(0).toUpperCase();
  }
</script>

<div class="space-y-4">
  <button class="inline-flex items-center gap-2 text-sm font-medium text-gray-600 hover:text-primary-600 transition-colors"
          onclick={() => onNavigate('pacientes')}>
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
      <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
    </svg>
    Volver a Pacientes
  </button>

  {#if loading}
    <div class="bg-white rounded-2xl p-8 shadow-modern border border-gray-100/80 flex justify-center">
      <OrbLoader size={56} state="working" />
    </div>
  {:else if error && !paciente}
    <div class="bg-red-50 border border-red-200 text-red-700 px-5 py-4 rounded-2xl flex items-center gap-3">
      <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
      </svg>
      {error}
    </div>
  {:else if paciente}
    <div class="bg-white rounded-2xl shadow-modern border border-gray-100/80 overflow-hidden">
      <div class="bg-gradient-animated p-5 md:p-6 text-white">
        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
          {#if paciente.tiene_foto}
            <img src={getFotoUrl(ind)} alt="" class="w-16 h-16 rounded-2xl object-cover border-2 border-white/30 flex-shrink-0" />
          {:else}
            <div class="w-16 h-16 rounded-2xl bg-white/20 backdrop-blur-sm flex items-center justify-center text-white text-xl font-bold flex-shrink-0">
              {initials(paciente)}
            </div>
          {/if}
          <div class="flex-1 min-w-0">
            <h1 class="text-xl md:text-2xl font-bold truncate">{nombreCompleto(paciente)}</h1>
            <div class="flex flex-wrap items-center gap-2 mt-1.5 text-sm text-white/80">
              <span class="bg-white/15 px-2 py-0.5 rounded-lg">#{paciente.historia}</span>
              <span>{paciente.identificacion}</span>
              <Badge {...estadoBadge(paciente.estado)} />
            </div>
          </div>
          <div class="text-right bg-white/15 backdrop-blur-sm rounded-xl px-4 py-2">
            <p class="text-xs text-white/70">Saldo</p>
            <p class="text-lg font-bold {paciente.saldo && paciente.saldo > 0 ? 'text-red-200' : 'text-green-200'}">{fmt(paciente.saldo)}</p>
          </div>
        </div>
      </div>

      <Tabs {tabs} {activeTab} onTabChange={handleTabChange} />

      <div class="p-5 md:p-6">
        {#if loadingTab}
          <div class="space-y-3 animate-pulse">
            <div class="h-4 bg-gray-200 rounded w-full"></div>
            <div class="h-4 bg-gray-200 rounded w-3/4"></div>
            <div class="h-4 bg-gray-200 rounded w-1/2"></div>
          </div>

        {:else if activeTab === 'datos'}
          <div class="space-y-5">
            <div>
              <button class="flex items-center gap-2 font-semibold text-gray-900 mb-3" onclick={() => toggleSeccion('datos')}>
                <svg class="w-4 h-4 transition-transform {expandedSecciones.datos ? 'rotate-90' : ''}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
                Identificación y Contacto
              </button>
              {#if expandedSecciones.datos}
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 text-sm bg-gray-50/80 p-4 rounded-xl">
                  <div><span class="font-medium text-gray-500">Tipo doc:</span> <span class="text-gray-900">{paciente.tdei || '-'}</span></div>
                  <div><span class="font-medium text-gray-500">Identificación:</span> <span class="text-gray-900">{paciente.identificacion}</span></div>
                  <div><span class="font-medium text-gray-500">Nacimiento:</span> <span class="text-gray-900">{paciente.fecnac || '-'}</span></div>
                  <div><span class="font-medium text-gray-500">Edad:</span> <span class="text-gray-900">{paciente.edad || '-'} años</span></div>
                  <div><span class="font-medium text-gray-500">Sexo:</span> <span class="text-gray-900">{paciente.sexo || '-'}</span></div>
                  <div><span class="font-medium text-gray-500">Estado civil:</span> <span class="text-gray-900">{paciente.estado_civil || '-'}</span></div>
                  <div><span class="font-medium text-gray-500">Ocupación:</span> <span class="text-gray-900">{paciente.ocupacion || '-'}</span></div>
                  <div><span class="font-medium text-gray-500">Nivel educativo:</span> <span class="text-gray-900">{paciente.nivel_educativo || '-'}</span></div>
                  <div class="sm:col-span-2"><span class="font-medium text-gray-500">Dirección:</span> <span class="text-gray-900">{paciente.direccion_residencia || '-'}</span></div>
                  <div><span class="font-medium text-gray-500">Ciudad:</span> <span class="text-gray-900">{paciente.ciudad_residencia || '-'}</span></div>
                  <div><span class="font-medium text-gray-500">Barrio:</span> <span class="text-gray-900">{paciente.barrio || '-'}</span></div>
                  <div><span class="font-medium text-gray-500">Tel. móvil:</span> <span class="text-gray-900">{paciente.telefono_movil || '-'}</span></div>
                  <div><span class="font-medium text-gray-500">Tel. residencia:</span> <span class="text-gray-900">{paciente.telefono_residencia1 || '-'}</span></div>
                  <div><span class="font-medium text-gray-500">Email:</span> <span class="text-gray-900">{paciente.email1 || '-'}</span></div>
                  <div><span class="font-medium text-gray-500">Fecha inicio:</span> <span class="text-gray-900">{paciente.fecha_inicio || '-'}</span></div>
                </div>
              {/if}
            </div>

            <div>
              <button class="flex items-center gap-2 font-semibold text-gray-900 mb-3" onclick={() => toggleSeccion('familia')}>
                <svg class="w-4 h-4 transition-transform {expandedSecciones.familia ? 'rotate-90' : ''}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
                Familia / Acudientes
              </button>
              {#if expandedSecciones.familia}
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 text-sm bg-gray-50/80 p-4 rounded-xl">
                  <div><span class="font-medium text-gray-500">Padre:</span> <span class="text-gray-900">{paciente.nombre_padre || '-'}</span></div>
                  <div><span class="font-medium text-gray-500">Tel. padre:</span> <span class="text-gray-900">{paciente.telefono_padre || '-'}</span></div>
                  <div><span class="font-medium text-gray-500">Madre:</span> <span class="text-gray-900">{paciente.nombre_madre || '-'}</span></div>
                  <div><span class="font-medium text-gray-500">Tel. madre:</span> <span class="text-gray-900">{paciente.telefono_madre || '-'}</span></div>
                  <div><span class="font-medium text-gray-500">Acudiente:</span> <span class="text-gray-900">{paciente.nombre_acudiente || '-'}</span></div>
                  <div><span class="font-medium text-gray-500">Tel. acudiente:</span> <span class="text-gray-900">{paciente.telefono_acudiente || '-'}</span></div>
                  <div><span class="font-medium text-gray-500">Cónyuge:</span> <span class="text-gray-900">{paciente.nombre_conyuge || '-'}</span></div>
                  <div><span class="font-medium text-gray-500">Tel. cónyuge:</span> <span class="text-gray-900">{paciente.telefono_conyuge || '-'}</span></div>
                  <div><span class="font-medium text-gray-500">Hermanos:</span> <span class="text-gray-900">{paciente.cantidad_hermanos || '-'}</span></div>
                </div>
              {/if}
            </div>

            <div>
              <button class="flex items-center gap-2 font-semibold text-gray-900 mb-3" onclick={() => toggleSeccion('info')}>
                <svg class="w-4 h-4 transition-transform {expandedSecciones.info ? 'rotate-90' : ''}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
                Información Adicional
              </button>
              {#if expandedSecciones.info}
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 text-sm bg-gray-50/80 p-4 rounded-xl">
                  <div><span class="font-medium text-gray-500">Tipo:</span> <span class="text-gray-900">{paciente.tipo || '-'}</span></div>
                  <div><span class="font-medium text-gray-500">Plan:</span> <span class="text-gray-900">{paciente.plan || '-'}</span></div>
                  <div><span class="font-medium text-gray-500">Modalidad pago:</span> <span class="text-gray-900">{paciente.modalidad_de_pago || '-'}</span></div>
                  <div><span class="font-medium text-gray-500">Odontólogo personal:</span> <span class="text-gray-900">{paciente.odontologo_personal || '-'}</span></div>
                  <div><span class="font-medium text-gray-500">Remitido por:</span> <span class="text-gray-900">{paciente.remitido_por || '-'}</span></div>
                  <div><span class="font-medium text-gray-500">Cómo nos conoció:</span> <span class="text-gray-900">{paciente.como_supo_de_nosotros || '-'}</span></div>
                  {#if paciente.observaciones}
                    <div class="sm:col-span-2 lg:col-span-3"><span class="font-medium text-gray-500">Observaciones:</span> <span class="text-gray-900">{paciente.observaciones}</span></div>
                  {/if}
                  {#if paciente.observacion_especial}
                    <div class="sm:col-span-2 lg:col-span-3"><span class="font-medium text-gray-500">Obs. especiales:</span> <span class="text-gray-900">{paciente.observacion_especial}</span></div>
                  {/if}
                </div>
              {/if}
            </div>

            <div>
              <button class="flex items-center gap-2 font-semibold text-gray-900 mb-3" onclick={() => toggleSeccion('tratamiento')}>
                <svg class="w-4 h-4 transition-transform {expandedSecciones.tratamiento ? 'rotate-90' : ''}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
                Plan de Tratamiento
              </button>
              {#if expandedSecciones.tratamiento}
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 text-sm bg-gray-50/80 p-4 rounded-xl">
                  <div><span class="font-medium text-gray-500">Costo:</span> <span class="text-gray-900 font-semibold">{fmt(paciente.costo_tratamiento)}</span></div>
                  <div><span class="font-medium text-gray-500">Cuota inicial:</span> <span class="text-gray-900">{fmt(paciente.cuota_inicial1)}</span></div>
                  <div><span class="font-medium text-gray-500">Nro cuotas:</span> <span class="text-gray-900">{paciente.nocuotas || '-'}</span></div>
                  <div><span class="font-medium text-gray-500">Valor cuota:</span> <span class="text-gray-900">{fmt(paciente.valor_cuota)}</span></div>
                  <div class="sm:col-span-2 lg:col-span-4"><span class="font-medium text-gray-500">Plan tratamiento:</span> <span class="text-gray-900">{paciente.plan_tratamiento || paciente.plan_de_tratamiento || '-'}</span></div>
                </div>
              {/if}
            </div>
          </div>

        {:else if activeTab === 'medico'}
          <div class="space-y-5">
            <div>
              <button class="flex items-center gap-2 font-semibold text-gray-900 mb-3" onclick={() => toggleSeccion('antecedentes')}>
                <svg class="w-4 h-4 transition-transform {expandedSecciones.antecedentes ? 'rotate-90' : ''}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
                Antecedentes Médicos
              </button>
              {#if expandedSecciones.antecedentes}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm bg-gray-50/80 p-4 rounded-xl">
                  <div><span class="font-medium text-gray-500">Padece enfermedad:</span> <span class="text-gray-900">{paciente.padece || '-'}</span></div>
                  <div><span class="font-medium text-gray-500">Cuál:</span> <span class="text-gray-900">{paciente.cual || '-'}</span></div>
                  <div><span class="font-medium text-gray-500">Recibe medicamento:</span> <span class="text-gray-900">{paciente.recibe_medicamento || '-'}</span></div>
                  <div><span class="font-medium text-gray-500">Cuál medicamento:</span> <span class="text-gray-900">{paciente.cual_medicamento || '-'}</span></div>
                  {#if paciente.padecimientos}
                    {@const padecimientos = parsePadecimientos(paciente.padecimientos)}
                    <div class="sm:col-span-2">
                      <span class="font-medium text-gray-500">Padecimientos:</span>
                      {#if padecimientos.length > 0}
                        <div class="mt-2 overflow-x-auto">
                          <table class="w-full text-xs border border-gray-200 rounded-xl overflow-hidden">
                            <thead class="bg-gray-100">
                              <tr>
                                <th class="px-3 py-1.5 text-left font-medium text-gray-600 border-b">#</th>
                                <th class="px-3 py-1.5 text-left font-medium text-gray-600 border-b">Enfermedad</th>
                                <th class="px-3 py-1.5 text-center font-medium text-gray-600 border-b w-12">SI</th>
                                <th class="px-3 py-1.5 text-center font-medium text-gray-600 border-b w-12">NO</th>
                              </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                              {#each padecimientos as p}
                                <tr class="hover:bg-gray-50">
                                  <td class="px-3 py-1 text-gray-500">{p.num}</td>
                                  <td class="px-3 py-1 text-gray-800">{p.enfermedad}</td>
                                  <td class="px-3 py-1 text-center">
                                    {#if p.si}
                                      <span class="inline-block w-4 h-4 rounded bg-green-100 text-green-700 text-xs font-bold leading-4">✓</span>
                                    {/if}
                                  </td>
                                  <td class="px-3 py-1 text-center">
                                    {#if p.no}
                                      <span class="inline-block w-4 h-4 rounded bg-red-100 text-red-700 text-xs font-bold leading-4">✗</span>
                                    {/if}
                                  </td>
                                </tr>
                              {/each}
                            </tbody>
                          </table>
                        </div>
                      {:else}
                        <span class="ml-2 text-gray-500">{paciente.padecimientos}</span>
                      {/if}
                    </div>
                  {/if}
                  {#if paciente.observaciones_medicas}
                    <div class="sm:col-span-2"><span class="font-medium text-gray-500">Obs. médicas:</span> <span class="text-gray-900">{paciente.observaciones_medicas}</span></div>
                  {/if}
                </div>
              {/if}
            </div>

            <div>
              <button class="flex items-center gap-2 font-semibold text-gray-900 mb-3" onclick={() => toggleSeccion('habitos')}>
                <svg class="w-4 h-4 transition-transform {expandedSecciones.habitos ? 'rotate-90' : ''}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
                Hábitos y Examen Clínico
              </button>
              {#if expandedSecciones.habitos}
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 text-sm bg-gray-50/80 p-4 rounded-xl">
                  <div><span class="font-medium text-gray-500">Cepilla:</span> <span class="text-gray-900">{paciente.cepilla || '-'}</span></div>
                  <div><span class="font-medium text-gray-500">Usa seda:</span> <span class="text-gray-900">{paciente.usa_seda || '-'}</span></div>
                  {#if paciente.habitos}
                    <div class="sm:col-span-2 lg:col-span-3"><span class="font-medium text-gray-500">Hábitos:</span> <span class="text-gray-900">{paciente.habitos}</span></div>
                  {/if}
                  <div><span class="font-medium text-gray-500">Relación canina:</span> <span class="text-gray-900">{paciente.relacion_canina || '-'}</span></div>
                  <div><span class="font-medium text-gray-500">Relación molar:</span> <span class="text-gray-900">{paciente.relacion_molar || '-'}</span></div>
                  <div><span class="font-medium text-gray-500">Overjet:</span> <span class="text-gray-900">{paciente.overjet || '-'}</span></div>
                  <div><span class="font-medium text-gray-500">Overbite:</span> <span class="text-gray-900">{paciente.overbite || '-'}</span></div>
                  <div><span class="font-medium text-gray-500">Mordida abierta:</span> <span class="text-gray-900">{paciente.mordida_abierta || '-'}</span></div>
                  <div><span class="font-medium text-gray-500">Mordida cruzada:</span> <span class="text-gray-900">{paciente.mordida_cruzada || '-'}</span></div>
                  <div><span class="font-medium text-gray-500">Diastemas:</span> <span class="text-gray-900">{paciente.diastemas || '-'}</span></div>
                  <div><span class="font-medium text-gray-500">Dientes ausentes:</span> <span class="text-gray-900">{paciente.dientes_ausentes || '-'}</span></div>
                  <div><span class="font-medium text-gray-500">Higiene oral:</span> <span class="text-gray-900">{paciente.higiene_oral || '-'}</span></div>
                  <div><span class="font-medium text-gray-500">Caries:</span> <span class="text-gray-900">{paciente.caries || '-'}</span></div>
                  <div><span class="font-medium text-gray-500">Periodonto:</span> <span class="text-gray-900">{paciente.peridonto || '-'}</span></div>
                </div>
              {/if}
            </div>

            <div>
              <button class="flex items-center gap-2 font-semibold text-gray-900 mb-3" onclick={() => toggleSeccion('diagnosticos')}>
                <svg class="w-4 h-4 transition-transform {expandedSecciones.diagnosticos ? 'rotate-90' : ''}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
                Diagnósticos
              </button>
              {#if expandedSecciones.diagnosticos}
                <div class="space-y-2 text-sm bg-gray-50/80 p-4 rounded-xl">
                  {#if paciente.diagnostico_medico_general}
                    <div><span class="font-medium text-gray-500">Médico general:</span> <span class="text-gray-900">{paciente.diagnostico_medico_general}</span></div>
                  {/if}
                  {#if paciente.diagnostico_intraoral}
                    <div><span class="font-medium text-gray-500">Intraoral:</span> <span class="text-gray-900">{paciente.diagnostico_intraoral}</span></div>
                  {/if}
                  {#if paciente.diagnostico_dental}
                    <div><span class="font-medium text-gray-500">Dental:</span> <span class="text-gray-900">{paciente.diagnostico_dental}</span></div>
                  {/if}
                  {#if paciente.diagnostico_periodontal}
                    <div><span class="font-medium text-gray-500">Periodontal:</span> <span class="text-gray-900">{paciente.diagnostico_periodontal}</span></div>
                  {/if}
                  {#if paciente.diagnostico_endodontico}
                    <div><span class="font-medium text-gray-500">Endodóntico:</span> <span class="text-gray-900">{paciente.diagnostico_endodontico}</span></div>
                  {/if}
                </div>
              {/if}
            </div>
          </div>

        {:else if activeTab === 'citas'}
          {#if citas.length > 0}
            <div class="mb-6">
              <h3 class="font-semibold text-gray-900 mb-3">Citas ({citas.length})</h3>
              <div class="overflow-x-auto table-responsive">
                <table class="w-full text-sm">
                  <thead class="bg-gray-50/80">
                    <tr>
                      <th class="text-left px-4 py-2.5 font-semibold text-gray-600 text-xs uppercase tracking-wider">Fecha</th>
                      <th class="text-left px-4 py-2.5 font-semibold text-gray-600 text-xs uppercase tracking-wider">Hora</th>
                      <th class="text-left px-4 py-2.5 font-semibold text-gray-600 text-xs uppercase tracking-wider">Procedimiento</th>
                      <th class="text-left px-4 py-2.5 font-semibold text-gray-600 text-xs uppercase tracking-wider">Especialista</th>
                      <th class="text-left px-4 py-2.5 font-semibold text-gray-600 text-xs uppercase tracking-wider">Estado</th>
                    </tr>
                  </thead>
                  <tbody class="divide-y divide-gray-100/80">
                    {#each citas as c}
                      <tr class="hover:bg-gray-50/50">
                        <td class="px-4 py-2.5 text-gray-700" data-label="Fecha">{c.fecha}</td>
                        <td class="px-4 py-2.5 text-gray-700" data-label="Hora">{c.horas}</td>
                        <td class="px-4 py-2.5 font-medium text-gray-900" data-label="Procedimiento">{c.procedimiento || '-'}</td>
                        <td class="px-4 py-2.5 text-gray-700" data-label="Especialista">{c.especialista || '-'}</td>
                        <td class="px-4 py-2.5" data-label="Estado"><Badge {...citaBadge(c)} /></td>
                      </tr>
                    {/each}
                  </tbody>
                </table>
              </div>
            </div>
          {/if}

          {#if canceladas.length > 0}
            <div>
              <h3 class="font-semibold text-gray-900 mb-3">Citas Canceladas ({canceladas.length})</h3>
              <div class="overflow-x-auto table-responsive">
                <table class="w-full text-sm">
                  <thead class="bg-gray-50/80">
                    <tr>
                      <th class="text-left px-4 py-2.5 font-semibold text-gray-600 text-xs uppercase tracking-wider">Fecha</th>
                      <th class="text-left px-4 py-2.5 font-semibold text-gray-600 text-xs uppercase tracking-wider">Hora</th>
                      <th class="text-left px-4 py-2.5 font-semibold text-gray-600 text-xs uppercase tracking-wider">Procedimiento</th>
                      <th class="text-left px-4 py-2.5 font-semibold text-gray-600 text-xs uppercase tracking-wider">Especialista</th>
                      <th class="text-left px-4 py-2.5 font-semibold text-gray-600 text-xs uppercase tracking-wider">Borrada por</th>
                      <th class="text-left px-4 py-2.5 font-semibold text-gray-600 text-xs uppercase tracking-wider">Fecha borrado</th>
                    </tr>
                  </thead>
                  <tbody class="divide-y divide-gray-100/80">
                    {#each canceladas as c}
                      <tr class="hover:bg-gray-50/50">
                        <td class="px-4 py-2.5 text-gray-700" data-label="Fecha">{c.fecha}</td>
                        <td class="px-4 py-2.5 text-gray-700" data-label="Hora">{c.horas}</td>
                        <td class="px-4 py-2.5 text-gray-700" data-label="Procedimiento">{c.procedimiento || '-'}</td>
                        <td class="px-4 py-2.5 text-gray-700" data-label="Especialista">{c.especialista || '-'}</td>
                        <td class="px-4 py-2.5 text-gray-700" data-label="Borrada por">{c.borradopor || '-'}</td>
                        <td class="px-4 py-2.5 text-gray-700" data-label="Fecha borrado">{c.fechaborra || '-'}</td>
                      </tr>
                    {/each}
                  </tbody>
                </table>
              </div>
            </div>
          {/if}

          {#if citas.length === 0 && canceladas.length === 0}
            <div class="text-center py-12">
              <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
              </svg>
              <p class="text-gray-400">No hay citas registradas</p>
            </div>
          {/if}

        {:else if activeTab === 'evoluciones'}
          {#if evoluciones.length > 0}
            <div class="space-y-3">
              {#each evoluciones as evo}
                <div class="border border-gray-200/80 rounded-xl p-4 hover:bg-gray-50/50 transition-colors">
                  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-2">
                    <div class="flex flex-wrap items-center gap-2">
                      <span class="font-medium text-gray-900">{evo.fecha}</span>
                      <span class="text-gray-400">{evo.hora}</span>
                      <Badge text={evo.procedimiento || 'N/A'} color="blue" />
                    </div>
                    <span class="text-sm text-gray-500">{evo.profesional || evo.personal_que_atiende || ''}</span>
                  </div>

                  {#if evo.mevolucion}
                    <p class="text-sm text-gray-700 mb-2">{evo.mevolucion}</p>
                  {/if}

                  {#if evo.diagnostico_principal || evo.diagnostico_dental}
                    <div class="mt-2 text-xs text-gray-600">
                      {#if evo.diagnostico_principal}<span class="font-medium">Dx:</span> {evo.diagnostico_principal}{/if}
                      {#if evo.diagnostico_dental} · {evo.diagnostico_dental}{/if}
                    </div>
                  {/if}

                  {#if evo.valor_consulta || evo.valor_copago}
                    <div class="mt-2 text-xs text-gray-500">
                      Consulta: {fmt(evo.valor_consulta)} · Copago: {fmt(evo.valor_copago)}
                    </div>
                  {/if}
                </div>
              {/each}
            </div>
          {:else}
            <div class="text-center py-12">
              <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
              </svg>
              <p class="text-gray-400">No hay evoluciones registradas</p>
            </div>
          {/if}

        {:else if activeTab === 'financiero'}
          <div class="space-y-6">
            {#if pagos.length > 0}
              <div>
                <h3 class="font-semibold text-gray-900 mb-3">Plan de Pagos</h3>
                <div class="space-y-3">
                  {#each pagos as pg}
                    <div class="border border-gray-200/80 rounded-xl p-4">
                      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 text-sm">
                        <div><span class="font-medium text-gray-500">Tipo:</span> <span class="text-gray-900">{pg.tipo || '-'}</span></div>
                        <div><span class="font-medium text-gray-500">Fecha:</span> <span class="text-gray-900">{pg.fecha || '-'}</span></div>
                        <div><span class="font-medium text-gray-500">Costo tratamiento:</span> <span class="text-gray-900 font-semibold">{fmt(parseFloat(pg.costo_tratamiento || '0'))}</span></div>
                        <div><span class="font-medium text-gray-500">Cuota inicial:</span> <span class="text-gray-900">{fmt(parseFloat(String(pg.cuota_inicial1 || '0')))}</span></div>
                        <div><span class="font-medium text-gray-500">Nro cuotas:</span> <span class="text-gray-900">{pg.nocuotas || pg.ncuotas || '-'}</span></div>
                        <div><span class="font-medium text-gray-500">Valor cuota:</span> <span class="text-gray-900">{fmt(pg.valor_cuota)}</span></div>
                        <div><span class="font-medium text-gray-500">Cancelado:</span>
                          <Badge text={pg.cancelado === 'S' ? 'Sí' : 'No'} color={pg.cancelado === 'S' ? 'green' : 'yellow'} />
                        </div>
                      </div>
                    </div>
                  {/each}
                </div>
              </div>
            {/if}

            {#if abonos.length > 0}
              <div>
                <h3 class="font-semibold text-gray-900 mb-3">Abonos ({abonos.length})</h3>
                <div class="overflow-x-auto table-responsive">
                  <table class="w-full text-sm">
                    <thead class="bg-gray-50/80">
                      <tr>
                        <th class="text-left px-4 py-2.5 font-semibold text-gray-600 text-xs uppercase tracking-wider">Fecha</th>
                        <th class="text-left px-4 py-2.5 font-semibold text-gray-600 text-xs uppercase tracking-wider">Valor</th>
                        <th class="text-left px-4 py-2.5 font-semibold text-gray-600 text-xs uppercase tracking-wider">Forma de pago</th>
                        <th class="text-left px-4 py-2.5 font-semibold text-gray-600 text-xs uppercase tracking-wider">Doctor</th>
                        <th class="text-left px-4 py-2.5 font-semibold text-gray-600 text-xs uppercase tracking-wider">Recibo</th>
                      </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100/80">
                      {#each abonos as a}
                        <tr class="hover:bg-gray-50/50">
                          <td class="px-4 py-2.5 text-gray-700" data-label="Fecha">{a.fecha}</td>
                          <td class="px-4 py-2.5 font-semibold text-health-600" data-label="Valor">{fmt(a.valor_abono)}</td>
                          <td class="px-4 py-2.5 text-gray-700" data-label="Forma de pago">{a.forma_de_pago || '-'}</td>
                          <td class="px-4 py-2.5 text-gray-700" data-label="Doctor">{a.doctor || '-'}</td>
                          <td class="px-4 py-2.5 text-gray-700" data-label="Recibo">{a.recibo || '-'}</td>
                        </tr>
                      {/each}
                    </tbody>
                  </table>
                </div>
              </div>
            {/if}

            {#if pagos.length === 0 && abonos.length === 0}
              <div class="text-center py-12">
                <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z" />
                </svg>
                <p class="text-gray-400">No hay información financiera</p>
              </div>
            {/if}
          </div>
        {/if}

        {#if error}
          <Toast message={error} type="error" onclose={() => error = ''} />
        {/if}
      </div>
    </div>
  {/if}
</div>
