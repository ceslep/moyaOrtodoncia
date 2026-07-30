<script lang="ts">
  import { getPaciente, getCitasPaciente, getCitasCanceladas, getEvoluciones, getAbonosPaciente, getPagos, getDetallesPagos, getFotoUrl } from '$lib/api';
  import type { Paciente, Cita, CitaCancelada, Evolucion, Abono, Pago, DetallePago } from '$lib/types';
  import Tabs from '$lib/components/Tabs.svelte';
  import Badge from '$lib/components/Badge.svelte';
  import Toast from '$lib/components/Toast.svelte';
  import OrbLoader from '$lib/components/OrbLoader.svelte';
  import ThiingsIcon from '$lib/components/ThiingsIcon.svelte';
  import CollapsibleSection from '$lib/components/CollapsibleSection.svelte';
  import DataField from '$lib/components/DataField.svelte';
  import FieldIcon from '$lib/components/FieldIcon.svelte';
  import GlassCard from '$lib/components/GlassCard.svelte';
  import PhoneField from '$lib/components/PhoneField.svelte';
  import EmailField from '$lib/components/EmailField.svelte';

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

<div class="space-y-5">
  <button class="group inline-flex items-center gap-2 px-3 py-2 -ml-1 rounded-xl text-sm font-semibold text-slate-600 focus-ring
                 transition-all duration-200 ease-out hover:bg-white/70 hover:text-primary-700"
          onclick={() => onNavigate('pacientes')}>
    <svg class="w-4 h-4 transition-transform duration-200 ease-out group-hover:-translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
      <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
    </svg>
    Volver a Pacientes
  </button>

  {#if loading}
    <GlassCard padding="p-8" class="flex justify-center">
      <OrbLoader size={56} state="working" />
    </GlassCard>
  {:else if error && !paciente}
    <div class="rounded-2xl border border-red-200/80 bg-red-50/80 backdrop-blur-xl px-5 py-4
      text-red-800 shadow-[var(--shadow-soft)] flex items-center gap-3">
      <svg class="w-5 h-5 flex-shrink-0 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
      </svg>
      <span class="text-sm font-medium">{error}</span>
    </div>
  {:else if paciente}
    <GlassCard padding="p-0" class="overflow-hidden">
      <!-- Banda de identidad. Gradiente oscuro y saturado: texto blanco cumple AA -->
      <div class="relative overflow-hidden bg-gradient-to-br from-blue-950 via-primary-600 to-indigo-900 p-5 md:p-6 text-white">
        <span class="pointer-events-none absolute -top-24 -right-12 w-64 h-64 rounded-full bg-white/12 blur-3xl" aria-hidden="true"></span>
        <span class="pointer-events-none absolute -bottom-24 left-10 w-56 h-56 rounded-full bg-emerald-400/12 blur-3xl" aria-hidden="true"></span>

        <div class="relative flex flex-col sm:flex-row items-start sm:items-center gap-4">
          {#if paciente.tiene_foto}
            <img src={getFotoUrl(ind)} alt="" class="w-16 h-16 rounded-2xl object-cover border-2 border-white/40 shadow-float flex-shrink-0" />
          {:else}
            <div class="w-16 h-16 rounded-2xl bg-white/20 backdrop-blur-md border border-white/30 flex items-center justify-center text-white text-xl font-bold flex-shrink-0">
              {initials(paciente)}
            </div>
          {/if}
          <div class="flex-1 min-w-0">
            <div class="flex items-center gap-2">
              <ThiingsIcon name={paciente.sexo === 'M' ? 'doctor' : 'patient'} size={24} alt="" />
              <h1 class="text-xl md:text-2xl font-bold tracking-tight truncate">{nombreCompleto(paciente)}</h1>
            </div>
            <div class="flex flex-wrap items-center gap-2 mt-2 text-sm">
              <span class="num bg-white/20 border border-white/25 px-2.5 py-1 rounded-full text-xs font-semibold">#{paciente.historia}</span>
              <span class="num text-white/85">{paciente.identificacion}</span>
              <Badge {...estadoBadge(paciente.estado)} onLight={false} dot />
            </div>
          </div>
          <div class="text-right bg-white/15 backdrop-blur-md border border-white/25 rounded-xl px-4 py-2.5 self-stretch sm:self-auto">
            <p class="text-[11px] font-semibold uppercase tracking-wider text-white/75">Saldo</p>
            <p class="num text-lg font-bold {paciente.saldo && paciente.saldo > 0 ? 'text-rose-100' : 'text-emerald-100'}">{fmt(paciente.saldo)}</p>
          </div>
        </div>
      </div>

      <Tabs {tabs} {activeTab} onTabChange={handleTabChange} />

      <div class="p-4 sm:p-5 md:p-6">
        {#if loadingTab}
          <div class="space-y-3">
            <div class="skeleton h-11 w-full"></div>
            <div class="skeleton h-11 w-11/12"></div>
            <div class="skeleton h-11 w-3/4"></div>
            <div class="skeleton h-11 w-1/2"></div>
          </div>

        {:else if activeTab === 'datos'}
          <div class="space-y-3">
            <CollapsibleSection
              title="Identificación y Contacto"
              iconName="id-card"
              tone="primary"
              open={expandedSecciones.datos}
              ontoggle={() => toggleSeccion('datos')}
            >
              <dl class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-2">
                <DataField label="Tipo doc" value={paciente.tdei} iconName="doc" tone="slate" />
                <DataField label="Identificación" value={paciente.identificacion} iconName="id-card" tone="blue" strong />
                <DataField label="Nacimiento" value={paciente.fecnac} iconName="cake" tone="violet" />
                <DataField label="Edad" value="{paciente.edad || '-'} años" iconName="user" tone="indigo" />
                <DataField label="Sexo" value={paciente.sexo} iconName="user" tone={paciente.sexo === 'M' ? 'blue' : 'pink'} />
                <DataField label="Estado civil" value={paciente.estado_civil} iconName="heart" tone="rose" />
                <DataField label="Ocupación" value={paciente.ocupacion_nombre || paciente.ocupacion} iconName="briefcase" tone="amber" />
                <DataField label="Nivel educativo" value={paciente.nivel_educativo} iconName="academic" tone="violet" />
                <DataField label="Dirección" value={paciente.direccion_residencia} iconName="home" tone="orange" span="sm:col-span-2" />
                <DataField
                  label="Ciudad"
                  value="{paciente.municipio_nombre || paciente.ciudad_residencia || '-'}{paciente.municipio_departamento ? ' (' + paciente.municipio_departamento + ')' : ''}"
                  iconName="city"
                  tone="orange"
                />
                <DataField label="Barrio" value={paciente.barrio} iconName="map-pin" tone="orange" />
                <PhoneField label="Tel. móvil" value={paciente.telefono_movil} iconName="mobile" />
                <PhoneField label="Tel. residencia" value={paciente.telefono_residencia1} iconName="phone" />
                <EmailField label="Email" value={paciente.email1} />
                <DataField label="Fecha inicio" value={paciente.fecha_inicio} iconName="calendar" tone="emerald" />
              </dl>
            </CollapsibleSection>

            <CollapsibleSection
              title="Familia / Acudientes"
              iconName="users"
              tone="violet"
              open={expandedSecciones.familia}
              ontoggle={() => toggleSeccion('familia')}
            >
              <!-- Nombre = azul/rosa segun rol; telefono = teal en todos, para
                   que la columna de contacto se lea de un barrido -->
              <dl class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2">
                <DataField label="Padre" value={paciente.nombre_padre} iconName="user" tone="blue" />
                <PhoneField label="Tel. padre" value={paciente.telefono_padre} />
                <DataField label="Madre" value={paciente.nombre_madre} iconName="user" tone="pink" />
                <PhoneField label="Tel. madre" value={paciente.telefono_madre} />
                <DataField label="Acudiente" value={paciente.nombre_acudiente} iconName="users" tone="indigo" />
                <PhoneField label="Tel. acudiente" value={paciente.telefono_acudiente} />
                <DataField label="Cónyuge" value={paciente.nombre_conyuge} iconName="heart" tone="rose" />
                <PhoneField label="Tel. cónyuge" value={paciente.telefono_conyuge} />
                <DataField label="Hermanos" value={paciente.cantidad_hermanos} iconName="users" tone="violet" />
              </dl>
            </CollapsibleSection>

            <CollapsibleSection
              title="Información Adicional"
              open={expandedSecciones.info}
              ontoggle={() => toggleSeccion('info')}
            >
              <dl class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2">
                <DataField label="Tipo" value={paciente.tipo} iconName="hash" tone="slate" />
                <DataField label="Plan" value={paciente.plan} iconName="clipboard" tone="indigo" />
                <DataField label="Modalidad pago" value={paciente.modalidad_de_pago} iconName="card" tone="emerald" />
                <DataField label="Odontólogo personal" value={paciente.odontologo_personal} iconName="stetho" tone="sky" />
                <DataField label="Remitido por" value={paciente.remitido_por} iconName="link" tone="violet" />
                <DataField label="Cómo nos conoció" value={paciente.como_supo_de_nosotros} iconName="megaphone" tone="amber" />
                {#if paciente.observaciones}
                  <DataField label="Observaciones" value={paciente.observaciones} iconName="info" tone="slate" span="sm:col-span-2 lg:col-span-3" />
                {/if}
                {#if paciente.observacion_especial}
                  <!-- Ambar: obs. especial es informacion que el clinico debe notar -->
                  <DataField label="Obs. especiales" value={paciente.observacion_especial} iconName="megaphone" tone="amber" span="sm:col-span-2 lg:col-span-3" />
                {/if}
              </dl>
            </CollapsibleSection>

            <CollapsibleSection
              title="Plan de Tratamiento"
              open={expandedSecciones.tratamiento}
              ontoggle={() => toggleSeccion('tratamiento')}
            >
              <!-- Dinero = esmeralda; conteo = indigo -->
              <dl class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-2">
                <DataField label="Costo" value={fmt(paciente.costo_tratamiento)} iconName="money" tone="emerald" strong />
                <DataField label="Cuota inicial" value={fmt(paciente.cuota_inicial1)} iconName="card" tone="emerald" />
                <DataField label="Nro cuotas" value={paciente.nocuotas} iconName="hash" tone="indigo" />
                <DataField label="Valor cuota" value={fmt(paciente.valor_cuota)} iconName="money" tone="emerald" />
                <DataField
                  label="Plan tratamiento"
                  value={paciente.plan_tratamiento || paciente.plan_de_tratamiento}
                  iconName="clipboard"
                  tone="indigo"
                  span="sm:col-span-2 lg:col-span-4"
                />
              </dl>
            </CollapsibleSection>
          </div>

        {:else if activeTab === 'medico'}
          <div class="space-y-3">
            <CollapsibleSection
              title="Antecedentes Médicos"
              open={expandedSecciones.antecedentes}
              ontoggle={() => toggleSeccion('antecedentes')}
            >
              <!-- Rojo/rosa = alerta clinica (enfermedad, medicacion): el color
                   es la senal, no adorno. Se apaga a slate cuando el dato es
                   negativo o esta vacio, para no gritar sin motivo. -->
              <dl class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                <DataField
                  label="Padece enfermedad"
                  value={paciente.padece}
                  iconName="heart"
                  tone={paciente.padece && paciente.padece.trim().toUpperCase().startsWith('S') ? 'red' : 'slate'}
                />
                <DataField label="Cuál" value={paciente.cual} iconName="clipboard" tone={paciente.cual ? 'rose' : 'slate'} />
                <DataField
                  label="Recibe medicamento"
                  value={paciente.recibe_medicamento}
                  iconName="pill"
                  tone={paciente.recibe_medicamento && paciente.recibe_medicamento.trim().toUpperCase().startsWith('S') ? 'red' : 'slate'}
                />
                <DataField label="Cuál medicamento" value={paciente.cual_medicamento} iconName="pill" tone={paciente.cual_medicamento ? 'rose' : 'slate'} />
                {#if paciente.padecimientos}
                  {@const padecimientos = parsePadecimientos(paciente.padecimientos)}
                  <div class="sm:col-span-2 rounded-xl bg-white/70 border border-white/80 px-3 py-2.5">
                    <dt class="flex items-center gap-2 text-[11px] font-semibold uppercase tracking-wider text-slate-500">
                      <span class="w-7 h-7 rounded-lg border bg-rose-500/12 border-rose-500/20 text-rose-600
                                   flex items-center justify-center flex-shrink-0 [&_svg]:w-4 [&_svg]:h-4" aria-hidden="true">
                        <FieldIcon name="clipboard" />
                      </span>
                      Padecimientos
                    </dt>
                    {#if padecimientos.length > 0}
                      <div class="mt-2 overflow-x-auto rounded-xl border border-slate-200/80">
                        <table class="w-full text-xs">
                          <thead class="bg-slate-50/90">
                            <tr>
                              <th class="px-3 py-2 text-left font-semibold text-slate-500 uppercase tracking-wider text-[10px]">#</th>
                              <th class="px-3 py-2 text-left font-semibold text-slate-500 uppercase tracking-wider text-[10px]">Enfermedad</th>
                              <th class="px-3 py-2 text-center font-semibold text-slate-500 uppercase tracking-wider text-[10px] w-14">SI</th>
                              <th class="px-3 py-2 text-center font-semibold text-slate-500 uppercase tracking-wider text-[10px] w-14">NO</th>
                            </tr>
                          </thead>
                          <tbody class="divide-y divide-slate-200/60">
                            {#each padecimientos as p}
                              <tr class="odd:bg-white/50 transition-colors duration-150 ease-out hover:bg-primary-600/6">
                                <td class="num px-3 py-1.5 text-slate-500">{p.num}</td>
                                <td class="px-3 py-1.5 text-slate-800">{p.enfermedad}</td>
                                <td class="px-3 py-1.5 text-center">
                                  {#if p.si}
                                    <span class="inline-flex items-center justify-center w-5 h-5 rounded-md bg-emerald-500/12 border border-emerald-600/25 text-emerald-700 text-[11px] font-bold">✓</span>
                                  {/if}
                                </td>
                                <td class="px-3 py-1.5 text-center">
                                  {#if p.no}
                                    <span class="inline-flex items-center justify-center w-5 h-5 rounded-md bg-red-500/12 border border-red-600/25 text-red-700 text-[11px] font-bold">✗</span>
                                  {/if}
                                </td>
                              </tr>
                            {/each}
                          </tbody>
                        </table>
                      </div>
                    {:else}
                      <dd class="mt-0.5 text-sm text-slate-700">{paciente.padecimientos}</dd>
                    {/if}
                  </div>
                {/if}
                {#if paciente.observaciones_medicas}
                  <DataField label="Obs. médicas" value={paciente.observaciones_medicas} iconName="stetho" tone="sky" span="sm:col-span-2" />
                {/if}
              </dl>
            </CollapsibleSection>

            <CollapsibleSection
              title="Hábitos y Examen Clínico"
              open={expandedSecciones.habitos}
              ontoggle={() => toggleSeccion('habitos')}
            >
              <!-- Higiene = esmeralda (habito bueno); mediciones oclusales =
                   sky/indigo; caries y periodonto = ambar/rosa (hallazgo) -->
              <dl class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2">
                <DataField label="Cepilla" value={paciente.cepilla} iconName="sparkles" tone="emerald" />
                <DataField label="Usa seda" value={paciente.usa_seda} iconName="sparkles" tone="emerald" />
                {#if paciente.habitos}
                  <DataField label="Hábitos" value={paciente.habitos} iconName="info" tone="amber" span="sm:col-span-2 lg:col-span-3" />
                {/if}
                <DataField label="Relación canina" value={paciente.relacion_canina} iconName="beaker" tone="sky" />
                <DataField label="Relación molar" value={paciente.relacion_molar} iconName="beaker" tone="sky" />
                <DataField label="Overjet" value={paciente.overjet} iconName="scale" tone="indigo" />
                <DataField label="Overbite" value={paciente.overbite} iconName="scale" tone="indigo" />
                <DataField label="Mordida abierta" value={paciente.mordida_abierta} iconName="beaker" tone="violet" />
                <DataField label="Mordida cruzada" value={paciente.mordida_cruzada} iconName="beaker" tone="violet" />
                <DataField label="Diastemas" value={paciente.diastemas} iconName="hash" tone="slate" />
                <DataField label="Dientes ausentes" value={paciente.dientes_ausentes} iconName="hash" tone="rose" />
                <DataField label="Higiene oral" value={paciente.higiene_oral} iconName="sparkles" tone="emerald" />
                <DataField label="Caries" value={paciente.caries} iconName="pill" tone="amber" />
                <DataField label="Periodonto" value={paciente.peridonto} iconName="heart" tone="rose" />
              </dl>
            </CollapsibleSection>

            <CollapsibleSection
              title="Diagnósticos"
              open={expandedSecciones.diagnosticos}
              ontoggle={() => toggleSeccion('diagnosticos')}
            >
              <!-- Un tono por area diagnostica: distingue las cinco lineas sin
                   depender de leer la etiqueta -->
              <dl class="grid grid-cols-1 gap-2">
                {#if paciente.diagnostico_medico_general}
                  <DataField label="Médico general" value={paciente.diagnostico_medico_general} iconName="stetho" tone="sky" />
                {/if}
                {#if paciente.diagnostico_intraoral}
                  <DataField label="Intraoral" value={paciente.diagnostico_intraoral} iconName="beaker" tone="violet" />
                {/if}
                {#if paciente.diagnostico_dental}
                  <DataField label="Dental" value={paciente.diagnostico_dental} iconName="tooth" tone="indigo" />
                {/if}
                {#if paciente.diagnostico_periodontal}
                  <DataField label="Periodontal" value={paciente.diagnostico_periodontal} iconName="heart" tone="rose" />
                {/if}
                {#if paciente.diagnostico_endodontico}
                  <DataField label="Endodóntico" value={paciente.diagnostico_endodontico} iconName="pill" tone="amber" />
                {/if}
              </dl>
            </CollapsibleSection>
          </div>

        {:else if activeTab === 'citas'}
          {#if citas.length > 0}
            <div class="mb-6">
              <div class="flex items-center gap-2.5 mb-3">
                <h3 class="text-sm font-semibold tracking-tight text-slate-900">Citas</h3>
                <span class="num text-xs font-semibold text-slate-500 bg-slate-500/10 px-2 py-0.5 rounded-full">{citas.length}</span>
              </div>
              <div class="overflow-x-auto table-responsive rounded-xl border border-slate-200/80">
                <table class="w-full text-sm">
                  <thead class="bg-slate-50/90 backdrop-blur-sm sm:sticky sm:top-0 sm:z-10">
                    <tr>
                      <th class="text-left px-4 py-3 font-semibold text-slate-500 text-[11px] uppercase tracking-wider">Fecha</th>
                      <th class="text-left px-4 py-3 font-semibold text-slate-500 text-[11px] uppercase tracking-wider">Hora</th>
                      <th class="text-left px-4 py-3 font-semibold text-slate-500 text-[11px] uppercase tracking-wider">Procedimiento</th>
                      <th class="text-left px-4 py-3 font-semibold text-slate-500 text-[11px] uppercase tracking-wider">Especialista</th>
                      <th class="text-left px-4 py-3 font-semibold text-slate-500 text-[11px] uppercase tracking-wider">Estado</th>
                    </tr>
                  </thead>
                  <tbody class="divide-y divide-slate-200/60">
                    {#each citas as c}
                      <tr class="odd:bg-white/50 transition-colors duration-150 ease-out hover:bg-primary-600/6">
                        <td class="num px-4 py-3 text-slate-600" data-label="Fecha">{c.fecha}</td>
                        <td class="num px-4 py-3 text-slate-600" data-label="Hora">{c.horas}</td>
                        <td class="px-4 py-3 font-semibold text-slate-900" data-label="Procedimiento">{c.procedimiento || '-'}</td>
                        <td class="px-4 py-3 text-slate-600" data-label="Especialista">{c.especialista || '-'}</td>
                        <td class="px-4 py-3" data-label="Estado"><Badge {...citaBadge(c)} dot /></td>
                      </tr>
                    {/each}
                  </tbody>
                </table>
              </div>
            </div>
          {/if}

          {#if canceladas.length > 0}
            <div>
              <div class="flex items-center gap-2.5 mb-3">
                <h3 class="text-sm font-semibold tracking-tight text-slate-900">Citas Canceladas</h3>
                <span class="num text-xs font-semibold text-red-700 bg-red-500/12 border border-red-600/20 px-2 py-0.5 rounded-full">{canceladas.length}</span>
              </div>
              <div class="overflow-x-auto table-responsive rounded-xl border border-slate-200/80">
                <table class="w-full text-sm">
                  <thead class="bg-slate-50/90 backdrop-blur-sm sm:sticky sm:top-0 sm:z-10">
                    <tr>
                      <th class="text-left px-4 py-3 font-semibold text-slate-500 text-[11px] uppercase tracking-wider">Fecha</th>
                      <th class="text-left px-4 py-3 font-semibold text-slate-500 text-[11px] uppercase tracking-wider">Hora</th>
                      <th class="text-left px-4 py-3 font-semibold text-slate-500 text-[11px] uppercase tracking-wider">Procedimiento</th>
                      <th class="text-left px-4 py-3 font-semibold text-slate-500 text-[11px] uppercase tracking-wider">Especialista</th>
                      <th class="text-left px-4 py-3 font-semibold text-slate-500 text-[11px] uppercase tracking-wider">Borrada por</th>
                      <th class="text-left px-4 py-3 font-semibold text-slate-500 text-[11px] uppercase tracking-wider">Fecha borrado</th>
                    </tr>
                  </thead>
                  <tbody class="divide-y divide-slate-200/60">
                    {#each canceladas as c}
                      <tr class="odd:bg-white/50 transition-colors duration-150 ease-out hover:bg-red-500/6">
                        <td class="num px-4 py-3 text-slate-600" data-label="Fecha">{c.fecha}</td>
                        <td class="num px-4 py-3 text-slate-600" data-label="Hora">{c.horas}</td>
                        <td class="px-4 py-3 text-slate-700" data-label="Procedimiento">{c.procedimiento || '-'}</td>
                        <td class="px-4 py-3 text-slate-600" data-label="Especialista">{c.especialista || '-'}</td>
                        <td class="px-4 py-3 text-slate-600" data-label="Borrada por">{c.borradopor || '-'}</td>
                        <td class="num px-4 py-3 text-slate-600" data-label="Fecha borrado">{c.fechaborra || '-'}</td>
                      </tr>
                    {/each}
                  </tbody>
                </table>
              </div>
            </div>
          {/if}

          {#if citas.length === 0 && canceladas.length === 0}
            <div class="rounded-2xl surface-subtle px-6 py-14 text-center">
              <div class="mx-auto w-14 h-14 rounded-2xl bg-slate-500/8 border border-slate-500/12 flex items-center justify-center mb-4">
                <svg class="w-7 h-7 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                </svg>
              </div>
              <p class="text-sm font-semibold text-slate-700">No hay citas registradas</p>
              <p class="mt-1 text-sm text-slate-500">Este paciente aun no tiene citas en el historial.</p>
            </div>
          {/if}

        {:else if activeTab === 'evoluciones'}
          {#if evoluciones.length > 0}
            <div class="space-y-3 list-optimized">
              {#each evoluciones as evo, i}
                <!-- Linea de tiempo: barra de acento a la izquierda -->
                <div
                  class="relative rounded-xl surface-subtle p-4 pl-5 overflow-hidden animate-rise
                    transition-[background-color,box-shadow] duration-200 ease-out hover:bg-white/85 hover:shadow-[var(--shadow-soft)]"
                  style="--i: {i % 12}"
                >
                  <span class="absolute left-0 top-0 bottom-0 w-1 bg-gradient-to-b from-primary-600 to-accent-600" aria-hidden="true"></span>

                  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-2">
                    <div class="flex flex-wrap items-center gap-2">
                      <span class="num font-semibold text-slate-900">{evo.fecha}</span>
                      <span class="num text-sm text-slate-500">{evo.hora}</span>
                      <Badge text={evo.procedimiento || 'N/A'} color="blue" />
                    </div>
                    <span class="text-sm text-slate-500">{evo.profesional || evo.personal_que_atiende || ''}</span>
                  </div>

                  {#if evo.mevolucion}
                    <p class="text-sm leading-relaxed text-slate-700">{evo.mevolucion}</p>
                  {/if}

                  {#if evo.diagnostico_principal || evo.diagnostico_dental}
                    <div class="mt-2.5 text-xs text-slate-600">
                      {#if evo.diagnostico_principal}<span class="font-semibold text-slate-500">Dx:</span> {evo.diagnostico_principal}{/if}
                      {#if evo.diagnostico_dental} · {evo.diagnostico_dental}{/if}
                    </div>
                  {/if}

                  {#if evo.valor_consulta || evo.valor_copago}
                    <div class="num mt-2.5 text-xs text-slate-500">
                      Consulta: {fmt(evo.valor_consulta)} · Copago: {fmt(evo.valor_copago)}
                    </div>
                  {/if}
                </div>
              {/each}
            </div>
          {:else}
            <div class="rounded-2xl surface-subtle px-6 py-14 text-center">
              <div class="mx-auto w-14 h-14 rounded-2xl bg-slate-500/8 border border-slate-500/12 flex items-center justify-center mb-4">
                <svg class="w-7 h-7 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                </svg>
              </div>
              <p class="text-sm font-semibold text-slate-700">No hay evoluciones registradas</p>
              <p class="mt-1 text-sm text-slate-500">Las notas de evolucion apareceran aqui.</p>
            </div>
          {/if}

        {:else if activeTab === 'financiero'}
          <div class="space-y-6">
            {#if pagos.length > 0}
              <div>
                <h3 class="text-sm font-semibold tracking-tight text-slate-900 mb-3">Plan de Pagos</h3>
                <div class="space-y-3">
                  {#each pagos as pg, i}
                    <div class="rounded-xl surface-subtle p-4 animate-rise" style="--i: {i % 12}">
                      <dl class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-2">
                        <DataField label="Tipo" value={pg.tipo} iconName="hash" tone="slate" />
                        <DataField label="Fecha" value={pg.fecha} iconName="calendar" tone="violet" />
                        <DataField label="Costo tratamiento" value={fmt(parseFloat(pg.costo_tratamiento || '0'))} iconName="money" tone="emerald" strong />
                        <DataField label="Cuota inicial" value={fmt(parseFloat(String(pg.cuota_inicial1 || '0')))} iconName="card" tone="emerald" />
                        <DataField label="Nro cuotas" value={pg.nocuotas || pg.ncuotas} iconName="hash" tone="indigo" />
                        <DataField label="Valor cuota" value={fmt(pg.valor_cuota)} iconName="money" tone="emerald" />
                        <!-- Cancelado: el chip sigue al estado real del pago -->
                        <DataField label="Cancelado" iconName="receipt" tone={pg.cancelado === 'S' ? 'emerald' : 'amber'}>
                          <Badge text={pg.cancelado === 'S' ? 'Sí' : 'No'} color={pg.cancelado === 'S' ? 'green' : 'yellow'} dot />
                        </DataField>
                      </dl>
                    </div>
                  {/each}
                </div>
              </div>
            {/if}

            {#if abonos.length > 0}
              <div>
                <div class="flex items-center gap-2.5 mb-3">
                  <h3 class="text-sm font-semibold tracking-tight text-slate-900">Abonos</h3>
                  <span class="num text-xs font-semibold text-health-700 bg-health-500/12 border border-health-600/20 px-2 py-0.5 rounded-full">{abonos.length}</span>
                </div>
                <div class="overflow-x-auto table-responsive rounded-xl border border-slate-200/80">
                  <table class="w-full text-sm">
                    <thead class="bg-slate-50/90 backdrop-blur-sm sm:sticky sm:top-0 sm:z-10">
                      <tr>
                        <th class="text-left px-4 py-3 font-semibold text-slate-500 text-[11px] uppercase tracking-wider">Fecha</th>
                        <th class="text-left px-4 py-3 font-semibold text-slate-500 text-[11px] uppercase tracking-wider">Valor</th>
                        <th class="text-left px-4 py-3 font-semibold text-slate-500 text-[11px] uppercase tracking-wider">Forma de pago</th>
                        <th class="text-left px-4 py-3 font-semibold text-slate-500 text-[11px] uppercase tracking-wider">Doctor</th>
                        <th class="text-left px-4 py-3 font-semibold text-slate-500 text-[11px] uppercase tracking-wider">Recibo</th>
                      </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200/60">
                      {#each abonos as a}
                        <tr class="odd:bg-white/50 transition-colors duration-150 ease-out hover:bg-health-500/6">
                          <td class="num px-4 py-3 text-slate-600" data-label="Fecha">{a.fecha}</td>
                          <td class="num px-4 py-3 font-bold text-health-700" data-label="Valor">{fmt(a.valor_abono)}</td>
                          <td class="px-4 py-3 text-slate-700" data-label="Forma de pago">{a.forma_de_pago || '-'}</td>
                          <td class="px-4 py-3 text-slate-600" data-label="Doctor">{a.doctor || '-'}</td>
                          <td class="num px-4 py-3 text-slate-600" data-label="Recibo">{a.recibo || '-'}</td>
                        </tr>
                      {/each}
                    </tbody>
                  </table>
                </div>
              </div>
            {/if}

            {#if pagos.length === 0 && abonos.length === 0}
              <div class="rounded-2xl surface-subtle px-6 py-14 text-center">
                <div class="mx-auto w-14 h-14 rounded-2xl bg-slate-500/8 border border-slate-500/12 flex items-center justify-center mb-4">
                  <svg class="w-7 h-7 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z" />
                  </svg>
                </div>
                <p class="text-sm font-semibold text-slate-700">No hay información financiera</p>
                <p class="mt-1 text-sm text-slate-500">Sin plan de pagos ni abonos registrados.</p>
              </div>
            {/if}
          </div>
        {/if}

        {#if error}
          <Toast message={error} type="error" onclose={() => error = ''} />
        {/if}
      </div>
    </GlassCard>
  {/if}
</div>
