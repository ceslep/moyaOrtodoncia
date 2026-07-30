export interface PaginatedResponse<T> {
  data: T[];
  meta: {
    page: number;
    per_page: number;
    total: number;
    total_pages: number;
  };
}

export interface ApiError {
  error: {
    message: string;
    code: number;
  };
}

export interface Paciente {
  ind: number;
  historia: number;
  identificacion: number | string;
  fecha: string | null;
  tdei: string | null;
  nombres: string | null;
  nombre1: string | null;
  nombre2: string | null;
  apellido1: string | null;
  apellido2: string | null;
  fecnac: string | null;
  edad: string | null;
  sexo: string | null;
  estado: string | null;
  telefono_movil: string | null;
  telefono_residencia1: string | null;
  telefono_residencia2: string | null;
  email1: string | null;
  email2: string | null;
  ocupacion: string | null;
  estado_civil: string | null;
  lugarnacimiento: string | null;
  direccion_residencia: string | null;
  ciudad_residencia: string | null;
  barrio: string | null;
  saldo: number | null;
  fecha_inicio: string | null;
  fecha_inicio2: string | null;
  costo_tratamiento: number | null;
  cuota_inicial1: number | null;
  cuota_inicial2: number | null;
  cuota_inicial3: number | null;
  cuota_inicial4: number | null;
  nocuotas: number | null;
  ncuotas: number | null;
  valor_cuota: number | null;
  plan: string | null;
  modalidad_de_pago: string | null;
  paciente_paga_completo: string | null;
  tiene_foto: number;
  nivel_educativo: string | null;
  en_que_colegio: string | null;
  nombre_padre: string | null;
  telefono_padre: string | null;
  nombre_madre: string | null;
  telefono_madre: string | null;
  nombre_acudiente: string | null;
  telefono_acudiente: string | null;
  nombre_conyuge: string | null;
  telefono_conyuge: string | null;
  cantidad_hermanos: string | null;
  casa_propia: string | null;
  casa_arrendada: string | null;
  observaciones: string | null;
  observacion_especial: string | null;
  padece: string | null;
  cual: string | null;
  recibe_medicamento: string | null;
  cual_medicamento: string | null;
  padecimientos: string | null;
  observaciones_medicas: string | null;
  habitos: string | null;
  otros_habitos: string | null;
  cepilla: string | null;
  usa_seda: string | null;
  denticion_permanente: string | null;
  relacion_canina: string | null;
  relacion_molar: string | null;
  overjet: string | null;
  overbite: string | null;
  mordida_abierta: string | null;
  mordida_cruzada: string | null;
  diastemas: string | null;
  dientes_ausentes: string | null;
  manchas_dentales: string | null;
  fracturas: string | null;
  higiene_oral: string | null;
  caries: string | null;
  peridonto: string | null;
  disfuncion_articular: string | null;
  otros_hallazgos: string | null;
  diagnostico_medico_general: string | null;
  diagnostico_intraoral: string | null;
  diagnostico_dental: string | null;
  diagnostico_periodontal: string | null;
  diagnostico_endodontico: string | null;
  plan_tratamiento: string | null;
  plan_de_tratamiento: string | null;
  remisiones_odontologicas: string | null;
  previo: string | null;
  tiempo_ortodoncia: string | null;
  como_supo_de_nosotros: string | null;
  remitido_por: string | null;
  odontologo_personal: string | null;
  tipo: string | null;
  profesional: string | null;
  razon_inicia: string | null;
  terminado: string | null;
  retencion: string | null;
  entidad: string | null;
  tipo_de_usuario: string | null;
  cuotas: Cuotas | null;
  municipio_nombre: string | null;
  municipio_departamento: string | null;
  ocupacion_nombre: string | null;
}

export interface Cuotas {
  cuota1: string | null;
  cuota2: string | null;
  cuota3: string | null;
  cuota4: string | null;
  cuota5: string | null;
  cuota6: string | null;
  cuota7: string | null;
  cuota8: string | null;
  cuota9: string | null;
  cuota10: string | null;
  cuota11: string | null;
  cuota12: string | null;
  cuota13: string | null;
  cuota14: string | null;
  cuota15: string | null;
  cuota16: string | null;
  cuota17: string | null;
  cuota18: string | null;
  cuota19: string | null;
  cuota20: string | null;
  cuota21: string | null;
  cuota22: string | null;
  cuota23: string | null;
  cuota24: string | null;
  cuota25: string | null;
  cuota26: string | null;
  cuota27: string | null;
  cuota28: string | null;
  cuota29: string | null;
  cuota30: string | null;
  cuota31: string | null;
  cuota32: string | null;
  cuota33: string | null;
  cuota34: string | null;
  cuota35: string | null;
  cuota36: string | null;
}

export interface Cita {
  ind: number;
  fecha: string | null;
  horas: string | null;
  paciente: number;
  procedimiento: string | null;
  consultorio: string | null;
  especialista: string | null;
  asistio: string | null;
  confirmo: string | null;
  tipo: string | null;
  motivo: string | null;
  duracion: number | null;
  enatencion: string | null;
  anotaciones_cita: string | null;
  adicional_cita: string | null;
  hora_llegada: string | null;
  hora_salida: string | null;
  proxima_cita: string | null;
  reasignada: string | null;
  inicio: string | null;
  evolucion: string | null;
}

export interface CitaCancelada {
  ind: string | null;
  fecha: string | null;
  horas: string | null;
  paciente: string | null;
  procedimiento: string | null;
  consultorio: string | null;
  especialista: string | null;
  motivo: string | null;
  borradopor: string | null;
  fechaborra: string | null;
  horaborra: string | null;
}

export interface Evolucion {
  ind: number;
  paciente: string;
  fecha: string | null;
  hora: string | null;
  procedimiento: string | null;
  mevolucion: string | null;
  diagnostico_dental: string | null;
  diagnostico_principal: string | null;
  diagnostico_pulpar: string | null;
  causa_externa: string | null;
  valor_consulta: number | null;
  valor_copago: number | null;
  neto: number | null;
  tipoprocedimiento: string | null;
  ambito: string | null;
  finalidad: string | null;
  personal_que_atiende: string | null;
  valorproc: number | null;
  factura_consulta: string | null;
  auxiliar: string | null;
  profesional: string | null;
  [key: string]: unknown;
}

export interface Abono {
  ind: number;
  paciente: number;
  identificacion: string | null;
  recibo: number | null;
  valor_abono: number | null;
  fecha: string | null;
  hora: string | null;
  forma_de_pago: string | null;
  cheque: string | null;
  banco: string | null;
  detalle: string | null;
  concita: string | null;
  acentado_por: string | null;
  doctor: string | null;
  tipo: string | null;
  tipo_pago: string | null;
  total: number | null;
}

export interface Pago {
  ind: number;
  paciente: number;
  tipo: string | null;
  no: string | null;
  fecha: string | null;
  descripcion: string | null;
  costo_tratamiento: string | null;
  cuota_inicial1: string | null;
  cuota_inicial2: number | null;
  cuota_inicial3: number | null;
  cuota_inicial4: number | null;
  nocuotas: string | null;
  ncuotas: number | null;
  valor_cuota: number | null;
  plan: string | null;
  cancelado: string | null;
  fecha_inicio: string | null;
  cuota1: string | null;
  cuota2: string | null;
  cuota3: string | null;
  cuota4: string | null;
  cuota5: string | null;
  cuota6: string | null;
  cuota7: string | null;
  cuota8: string | null;
  cuota9: string | null;
  cuota10: string | null;
  cuota11: string | null;
  cuota12: string | null;
  cuota13: string | null;
  cuota14: string | null;
  cuota15: string | null;
  cuota16: string | null;
  cuota17: string | null;
  cuota18: string | null;
  cuota19: string | null;
  cuota20: string | null;
  cuota21: string | null;
  cuota22: string | null;
  cuota23: string | null;
  cuota24: string | null;
  cuota25: string | null;
  cuota26: string | null;
  cuota27: string | null;
  cuota28: string | null;
  cuota29: string | null;
  cuota30: string | null;
  cuota31: string | null;
  cuota32: string | null;
  cuota33: string | null;
  cuota34: string | null;
  cuota35: string | null;
  cuota36: string | null;
  paciente_paga_completo: string | null;
  estado: string | null;
}

export interface DetallePago {
  ind: number;
  observacion: string | null;
  fecha: string | null;
  tipo: string | null;
  hora: string | null;
}

export interface Procedimiento {
  ind: number;
  codigo: string | null;
  nombre: string | null;
  duracion: string | null;
  color: string | null;
  tipocita: string | null;
  etapa: string | null;
  tipoconsulta: string | null;
  tipoprocedimiento: string | null;
}

export interface Especialidad {
  ind: number;
  nombre: string | null;
  descripcion: string | null;
  codigo: string | null;
  activa: string | null;
  grupo: string | null;
  abreviatura: string | null;
}

export interface Entidad {
  ind: number;
  nit: string | null;
  nocodigo: string | null;
  nombres: string | null;
  direccion: string | null;
  ciudad: string | null;
  telefono: string | null;
  email: string | null;
}

export interface HojaVida {
  ind: number;
  identificacion: number;
  nombres: string | null;
  apellidos: string | null;
  fecha: string | null;
  edad: string | null;
  especialidad: string | null;
  telefono: string | null;
  email: string | null;
  estado: string | null;
  ciudad: string | null;
  residencia: string | null;
  estadocivil: string | null;
  activo: string | null;
  tipo: string | null;
  nombresp: string | null;
  tarjeta_profesional: string | null;
  otorgadopor: string | null;
  tiene_foto: number;
  [key: string]: unknown;
}

export interface DashboardResumen {
  pacientes_activos: number;
  citas_hoy: number;
  citas_semana: number;
  citas_manana: number;
  cartera_pendiente: number;
  abonos_hoy: number;
  nuevos_mes: number;
  proximas_citas: (Cita & { nombres: string; historia: number })[];
}

export interface DatosEmpresa {
  ind: number;
  nit: string | null;
  nombre_empresa: string | null;
  ciudad: string | null;
  direccion: string | null;
  telefono: string | null;
  email: string | null;
  web: string | null;
  representante_legal: string | null;
  especialidad: string | null;
  sede: string | null;
  tiene_logo: number;
}
