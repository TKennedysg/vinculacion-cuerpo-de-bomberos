# Flujo de trabajo con Git y GitHub

Trabajamos con **una rama por módulo**. Nadie hace commit directo en `main` (salvo Emilio
con el proyecto base). Todo entra a `main` mediante **Pull Request** revisado por Emilio.

## Por qué ramas y no todos en `main`
| Todos en `main` | Una rama por módulo |
|---|---|
| Si alguien sube código roto, se le rompe el proyecto a los 6 | Si alguien rompe algo, solo afecta a su rama |
| Conflictos en casi cada `git pull` | Los conflictos se resuelven una vez, al integrar |
| No se sabe qué cambió cada uno | Cada Pull Request muestra lo que hizo cada integrante (sirve como evidencia para el informe) |

## Ramas
| Integrante | Rama |
|---|---|
| Emilio | `modulo-1-seguridad` |
| Karpite | `modulo-2-contribuyentes` |
| Adrian | `modulo-3-liquidacion` |
| Leo | `modulo-4-pagos` |
| Santiago | `modulo-5-permisos-caja` |
| Erick | `modulo-6-solvencias` |

## Comandos

Una sola vez, crear tu rama:
```bash
git checkout main
git pull origin main
git checkout -b modulo-5-permisos-caja   # usa el nombre de TU rama
```

Día a día, trabajar y guardar:
```bash
git add .
git commit -m "RF-30: formulario de emisión de permiso"
git push origin modulo-5-permisos-caja
```

Antes de empezar cada día, traer lo nuevo de `main` a tu rama:
```bash
git checkout main
git pull origin main
git checkout modulo-5-permisos-caja
git merge main
```

Para integrar: en GitHub, abrir un **Pull Request** de tu rama hacia `main`, con los RF que
incluye en el título (ej. `RF-30 a RF-32: emisión de permisos`). Emilio lo revisa y lo une.

## Reglas
1. **Nadie hace commit directo en `main`.** Proteger la rama en GitHub: Settings → Branches →
   regla de protección para `main` que exija Pull Request.
2. **Pull Requests pequeños y frecuentes**: uno por requisito o cada 1 a 2 días. No esperar al fin de semana.
3. **Hacer merge de `main` en tu rama a diario** para no quedarte atrás.
4. **Mensajes de commit con el RF**: `RF-20: cálculo desglosado por año fiscal`.
5. **Archivos compartidos solo los toca su dueño**: migraciones y roles (Emilio), seeders (Karpite),
   modal de pagos y plantilla PDF (Leo). Si necesitas un cambio, pídeselo.
6. **Nunca subir el `.env`** (claves y contraseñas). Solo `.env.example`.

## Si no te sientes cómodo con la terminal
Puedes hacer los mismos pasos con **GitHub Desktop** o con la pestaña de Git de **VS Code**
(cambiar de rama, commit, push, pull y abrir el Pull Request con botones).
