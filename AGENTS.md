# CLAUDE.md — Reglas del proyecto para agentes de IA

Proyecto de vinculación: **migración a la web de GESADMEN**, el sistema de recaudación del
Benemérito Cuerpo de Bomberos de Montecristi (contribución predial 0.15 por mil, permisos de
funcionamiento, certificados de solvencia y caja). Lo desarrolla un equipo de **6 personas**,
cada una con un módulo propio. Todo agente (Claude Code, Cursor, etc.) que trabaje en este
repositorio DEBE seguir estas reglas para que el código de los 6 integrantes sea consistente.

## Documentos de referencia (leer antes de programar)
- `docs/gesadmen_requerimientos.pdf`: requisitos originales RF-01 a RF-41, con capturas del sistema actual.
- `docs/requerimientos.md`: los requisitos por módulo y responsable, con las tareas detalladas.
- `docs/base-de-datos.md`: modelo de datos común. **No crear tablas ni columnas fuera de este
  documento sin actualizarlo primero y avisar al grupo.**
- `docs/reglas-de-negocio.md`: cálculos y validaciones. Si una regla está marcada como
  **PENDIENTE**, no inventar la fórmula: dejar un `TODO(regla-pendiente)` y una función aislada fácil de cambiar.
- `docs/plan-de-trabajo.md`: cronograma, dependencias y acuerdos entre módulos.
- `docs/preguntas-bomberos.md`: dudas abiertas con la institución y el tutor.

## Módulos y responsables
Antes de escribir código, identifica a qué módulo pertenece la tarea. **No modifiques archivos
de otro módulo**; si hace falta, deja un comentario `TODO(<responsable>)` y avisa.

| # | Módulo | RF | Responsable | Rama |
|---|---|---|---|---|
| 1 | Seguridad, administración e integración | 01–03, 28 | Emilio | `modulo-1-seguridad` |
| 2 | Contribuyentes, establecimientos y predios | 04–12 | Karpite | `modulo-2-contribuyentes` |
| 3 | Liquidación predial | 13–21 | Adrian | `modulo-3-liquidacion` |
| 4 | Formas de pago e historial de contribuciones | 22–26 | Leo | `modulo-4-pagos` |
| 5 | Permisos de funcionamiento y caja | 30–33, 39–41 | Santiago | `modulo-5-permisos-caja` |
| 6 | Certificados de solvencia | 27, 29, 34–38 | Erick | `modulo-6-solvencias` |

Componentes compartidos (respetar su interfaz; los cambios los hace su dueño):
- Proyecto base, migraciones, roles y anulación (`AnulacionService`): **Emilio**.
- Datos de prueba (`database/seeders`): **Karpite**.
- Cálculo predial (`LiquidacionPredialService`) y consulta de deuda (`EstadoCuentaPredialService::adeuda()`): **Adrian**.
- Modal de formas de pago (componente Livewire reutilizable), `RegistroCobroService` y plantilla PDF de comprobantes: **Leo**.
- Generación de secuencias de tesorería (`SecuenciaTesoreriaService`): **Emilio**.

## Stack tecnológico
- **Backend:** Laravel (última versión estable) sobre PHP 8.2 o superior, con Composer.
- **Frontend:** Filament para el panel administrativo (formularios, tablas, filtros, paginación),
  Livewire para las pantallas especiales (liquidación predial, modal de formas de pago, arqueo de caja)
  y Tailwind CSS para el diseño. Las vistas se hacen con Blade.
- **Base de datos:** MySQL 8 (o MariaDB). Se accede solo con Eloquent o el Query Builder, nunca
  concatenando SQL. Todo cambio de tablas se hace con migraciones de Laravel.
- **Autenticación y roles:** el login de Filament con recuperación de contraseña. Los perfiles
  ADMINISTRADOR, RECAUDADOR y CONTRIBUYENTE se manejan con la tabla `perfil` y los métodos del
  modelo `User` (`esAdministrador()`, `esRecaudador()` y `esContribuyente()`), con Policies en el
  backend. `spatie/laravel-permission` permanece instalado hasta que el equipo autorice su retiro.
- **Reportes/PDF:** `barryvdh/laravel-dompdf` para comprobantes, certificados y cortes de caja.
- **Pruebas:** PHPUnit o Pest, sobre todo para los cálculos de `docs/reglas-de-negocio.md`.
- **Entorno local recomendado (Windows):** Laragon (PHP + MySQL + Composer) y Node.js.
- **Seguridad:** el archivo `.env` nunca se sube a GitHub (solo `.env.example`). Se usa HTTPS en producción.
  Los permisos se validan en el backend con Policies. Todo cobro y toda anulación queda auditada.
  Se manejan datos personales (cédulas, correos, teléfonos), protegidos por la Ley Orgánica de
  Protección de Datos Personales de Ecuador: no usar datos reales en pruebas ni en capturas.

### Organización del código
- Cálculos y lógica de negocio en clases de servicio (`app/Services`), por ejemplo `LiquidacionPredialService`.
  Nunca dentro de las vistas ni de los componentes de Filament.
- Un Resource de Filament por entidad (Contribuyente, Predio, Establecimiento, etc.).
- Pantallas especiales como páginas Livewire en `app/Livewire/<Modulo>/`.
- Plantillas PDF en `resources/views/pdf/`.
- Datos de prueba en `database/seeders` (los mantiene Karpite).

## Convenciones
- **Idioma:** la interfaz y los mensajes al usuario van en español. Los nombres de tablas y
  columnas van en español y en `snake_case` (ej. `contribuyente`, `clave_catastral`).
- **Dinero:** usar siempre tipo decimal (`DECIMAL(12,2)`), nunca float. Redondear a 2 decimales al final de cada cálculo.
- **Estados:** usar los códigos del sistema original. Documentos: `A` = Activo, `X` = Anulado.
  Registros: `ACTIVO` / `INACTIVO` (predios además `ANULADO` / `TRANSFERIDO`).
- **No borrar:** nada se elimina físicamente. Anular = cambiar el estado y guardar el motivo, el usuario y la fecha.
- **Auditoría:** toda transacción guarda `usuario_id`, `created_at` y `updated_at`.
- **Roles:** `ADMINISTRADOR`, `RECAUDADOR`, `CONTRIBUYENTE`. Validar los permisos en el backend, no solo ocultar botones.
- **Secuencias de tesorería:** formato `EEE-PPP-SSSSSSSSS` (establecimiento, punto de emisión,
  secuencial). Generarlas dentro de una transacción de base de datos para evitar duplicados.
- **Cobros:** todo cobro (predial, permiso o solvencia) se guarda en **una sola transacción**
  (comprobante + detalle + formas de pago) a través de `RegistroCobroService`.
- **Interfaz:** usar la plantilla común (menú lateral con Contribuyentes, Recaudaciones y Administración).
  El diseño es nuevo y moderno. Las capturas del PDF indican la función de cada pantalla, no su aspecto.

## Flujo de trabajo con Git
Detalle y comandos en `docs/flujo-git.md`.
- **Una rama por módulo** (tabla de arriba). **Nunca hacer commit directo en `main`**; `main` está
  protegida y solo recibe Pull Requests revisados por Emilio.
- Commits pequeños con el RF en el mensaje: `RF-20: cálculo desglosado por año fiscal`.
- Pull Requests pequeños y frecuentes (uno por requisito o cada 1 a 2 días), con los RF en el título.
- Antes de empezar el día: `git pull origin main` en `main` y luego `git merge main` en la rama propia.
- Los archivos compartidos (migraciones, roles, seeders, modal de pagos, plantilla PDF) solo los
  modifica su dueño.
- Nunca subir `.env`.

## Cómo pedirle trabajo al agente
1. Un requisito a la vez: pegar el texto del RF y mencionar la captura del PDF.
2. Pedir primero el plan, luego el código, luego las pruebas.
3. Revisar y entender el código antes de hacer commit: en la defensa les van a preguntar cómo funciona.

===

<laravel-boost-guidelines>
=== foundation rules ===

# Laravel Boost Guidelines

## Foundational Context

This application is a Laravel application running on PHP 8.3. Always use the APIs that match the installed major version of each package — do not assume a version.

Before relying on a package's API, confirm its installed version:
- PHP packages: run `composer show --direct` to list direct dependencies with versions, or `composer show <vendor/package>` for a single package.
- JS packages: check `package.json` for the installed versions.

## Skills Activation

This project has domain-specific skills available in `**/skills/**`. You MUST activate the relevant skill whenever you work in that domain—don't wait until you're stuck.

## Conventions

- You must follow all existing code conventions used in this application. When creating or editing a file, check sibling files for the correct structure, approach, and naming.
- Use descriptive names for variables and methods. For example, `isRegisteredForDiscounts`, not `discount()`.
- Check for existing components to reuse before writing a new one.

## Verification Scripts

- Do not create verification scripts or tinker when tests cover that functionality and prove they work. Unit and feature tests are more important.

## Application Structure & Architecture

- Stick to existing directory structure; don't create new base folders without approval.
- Do not change the application's dependencies without approval.

## Frontend Bundling

- If a frontend change doesn't show in the UI or you get a "Unable to locate file in Vite manifest" error, run `npm run build` or ask the user to run `npm run dev` or `composer run dev`.

## Documentation Files

- You must only create documentation files if explicitly requested by the user.

=== boost rules ===

# Laravel Boost

## Project Rules

- This project contains committed, area-grouped rules in `.ai/rules` when that directory exists, including path-scoped framework guidelines under `.ai/rules/boost`. Before you enter plan mode or create/edit any file, you MUST first: open @.ai/rules/index.md (it maps file globs to rule files), read every rule file whose globs cover the path(s) in scope, and run `grep -rin 'keyword' .ai/rules` to catch what a path match alone misses. Do not write code until you have read and are following every matching rule. If `.ai/rules` does not exist, continue without it.

## Artisan

- Run Artisan commands directly via the command line (e.g., `php artisan route:list`). Use `php artisan list` to discover available commands and `php artisan [command] --help` to check parameters.
- Inspect routes with `php artisan route:list`. Filter with: `--method=GET`, `--name=users`, `--path=api`, `--except-vendor`, `--only-vendor`.
- Read configuration values using dot notation: `php artisan config:show app.name`, `php artisan config:show database.default`. Or read config files directly from the `config/` directory.

## Tinker

- Execute PHP in app context for debugging and testing code. Do not create models without user approval, prefer tests with factories instead. Prefer existing Artisan commands over custom tinker code.
- Always use single quotes to prevent shell expansion: `php artisan tinker --execute 'Your::code();'`
  - Double quotes for PHP strings inside: `php artisan tinker --execute 'User::where("active", true)->count();'`

=== php rules ===

# PHP

- Always use curly braces for control structures, even for single-line bodies.
- Use PHP 8 constructor property promotion: `public function __construct(public GitHub $github) { }`. Do not leave empty zero-parameter `__construct()` methods unless the constructor is private.
- Use explicit return type declarations and type hints for all method parameters: `function isAccessible(User $user, ?string $path = null): bool`
- Use TitleCase for Enum keys: `FavoritePerson`, `BestLake`, `Monthly`.
- Prefer PHPDoc blocks over inline comments. Only add inline comments for exceptionally complex logic.
- Use array shape type definitions in PHPDoc blocks.

=== deployments rules ===

# Deployment

- Laravel can be deployed using [Laravel Cloud](https://cloud.laravel.com/), which is the fastest way to deploy and scale production Laravel applications.
- Activate the `deploying-to-cloud` skill whenever deploying to Laravel Cloud, configuring Cloud environments or resources, using the Cloud CLI, or troubleshooting Cloud deployments.

=== laravel/core rules ===

# Do Things the Laravel Way

- Use `php artisan make:` commands to create new files (i.e. migrations, controllers, models, etc.). You can list available Artisan commands using `php artisan list` and check their parameters with `php artisan [command] --help`.
- If you're creating a generic PHP class, use `php artisan make:class`.
- Pass `--no-interaction` to all Artisan commands to ensure they work without user input. You should also pass the correct `--options` to ensure correct behavior.

### Model Creation

- When creating new models, create useful factories and seeders for them too. Ask the user if they need any other things, using `php artisan make:model --help` to check the available options.

## APIs & Eloquent Resources

- For APIs, default to using Eloquent API Resources and API versioning unless existing API routes do not, then you should follow existing application convention.

## URL Generation

- When generating links to other pages, prefer named routes and the `route()` function.

## Testing

- When creating models for tests, use the factories for the models. Check if the factory has custom states that can be used before manually setting up the model.
- Faker: Use methods such as `$this->faker->word()` or `fake()->randomDigit()`. Follow existing conventions whether to use `$this->faker` or `fake()`.
- When creating tests, make use of `php artisan make:test [options] {name}` to create a feature test, and pass `--unit` to create a unit test. Most tests should be feature tests.

=== pint/core rules ===

# Laravel Pint Code Formatter

- If you have modified any PHP files, you must run `vendor/bin/pint --dirty --format agent` before finalizing changes to ensure your code matches the project's expected style.
- Do not run `vendor/bin/pint --test --format agent`, simply run `vendor/bin/pint --format agent` to fix any formatting issues.

=== phpunit/core rules ===

# PHPUnit

- This project uses PHPUnit. Create tests with `php artisan make:test --phpunit {name}`.
- Do not include the test suite directory in `{name}`. Use `SomeFeatureTest`, not `Feature/SomeFeatureTest`.
- Read the `testing-best-practices` skill for guidance on coverage, naming, structure, dependency isolation, and review.

## Running Tests

- Run the narrowest set of tests that covers the change. Pass a file path or `--filter=testName` to `php artisan test --compact`.
- Rerun a test after each change to it.
- Run `vendor/bin/phpunit` to call the test runner directly. It accepts the same file path and `--filter=testName` arguments.

</laravel-boost-guidelines>
