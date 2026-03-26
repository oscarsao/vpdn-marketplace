# VPDN Marketplace

Plataforma de marketplace para compraventa e inversion en negocios. Portal B2B con analisis financiero, sistema de asesores, y herramientas de valoracion impulsadas por IA.

## Tech Stack

| Componente | Tecnologia |
|-----------|-----------|
| Frontend | Vue 3 + Vite + Tailwind CSS 4 + Pinia |
| Backend | Laravel 10 + PHP 8.3 |
| Base de datos | MySQL 8.0 |
| Autenticacion | JWT (tymon/jwt-auth) |
| Infraestructura | Docker + Nginx |

## Funcionalidades principales

- Marketplace de negocios con busqueda y filtros avanzados
- Analisis financiero y valoracion de negocios
- Motor de IA para scoring de oportunidades y recomendaciones
- Sistema de asesores con asignacion de clientes
- Comparador de negocios side-by-side
- Dashboard de estadisticas del mercado
- Calendario de citas y gestion de solicitudes
- Panel de administracion completo
- Vista de mapa interactivo
- Sistema de notificaciones

## Requisitos previos

- Docker & Docker Compose
- Node.js >= 18
- npm >= 9

## Instalacion rapida

### 1. Clonar el repositorio

```bash
git clone https://github.com/oscarsao/vpdn-marketplace.git
cd vpdn-marketplace
```

### 2. Backend (Laravel + Docker)

```bash
cd coyag-backend
cp .env.example .env
docker-compose up -d --build
docker exec coyag-app php artisan key:generate
docker exec coyag-app php artisan jwt:secret
docker exec coyag-app php artisan migrate --seed
```

El backend estara disponible en `http://localhost:8000`

### 3. Frontend (Vue 3)

```bash
cd coyag-frontend
npm install
npm run dev
```

El frontend estara disponible en `http://localhost:5173`

### 4. Configuracion del frontend

Crea un archivo `.env` en `coyag-frontend/`:

```
VITE_API_URL=http://localhost:8000/api/v1/
VITE_APP_NAME=COYAG VPDN
VITE_USE_MOCK=false
```

> Cambia `VITE_USE_MOCK=true` para ejecutar con datos de ejemplo sin backend.

## Estructura del proyecto

```
vpdn-marketplace/
├── coyag-frontend/       # Vue 3 SPA
│   ├── src/
│   │   ├── views/        # Paginas (auth, admin, portal, client)
│   │   ├── components/   # Componentes reutilizables
│   │   ├── stores/       # Pinia stores
│   │   ├── data/         # AI engine, statistics, mock data
│   │   └── api/          # Axios client y mock API
│   └── ...
├── coyag-backend/        # Laravel 10 API
│   ├── app/
│   │   ├── Http/Controllers/  # 60+ controllers
│   │   └── Models/            # 50+ models
│   ├── routes/api.php         # API routes
│   ├── docker-compose.yml     # Docker orchestration
│   └── Dockerfile
└── README.md
```

## API

Base URL: `http://localhost:8000/api/v1/`

Endpoints principales:
- `POST /auth/login` - Autenticacion JWT
- `GET /business/index` - Listar negocios
- `GET /business/show/{id}` - Detalle de negocio
- `GET /client/profile` - Perfil del cliente
- `GET /statistics/*` - Estadisticas del mercado
- `POST /ai/*` - Funciones de IA

## Licencia

Todos los derechos reservados.
