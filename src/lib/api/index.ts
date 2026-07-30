import type { PaginatedResponse, Paciente, Cita, CitaCancelada, Evolucion, Abono, Pago, DetallePago, DashboardResumen, DatosEmpresa } from '$lib/types';

const BASE = 'https://app.iedeoccidente.com/mo/public/index.php';

async function apiFetch<T>(url: string): Promise<T> {
  const res = await fetch(url);
  if (!res.ok) {
    const err = await res.json().catch(() => ({ error: { message: 'Error de red', code: res.status } }));
    throw new Error(err.error?.message || `HTTP ${res.status}`);
  }
  return res.json();
}

export function searchPacientes(search: string, page = 1, perPage = 20) {
  const params = new URLSearchParams({ search, page: String(page), per_page: String(perPage) });
  return apiFetch<PaginatedResponse<Paciente>>(`${BASE}?route=api/pacientes&${params}`);
}

export function getPaciente(ind: number) {
  return apiFetch<{ data: Paciente }>(`${BASE}?route=api/pacientes/${ind}`);
}

export function getCitasPaciente(ind: number, estado?: string, desde?: string, hasta?: string) {
  const params = new URLSearchParams();
  if (estado) params.set('estado', estado);
  if (desde) params.set('desde', desde);
  if (hasta) params.set('hasta', hasta);
  const qs = params.toString();
  return apiFetch<{ data: Cita[] }>(`${BASE}?route=api/pacientes/${ind}/citas${qs ? '&' + qs : ''}`);
}

export function getCitasCanceladas(ind: number) {
  return apiFetch<{ data: CitaCancelada[] }>(`${BASE}?route=api/pacientes/${ind}/citas-canceladas`);
}

export function getEvoluciones(ind: number) {
  return apiFetch<{ data: Evolucion[] }>(`${BASE}?route=api/pacientes/${ind}/evoluciones`);
}

export function getAbonosPaciente(ind: number) {
  return apiFetch<{ data: Abono[] }>(`${BASE}?route=api/pacientes/${ind}/abonos`);
}

export function getPagos(ind: number) {
  return apiFetch<{ data: Pago[] }>(`${BASE}?route=api/pacientes/${ind}/pagos`);
}

export function getDetallesPagos(ind: number) {
  return apiFetch<{ data: DetallePago[] }>(`${BASE}?route=api/pacientes/${ind}/detalles-pagos`);
}

export function getHistoriaClinica(ind: number) {
  return apiFetch<{ data: Record<string, unknown> }>(`${BASE}?route=api/pacientes/${ind}/historia-clinica`);
}

export function getFotoUrl(ind: number): string {
  return `${BASE}?route=api/pacientes/${ind}/foto`;
}

export function getCitasGlobales(params: Record<string, string>, page = 1, perPage = 20) {
  const sp = new URLSearchParams({ page: String(page), per_page: String(perPage) });
  for (const [k, v] of Object.entries(params)) {
    if (v) sp.set(k, v);
  }
  return apiFetch<PaginatedResponse<Cita>>(`${BASE}?route=api/citas&${sp}`);
}

export function getAbonosGlobales(params: Record<string, string>, page = 1, perPage = 20) {
  const sp = new URLSearchParams({ page: String(page), per_page: String(perPage) });
  for (const [k, v] of Object.entries(params)) {
    if (v) sp.set(k, v);
  }
  return apiFetch<PaginatedResponse<Abono>>(`${BASE}?route=api/abonos&${sp}`);
}

export function getProcedimientos(search: string, page = 1, perPage = 20) {
  const sp = new URLSearchParams({ search, page: String(page), per_page: String(perPage) });
  return apiFetch<PaginatedResponse<import('$lib/types').Procedimiento>>(`${BASE}?route=api/procedimientos&${sp}`);
}

export function getEspecialidades(search: string) {
  const sp = new URLSearchParams({ search });
  return apiFetch<{ data: import('$lib/types').Especialidad[] }>(`${BASE}?route=api/especialidades&${sp}`);
}

export function getEntidades(search: string) {
  const sp = new URLSearchParams({ search });
  return apiFetch<{ data: import('$lib/types').Entidad[] }>(`${BASE}?route=api/entidades&${sp}`);
}

export function getPersonal(search: string, page = 1, perPage = 20) {
  const sp = new URLSearchParams({ search, page: String(page), per_page: String(perPage) });
  return apiFetch<PaginatedResponse<import('$lib/types').HojaVida>>(`${BASE}?route=api/personal&${sp}`);
}

export function getPersonalFicha(ind: number, identificacion: number) {
  return apiFetch<{ data: import('$lib/types').HojaVida }>(`${BASE}?route=api/personal/${ind}/${identificacion}`);
}

export function getDashboard() {
  return apiFetch<{ data: DashboardResumen }>(`${BASE}?route=api/dashboard/resumen`);
}

export function getDatosEmpresa() {
  return apiFetch<{ data: DatosEmpresa }>(`${BASE}?route=api/datos-empresa`);
}

export function getMunicipioByCodigo(codigo: string) {
  return apiFetch<{ data: Record<string, string> }>(`${BASE}?route=api/municipios/${encodeURIComponent(codigo)}`);
}

export function getOcupacionByCodigo(codigo: string) {
  return apiFetch<{ data: Record<string, string> }>(`${BASE}?route=api/ocupaciones/${encodeURIComponent(codigo)}`);
}
