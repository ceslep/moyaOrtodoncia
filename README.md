# Moya Ortodoncia - Sistema de Consultas

SPA de solo consulta (read-only) sobre la base de datos `iedeocci_adm` de una clínica odontológica.

## Stack

- **Frontend:** Svelte 5 (runes) + TypeScript + TailwindCSS 4 + Vite 8
- **Backend:** PHP 8.2+ con PDO + MySQL
- **Base de datos:** MySQL/MariaDB `iedeocci_adm`

## Estructura

```
moyaOrtodoncia/
├── backend/
│   ├── public/
│   │   ├── .htaccess          ← reescritura de URLs
│   │   └── index.php          ← front controller
│   ├── src/
│   │   ├── Config/Database.php
│   │   ├── Controllers/       ← 8 controladores (solo GET)
│   │   ├── Repositories/      ← 8 repositorios (solo SELECT)
│   │   └── Support/           ← JsonResponse, Pagination, Validator
│   ├── .env                   ← credenciales BD (no versionado)
│   └── .env.example
├── src/                       ← Frontend Svelte 5
│   ├── lib/
│   │   ├── api/index.ts       ← módulos fetch por dominio
│   │   ├── types/index.ts     ← interfaces TypeScript
│   │   └── components/        ← 7 componentes reutilizables
│   ├── routes/                ← 7 vistas/módulos
│   ├── App.svelte             ← state router
│   └── main.ts
├── dist/                      ← build de producción
└── package.json
```

## Instalación

### Backend (Apache XAMPP)

1. Asegúrate de que Apache y MySQL estén corriendo en XAMPP
2. El directorio `backend/` ya está dentro de `htdocs/moyaOrtodoncia/`
3. Accede a: `http://localhost/moyaOrtodoncia/backend/public/index.php?route=api/pacientes`
4. Credenciales en `backend/.env` (usuario de solo lectura)

### Frontend

```bash
# Instalar dependencias
npm install

# Desarrollo (con proxy al backend)
npm run dev

# Build de producción
npm run build
```

El frontend en desarrollo corre en `http://localhost:5173/moyaOrtodoncia/` y proxea las llamadas al backend.

### Build de producción

```bash
npm run build
```

Los archivos se generan en `dist/`. Copia el contenido a tu directorio de Apache.

## Variables de entorno

Archivo `backend/.env`:

```env
DB_HOST=162.241.203.120
DB_NAME=iedeocci_adm
DB_USER=iedeocci_adm
DB_PASS=tu_password
DB_CHARSET=utf8mb4
DB_PORT=3306
```

## API Endpoints (solo lectura)

```
GET /api/pacientes?search=&page=&per_page=
GET /api/pacientes/{ind}
GET /api/pacientes/{ind}/foto
GET /api/pacientes/{ind}/historia-clinica
GET /api/pacientes/{ind}/citas?estado=&desde=&hasta=
GET /api/pacientes/{ind}/citas-canceladas
GET /api/pacientes/{ind}/evoluciones
GET /api/pacientes/{ind}/abonos
GET /api/pacientes/{ind}/pagos
GET /api/pacientes/{ind}/detalles-pagos
GET /api/citas?desde=&hasta=&especialista=&consultorio=&estado=&page=&per_page=
GET /api/abonos?desde=&hasta=&forma_de_pago=&page=&per_page=
GET /api/procedimientos?search=&page=&per_page=
GET /api/especialidades?search=
GET /api/entidades?search=
GET /api/personal?search=&page=&per_page=
GET /api/personal/{ind}/{identificacion}
GET /api/dashboard/resumen
GET /api/datos-empresa
```

## Seguridad

- **Solo lectura:** Cero operaciones INSERT/UPDATE/DELETE en todo el backend
- **Prepared statements:** Todas las queries usan PDO con parámetros vinculados
- **Sin exposición de blobs:** Campos `longblob` (fotos, firmas, logos) se sirven en endpoints dedicados con `Content-Type` correcto
- **Errores genéricos:** Los errores de PDO se loguean en servidor, nunca se devuelven al cliente
- **Usuario de solo lectura:** Usar `GRANT SELECT ON iedeocci_adm.* TO 'iedeocci_readonly'@'%'`

## Relaciones FK verificadas

```
paciente.historia ──────┬──→ citas.paciente
                       ├──→ cppre.paciente
                       ├──→ evolucion.paciente
                       ├──→ abonos.paciente
                       ├──→ pagos.paciente
                       ├──→ detallespagos.paciente
                       ├──→ canceladas.paciente
                       └──→ cppredata.historia
```

## Módulos

1. **Dashboard** — Métricas: pacientes activos, citas hoy/semana, cartera pendiente, abonos
2. **Pacientes** — Búsqueda global, tabla paginada, ficha detallada
3. **Ficha Paciente** — Pestañas: datos personales, info médica, citas, evoluciones (con odontograma SVG), financiero, historia clínica
4. **Agenda** — Citas globales con filtros de fecha, especialista, consultorio, estado
5. **Financiero** — Abonos globales con filtros
6. **Catálogos** — Procedimientos, especialidades, entidades
7. **Personal** — Hojas de vida con ficha de detalle
