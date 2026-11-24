# Guía de instalación del entorno (Windows)

Pasos que siguió Santiago para dejar listo su entorno. Todo se hace desde **PowerShell como
administrador**, salvo donde se indica. Cuando un comando no se reconozca justo después de instalar
algo, ver la sección "Recargar el PATH".

## 0. Verificar qué tienes instalado
Estos comandos **no instalan nada**; solo muestran la versión si ya existe. Si dan error, falta instalarlo.
```powershell
php -v
composer -V
node -v
npm -v
git --version
mysql --version
```
Se necesita: PHP 8.3+, Composer 2, Node 20+, npm, Git y MySQL 8.

## 1. Node.js y Git (si faltan)
```powershell
winget install OpenJS.NodeJS.LTS
winget install Git.Git
```

## 2. PHP 8.3
```powershell
winget install --id PHP.PHP.8.3 -e
```
winget avisa: *"Variable de entorno PATH modificada; reinicie el shell"*. Hay que recargar el PATH (ver abajo).

## 3. MySQL 8
```powershell
winget install --id Oracle.MySQL -e
```
> Nota: si ya tenías MySQL instalado, winget solo lo actualiza. Revisa qué servicio existe con
> `Get-Service *mysql*` (en el caso de Santiago era `MySQL80`, servidor 8.0).

## 4. Composer
No se encontró un paquete oficial de Composer en winget, así que se descarga y ejecuta su instalador oficial:
```powershell
Invoke-WebRequest -Uri https://getcomposer.org/Composer-Setup.exe -OutFile "$env:TEMP\Composer-Setup.exe"
Start-Process "$env:TEMP\Composer-Setup.exe"
```
En el asistente:
1. **Install for all users (recommended)**.
2. **Developer mode**: desmarcado → Next.
3. **Settings Check**: debe aparecer la ruta del PHP instalado por winget
   (`...\WinGet\Packages\PHP.PHP.8.3_...\php.exe`) → Next.
4. **Proxy Settings**: vacío → Next → Install → Finish.

## 5. Recargar el PATH
Una ventana de PowerShell abierta antes de instalar no ve los programas nuevos. Cierra la ventana y abre
otra, o recarga el PATH en la misma:
```powershell
$env:Path = [Environment]::GetEnvironmentVariable("Path","Machine") + ";" + [Environment]::GetEnvironmentVariable("Path","User")
php -v
composer -V
```

## 6. Agregar MySQL al PATH
El instalador de MySQL no lo agrega al PATH. Como administrador:
```powershell
$mysqlBin = (Get-ChildItem "C:\Program Files\MySQL" -Directory -Filter "MySQL Server*" | Select-Object -Last 1).FullName + "\bin"
$mysqlBin
[Environment]::SetEnvironmentVariable("Path", [Environment]::GetEnvironmentVariable("Path","Machine") + ";$mysqlBin", "Machine")
$env:Path = [Environment]::GetEnvironmentVariable("Path","Machine") + ";" + [Environment]::GetEnvironmentVariable("Path","User")
mysql --version
```

## 7. Activar las extensiones de PHP que necesita Laravel
El PHP de winget trae varias extensiones desactivadas. Se activan quitando el `;` en el `php.ini`:
```powershell
$ini = php -r "echo php_ini_loaded_file();"
$ini
(Get-Content $ini) -replace '^;\s*extension=(pdo_mysql|fileinfo|zip|intl)\s*$', 'extension=$1' | Set-Content $ini
php -m | Select-String "pdo_mysql|mbstring|openssl|fileinfo|curl|zip|intl"
```
Deben aparecer las 7: `curl`, `fileinfo`, `intl`, `mbstring`, `openssl`, `pdo_mysql` y `zip`.

## 8. Iniciar el servidor de MySQL
```powershell
Get-Service *mysql*
Start-Service MySQL80
Get-Service MySQL80
Set-Service MySQL80 -StartupType Automatic   # opcional: que arranque solo al prender la PC
```

## 9. Restablecer la contraseña de root (si no la recuerdas)
1. Ver qué servidor y qué configuración usa el servicio:
   ```powershell
   (Get-CimInstance Win32_Service -Filter "Name='MySQL80'").PathName
   ```
   Ejemplo de salida:
   `"C:\Program Files\MySQL\MySQL Server 8.0\bin\mysqld.exe" --defaults-file="C:\ProgramData\MySQL\MySQL Server 8.0\my.ini" MySQL80`
2. Detener el servicio y arrancar MySQL con un archivo que cambia la contraseña
   (reemplaza `TU_CONTRASEÑA` y las rutas por las del paso anterior):
   ```powershell
   Stop-Service MySQL80
   Set-Content C:\mysql-init.txt "ALTER USER 'root'@'localhost' IDENTIFIED BY 'TU_CONTRASEÑA';"
   & "C:\Program Files\MySQL\MySQL Server 8.0\bin\mysqld.exe" --defaults-file="C:\ProgramData\MySQL\MySQL Server 8.0\my.ini" --init-file=C:\mysql-init.txt --console
   ```
   Esperar la línea **"ready for connections"** y no cerrar la ventana.
3. En **otra** ventana de PowerShell, apagarlo con la contraseña nueva:
   ```powershell
   mysqladmin -u root -p shutdown
   ```
   La primera ventana mostrará *"Shutdown complete"*. Es normal.
4. Borrar el archivo (contiene la contraseña en texto plano) y volver a iniciar el servicio:
   ```powershell
   Remove-Item C:\mysql-init.txt
   Start-Service MySQL80
   ```

## 10. Crear la base de datos del proyecto
```powershell
mysql -u root -p
```
```sql
CREATE DATABASE gesadmen CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
SHOW DATABASES;
exit
```

## Resultado esperado
| Herramienta | Versión usada por Santiago |
|---|---|
| PHP | 8.3.37 (con las 7 extensiones) |
| Composer | 2.11.2 |
| MySQL | 8.0.43 (servicio `MySQL80`) |
| Node / npm | 22.21.1 / 10.9.4 |
| Git | 2.51.1 |

## Siguiente paso: levantar el proyecto
Ver "Cómo empezar" en el `README.md`. En el `.env`, usa `DB_DATABASE=gesadmen` y tu contraseña de root
entre comillas, por ejemplo `DB_PASSWORD="TuClave!"`. **Nunca subas el `.env` a GitHub.**
