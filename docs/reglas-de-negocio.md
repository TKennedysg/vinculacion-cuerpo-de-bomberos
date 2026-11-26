# Reglas de negocio

Estado: **CONFIRMADA** (está en el PDF) o **PENDIENTE** (hay que preguntarla al Cuerpo de Bomberos).
Cuando una regla pendiente se confirme, el responsable del módulo actualiza este archivo.

## Contribución predial (RF-20) · Adrian
| Regla | Estado |
|---|---|
| Contribución = avalúo × 0.15 / 1000 por año fiscal | CONFIRMADA (el PDF menciona "0.15 x 1000") |
| Valor mínimo de $1.25 por año. Si el cálculo da menos, la diferencia se carga a gastos administrativos | CONFIRMADA |
| Porcentaje de descuento: ¿de qué depende? (pronto pago, tercera edad, discapacidad…) | PENDIENTE |
| Intereses por mora: ¿qué tasa usan y desde cuándo corren? | PENDIENTE |
| Multa: ¿cómo se calcula? | PENDIENTE |
| ¿Se usa el avalúo actual o el avalúo de cada año? | PENDIENTE |

Ejemplo de la regla del mínimo: un avalúo de $5,000 da $5,000 × 0.00015 = $0.75. Como es menor a $1.25, se agregan $0.50 de gastos administrativos y el total del año queda en $1.25.

Pseudocódigo de referencia:
```
base = redondear(avaluo * 0.15 / 1000, 2)
descuento = redondear(base * porcentaje_descuento / 100, 2)   # PENDIENTE
total_base = base - descuento
intereses = calcularIntereses(...)   # PENDIENTE
multa = calcularMulta(...)           # PENDIENTE
gastos_adm = max(0, 1.25 - total_base)
total = total_base + intereses + multa + gastos_adm
```
Nota: se asume que el mínimo de $1.25 se compara contra el total base; también hay que confirmarlo.

## Formas de pago (RF-23) · Leo
- Diferencia = valor a cancelar − suma de las formas de pago. Solo se permite continuar si es exactamente $0.00. **CONFIRMADA**
- ¿Qué datos se piden en cada forma (banco, número de cheque, número de tarjeta)? **PENDIENTE**

## Certificado de solvencia · Erick
- Valor fijo de $10.00. **CONFIRMADA**
- Si el contribuyente debe la contribución predial, se muestra una alerta. **CONFIRMADA**
- La alerta **solo advierte** y exige que el recaudador confirme que informó la mora; no bloquea la
  emisión. La confirmación queda registrada en `comprobante.observacion`. **DECISIÓN DEL EQUIPO**
  (pendiente de ratificar con la institución; el PDF solo dice "informando la mora antes de proceder").

## Permiso de funcionamiento · Santiago
- ¿Cómo se calcula el valor de la tasa (por tipo de negocio, por área, valor fijo)? **PENDIENTE**

## Anulaciones · Emilio
- Anular cambia el estado a X y exige un motivo. **CONFIRMADA**
- ¿Quién puede anular (solo el administrador)? ¿Se puede anular un comprobante de días anteriores? **PENDIENTE**

## Caja (RF-39 a 41) · Santiago
- El arqueo cuenta billetes ($100, $50, $20, $10, $5, $2, $1) y monedas ($1.00, $0.50, $0.25, $0.10, $0.05, $0.01). **CONFIRMADA**
- El corte consolidado descuenta las ventas a crédito. **CONFIRMADA**
- ¿Qué pasa si el arqueo no cuadra con lo cobrado en efectivo (se registra un faltante o sobrante)? **PENDIENTE**
- ¿El corte de caja cierra el día (ya no se puede cobrar con esa fecha)? **PENDIENTE**
