# Plan de trabajo: 6 integrantes, 2 semanas con agentes de IA

## Equipo
| Persona | Módulo | RF | Dificultad | Rol extra |
|---|---|---|---|---|
| Emilio | Seguridad, administración e integración | 01–03, 28 | ⭐⭐⭐⭐ | Coordinador técnico: proyecto base, migraciones, roles, revisión de Pull Requests |
| Karpite | Contribuyentes, establecimientos y predios | 04–12 | ⭐⭐ | Datos de prueba para todo el grupo |
| Adrian | Liquidación predial | 13–21 | ⭐⭐⭐⭐⭐ | Validar los cálculos con el Cuerpo de Bomberos |
| Leo | Formas de pago e historial | 22–26 | ⭐⭐⭐⭐ | Modal de pagos y plantilla PDF reutilizables |
| Santiago | Permisos de funcionamiento y caja | 30–33, 39–41 | ⭐⭐⭐ | Coordinador del informe, de las evidencias y del contacto con la institución |
| Erick | Certificados de solvencia | 27, 29, 34–38 | ⭐⭐⭐ | Pruebas generales (QA) y manual de usuario |

## Decisiones tomadas
- Se migra **todo GESADMEN a la web** (RF-01 a RF-41), con un diseño visual nuevo.
- Base de datos SQL: **MySQL 8 / MariaDB**.
- Stack: **Laravel + Filament + Livewire + Tailwind**, PDF con **laravel-dompdf**, roles con **spatie/laravel-permission**.
- Entorno local: **Laragon** + Node.js + Git.
- Repositorio: `github.com/TKennedysg/vinculacion-cuerpo-de-bomberos`, **una rama por módulo**, integración por Pull Request revisado por Emilio y `main` protegida (ver `flujo-git.md`).
- Pendiente de confirmar con el tutor: si se exigen los 41 requisitos en este periodo o por fases.

## Dependencias
```
Emilio (base, migraciones, roles) ──► todos
Karpite (contribuyentes, predios, seeders) ──► Adrian, Santiago, Erick
Adrian (valor a cancelar) ──► Leo (modal de pagos)
Leo (modal de pagos + PDF) ──► Santiago, Erick
Adrian (adeuda()) ──► Erick (RF-37)
Todos los cobros ──► Santiago (cortes de caja RF-40 y 41)
```

## Acuerdos entre módulos (definir el día 1)
| Componente | Dueño | Lo usan | Interfaz acordada |
|---|---|---|---|
| `SecuenciaTesoreriaService::siguiente($puntoEmision)` | Emilio | Adrian, Santiago, Erick | Devuelve `EEE-PPP-SSSSSSSSS` |
| `SecuenciaTesoreriaService::siguienteParaComprobante($puntoEmision)` | Emilio | Leo, Adrian, Santiago, Erick | Devuelve `punto_emision_id`, `secuencial` de 9 dígitos y `numero` completo |
| `AnulacionService::anular($comprobante, $motivo)` | Emilio | Leo, Santiago, Erick | Estado X + auditoría |
| `LiquidacionPredialService::calcular($predio, $anios)` | Adrian | Leo | Filas por año + total |
| `EstadoCuentaPredialService::adeuda($contribuyente)` | Adrian | Erick | `bool` + años adeudados |
| Modal `FormaPago` (Livewire) | Leo | Adrian, Santiago, Erick | Recibe el valor a cancelar; emite las formas de pago cuadradas |
| `RegistroCobroService::registrar(...)` | Leo | Adrian, Santiago, Erick | Guarda el cobro en una transacción |
| Plantilla PDF de comprobante | Leo | Adrian, Santiago, Erick | Vista Blade en `resources/views/pdf/` |

Mientras un componente no esté listo, se usa una versión simulada (devolviendo datos fijos) para no bloquearse.

## Semana 1
| Día | Tarea | Quién |
|---|---|---|
| 1 | Leer los documentos, instalar el entorno, revisar `base-de-datos.md`, firmar los acuerdos, crear cada uno su rama | Todos |
| 1 | Proteger `main` en GitHub (Settings → Branches) | Emilio |
| 1–2 | Organizar la reunión con el Cuerpo de Bomberos y el tutor (`preguntas-bomberos.md`) | Santiago |
| 1–2 | Proyecto base, plantilla visual, migraciones, login | Emilio |
| 2 | Bocetos de pantallas y lógica en pseudocódigo | Karpite, Adrian, Leo, Santiago, Erick |
| 3–5 | CRUD de contribuyentes, predios, establecimientos + seeders | Karpite |
| 3–5 | Roles, permisos, administración, secuencias, anulación | Emilio |
| 3–5 | `LiquidacionPredialService` con pruebas + pantalla de liquidación | Adrian |
| 3–5 | Modal de formas de pago + `RegistroCobroService` + plantilla PDF | Leo |
| 3–5 | Permisos de funcionamiento (RF-30 a 33) + arqueo (RF-39) | Santiago |
| 3–5 | Formulario de solvencia, leyenda dinámica, varios predios | Erick |
| 5 | **Integración 1** en `main` + demo interna | Todos |

## Semana 2
| Día | Tarea | Quién |
|---|---|---|
| 6–9 | Terminar los módulos; conectar los cobros con el modal real | Todos |
| 6–9 | Cortes de caja (RF-40 y 41) | Santiago |
| 6–9 | Historiales y alerta de deuda | Leo, Erick |
| 8 | **Integración 2** | Todos |
| 10–11 | Prueba completa: registrar → liquidar → pagar → comprobante → solvencia → permiso → corte de caja | Erick coordina, todos prueban |
| 12–13 | Corrección de errores, manual de usuario, capturas, informe | Todos (Santiago une el informe) |
| 14 | Demo final y entrega | Todos |

## Evidencias para vinculación (coordina Santiago)
- Fotos y actas de las reuniones con la institución.
- Capturas del avance de cada módulo (cada persona sube las suyas a una carpeta compartida).
- Historial de commits en GitHub.
- Lista de asistencia a la capacitación.
