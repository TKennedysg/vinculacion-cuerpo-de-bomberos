# Vinculación: Cuerpo de Bomberos de Montecristi (GESADMEN Web)

Proyecto de vinculación con la sociedad: migración a la web de **GESADMEN**, el sistema de
gestión y recaudación del Benemérito Cuerpo de Bomberos de Montecristi. Cubre la contribución
predial (0.15 por mil), los permisos de funcionamiento, los certificados de solvencia y la caja.

## Equipo
| Integrante | Módulo |
|---|---|
| Emilio | Seguridad, administración e integración (coordinador técnico) |
| Karpite | Contribuyentes, establecimientos y predios |
| Adrian | Liquidación predial |
| Leo | Formas de pago e historial |
| Santiago | Permisos de funcionamiento y caja (coordinador del informe) |
| Erick | Certificados de solvencia (QA y manual) |

## Stack
Laravel · Filament · Livewire · Tailwind CSS · MySQL 8 / MariaDB · laravel-dompdf · spatie/laravel-permission

## Documentación
- [`CLAUDE.md`](CLAUDE.md): reglas del proyecto (también las leen los agentes de IA).
- [`docs/requerimientos.md`](docs/requerimientos.md): requisitos RF-01 a RF-41 por módulo y responsable.
- [`docs/plan-de-trabajo.md`](docs/plan-de-trabajo.md): cronograma de 2 semanas, dependencias y acuerdos.
- [`docs/base-de-datos.md`](docs/base-de-datos.md): modelo de datos.
- [`docs/reglas-de-negocio.md`](docs/reglas-de-negocio.md): cálculos confirmados y pendientes.
- [`docs/flujo-git.md`](docs/flujo-git.md): ramas por módulo, comandos y reglas de Pull Request.
- [`docs/preguntas-bomberos.md`](docs/preguntas-bomberos.md): dudas para la institución y el tutor.
- [`docs/gesadmen_requerimientos.pdf`](docs/gesadmen_requerimientos.pdf): documento original de requisitos.

## Cómo empezar
1. Instalar **Laragon** (PHP 8.2+, MySQL, Composer), **Node.js** y **Git**.
2. Clonar el repositorio y crear la rama de tu módulo (ver `docs/flujo-git.md`).
3. Cuando Emilio suba el proyecto base: `composer install`, `npm install`, copiar `.env.example` a `.env`,
   `php artisan key:generate`, `php artisan migrate --seed`, `npm run dev` y `php artisan serve`.
