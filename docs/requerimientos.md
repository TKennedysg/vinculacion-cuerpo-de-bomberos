# Requerimientos funcionales por módulo

Fuente: `gesadmen_requerimientos.pdf` (RF-01 a RF-41). Dificultad de ⭐ (fácil) a ⭐⭐⭐⭐⭐ (difícil),
medida por la lógica a programar, no por la cantidad de requisitos.

| # | Módulo | RF | Responsable | Dificultad |
|---|---|---|---|---|
| 1 | Seguridad, administración e integración | 01–03, 28 | Emilio | ⭐⭐⭐⭐ |
| 2 | Contribuyentes, establecimientos y predios | 04–12 | Karpite | ⭐⭐ |
| 3 | Liquidación predial | 13–21 | Adrian | ⭐⭐⭐⭐⭐ |
| 4 | Formas de pago e historial | 22–26 | Leo | ⭐⭐⭐⭐ |
| 5 | Permisos de funcionamiento y caja | 30–33, 39–41 | Santiago | ⭐⭐⭐ |
| 6 | Certificados de solvencia | 27, 29, 34–38 | Erick | ⭐⭐⭐ |

---

## Módulo 1 — Seguridad, administración e integración · Emilio · ⭐⭐⭐⭐
Pocas pantallas, pero todos dependen de él. **La base del proyecto debe estar en `main` en los días 1–2.**

- [ ] **Proyecto base:** Laravel, Filament, Tailwind, conexión a MySQL, `.env.example` sin claves.
- [ ] **Migraciones** de todas las tablas de `docs/base-de-datos.md`, con modelos y relaciones Eloquent.
- [ ] **`SecuenciaTesoreriaService`:** genera `EEE-PPP-SSSSSSSSS` por punto de emisión, dentro de una transacción.
- [ ] **RF-01** Inicio de sesión con correo y contraseña.
- [ ] **RF-02** Cambio de contraseña (actual, nueva, confirmar) y recuperación por correo.
- [ ] **RF-03** Menú lateral (Contribuyentes, Recaudaciones, Administración). En Administración: CRUD de usuarios, puntos de emisión y perfiles (Administrador, Recaudador, Contribuyente) con `spatie/laravel-permission`.
- [ ] **Permisos por rol:** el recaudador cobra pero no administra; el contribuyente solo consulta lo suyo.
- [ ] **RF-28** Anulación general (`AnulacionService`): pide el motivo, cambia el estado a X y guarda quién y cuándo. Lo usan Leo, Santiago y Erick.
- [ ] **Integración:** revisar los Pull Requests y resolver conflictos.

## Módulo 2 — Contribuyentes, establecimientos y predios · Karpite · ⭐⭐
Mucho formulario y tabla (rápido con Filament). Lo más delicado es la transferencia de predios.

- [ ] **RF-04** Ficha del contribuyente.
  - Obligatorios: país de origen, tipo de contribuyente, tipo de identificación (Cédula/RUC/Pasaporte), teléfono, estatus.
  - Generales: ID automático, razón social o nombres, representante legal, dirección.
  - Contacto: zona (Urbana/Rural), correo, teléfono de domicilio, celular.
  - Temporales: fecha de nacimiento; la edad o los años de funcionamiento se calculan.
- [ ] **RF-05** Botones Agregar, Editar, Guardar, Imprimir ficha (PDF) y Regresar.
- [ ] **RF-06** Contadores de predios: activos, inactivos, anulados, transferidos y total.
- [ ] **RF-07** Agregar establecimientos desde la ficha (sección P. Funcionamiento, Relation Manager).
- [ ] **RF-08** Ficha de establecimiento: ID, contador de pagos, datos heredados del contribuyente, tipo, nombre, dirección, teléfono, observaciones, estatus.
- [ ] **RF-09** Grilla de establecimientos (ID, nombre, estado).
- [ ] **RF-10** Predios: ID, contador de pagos, código catastral* (único), matrícula, fecha de escritura, fecha inicial de contribución, zona (Urbana/Residencial/Rural/Industrial), ubicación, área m², avalúo, observaciones, estatus.
- [ ] **RF-11** Búsqueda por código catastral.
- [ ] **RF-12** Editar, guardar, anular y **transferir** un predio a otro contribuyente, guardando el historial en `predio_transferencia`.
- [ ] **Datos de prueba (seeders):** unos 30 contribuyentes, 50 predios y 15 establecimientos ficticios.
- [ ] Extra recomendado: validar la cédula y el RUC ecuatorianos con el dígito verificador.

Riesgo bajo. Si termina antes, apoya a Adrian o a Leo.

## Módulo 3 — Liquidación predial · Adrian · ⭐⭐⭐⭐⭐
El corazón del sistema: cálculos de dinero, varias tablas a la vez y reglas pendientes.

- [ ] **RF-13** Página "Recaudación predial" (Livewire) en el menú Recaudaciones.
- [ ] **RF-15** Buscador de contribuyentes por ID, identificación o nombre, con paginación.
- [ ] **RF-16** Modal con los predios del contribuyente (clave catastral, matrícula, ID).
- [ ] **RF-17** Autocompletar la cabecera: identificación, nombre, fecha de nacimiento, edad, correo; ID predial, clave catastral, área, ubicación.
- [ ] **RF-18** Número de comprobante y fecha de emisión (usa `SecuenciaTesoreriaService`).
- [ ] **RF-19** Botón "+ Periodo" para agregar años adeudados, sin permitir años ya pagados.
- [ ] **RF-20** Cálculo por año en `LiquidacionPredialService`: avalúo, subtotal, % y valor de descuento, total base, intereses, multa, gastos administrativos, total. Mínimo de $1.25 por año. **Con pruebas automáticas.**
- [ ] **RF-21** Fila de SUMAS en tiempo real y campo de observación (por defecto "SIN NOVEDAD").
- [ ] **RF-14** Contador de pagos realizados en predios y establecimientos (se calcula a partir de los comprobantes activos).
- [ ] **`EstadoCuentaPredialService::adeuda($contribuyente)`:** indica si debe y qué años. Lo usa Erick (RF-37).

Riesgo alto: intereses, multas y descuentos no están definidos. Programar primero lo confirmado (0.15 por mil y el mínimo de $1.25) y dejar lo demás en funciones aisladas.

## Módulo 4 — Formas de pago e historial · Leo · ⭐⭐⭐⭐
El cuadre en $0.00, guardar el cobro sin errores y los componentes que reutilizan los demás.

- [ ] **RF-22** Modal "Forma de Pago" **reutilizable** con 9 formas: efectivo, depósito, crédito, transferencia, cheque certificado, permuta, tarjeta de débito, tarjeta de crédito, nota de crédito. Pide número de documento y banco cuando aplica.
- [ ] **RF-23** Valor a cancelar, suma y diferencia en tiempo real; **Continuar bloqueado** si la diferencia no es $0.00.
- [ ] **RF-24** `RegistroCobroService`: guarda en una sola transacción el comprobante, el detalle y las formas de pago, y genera el comprobante.
- [ ] **Plantilla PDF común** de comprobantes (logo, datos de tesorería, numeración), usada por Adrian, Santiago y Erick.
- [ ] **RF-25** Historial de contribuciones con filtros: serie/fecha, serie/secuencia, nombre, identificación, clave catastral, rango de fechas, establecimiento/punto de emisión.
- [ ] **RF-26** Grilla: ID, establecimiento, emisión, secuencial, fecha, valor, contribuyente, identificación, usuario, estado A/X; reimprimir y anular (con `AnulacionService`).

Coordinar desde el día 1 con Adrian (le pasa el valor a cancelar) y con Santiago y Erick (usan el modal).

## Módulo 5 — Permisos de funcionamiento y caja · Santiago · ⭐⭐⭐
Los permisos son sencillos; los cortes de caja son reportes con sumas y agrupaciones.

- [ ] **RF-30** Emisión del permiso: secuencia automática, fecha, contribuyente, representante legal, dirección del local, concepto ("Tasa de Permiso de Funcionamiento"), años desde y hasta, valor en dólares. Cobra con el modal de Leo.
- [ ] **RF-31** Buscador que muestre solo contribuyentes con RUC o cédula (ID, identificación, razón social, teléfono).
- [ ] **RF-32** Desplegable con los establecimientos del contribuyente elegido.
- [ ] **RF-33** Historial de permisos con filtros (serie/fecha, serie/secuencia, nombre, identificación, punto de emisión, rango de fechas) y grilla A/X.
- [ ] **RF-39** Arqueo por denominación: billetes de $100, $50, $20, $10, $5, $2 y $1; monedas de $1.00, $0.50, $0.25, $0.10, $0.05 y $0.01. Subtotales y total automáticos, comparados contra lo cobrado en efectivo.
- [ ] **RF-40** Corte de caja diario por recaudador: cobros agrupados por rubro (predial, solvencia, permiso), serie, usuario, rango de secuenciales y total en efectivo. En PDF.
- [ ] **RF-41** Corte consolidado de tesorería: todos los recaudadores, menos las ventas a crédito, con el desglose de depósitos, transferencias, cheques y notas de crédito (número de documento y banco). En PDF.

Semana 1: permisos (RF-30 a 33) y arqueo (RF-39). Semana 2: cortes de caja (RF-40 y 41), cuando ya existan cobros guardados.

## Módulo 6 — Certificados de solvencia · Erick · ⭐⭐⭐
Formulario con lógica intermedia: alerta de deuda, leyenda dinámica y varios predios.

- [ ] **RF-34** Emisión con secuencia automática y tipo de trámite del catálogo: Escritura, Escritura-Hipoteca, Fines Consiguientes, Hipoteca, Nombramiento, Posesión Efectiva, Rectificación de Medidas.
- [ ] **RF-35** Solicitante (identificación y razón social), descripción del trámite (qué), beneficiario (a favor de) y valor fijo de $10.00. Cobra con el modal de Leo.
- [ ] **RF-36** Leyenda dinámica "La persona interesada podrá hacer uso de la presente Certificación únicamente para tramitación de: [TIPO DE TRÁMITE]" y varios códigos catastrales y matrículas por certificado.
- [ ] **RF-37** Al elegir un contribuyente, consultar `EstadoCuentaPredialService::adeuda()` y mostrar la alerta de deuda (por confirmar si solo advierte o bloquea).
- [ ] **RF-27 / RF-29 / RF-38** Historial de solvencias con filtros (serie, secuencia, contribuyente, identificación, fechas) y grilla A/X. Son tres requisitos, pero una sola pantalla.
- [ ] **PDF del certificado** con el formato oficial, usando la plantilla de Leo.

Además coordina las pruebas generales (QA) y el manual de usuario.

---

## Observaciones sobre el PDF original
- Hay dos secciones numeradas como "7".
- El RF-29 repite el texto del RF-26; se interpreta como historial de solvencias.
- El RF-27, el RF-29 y el RF-38 se superponen (historial de solvencias).
- No se define qué puede hacer el perfil Contribuyente.
- No se definen las fórmulas de intereses, multas, descuentos ni el valor del permiso de funcionamiento.
