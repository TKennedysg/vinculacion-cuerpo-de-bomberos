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
- **Autenticación y roles:** el login de Filament con recuperación de contraseña, y `spatie/laravel-permission`
  para los perfiles ADMINISTRADOR, RECAUDADOR y CONTRIBUYENTE.
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
