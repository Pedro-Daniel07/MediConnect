# Pruebas End-to-End con Playwright

Suite de pruebas automatizadas para los flujos críticos de MediConnect (`tests/flujos.spec.js`).

---

## 1. Reiniciar la Base de Datos Limpia

Antes de ejecutar las pruebas (o tras modificaciones en los datos), reinicia la base de datos ejecutando los siguientes 4 comandos en este orden estricto:

```bash
/opt/lampp/bin/mysql -u root -e "DROP DATABASE IF EXISTS mediconnect;"
/opt/lampp/bin/mysql -u root < database/database.sql
/opt/lampp/bin/mysql -u root mediconnect < database/migracion_fase_b.sql
/opt/lampp/bin/mysql -u root mediconnect < database/migracion_fase_c.sql
```

---

## 2. Cómo Correr las Pruebas

1. Asegúrate de tener el servicio MySQL de XAMPP activo:
   ```bash
   sudo /opt/lampp/lampp startmysql
   ```
2. Ejecuta la suite de Playwright:
   ```bash
   npx playwright test --reporter=list
   ```

> **Nota:** No es necesario iniciar manualmente el servidor web PHP. Playwright lo arranca y gestiona de forma automática mediante la directiva `webServer` en `playwright.config.js`.

---

## 3. Aviso de Rutas Específicas del Entorno

El archivo `playwright.config.js` está configurado con rutas absolutas de esta máquina:
* **Navegador:** `/usr/bin/brave-browser`
* **PHP:** `/opt/lampp/bin/php` (XAMPP)

Si ejecutas las pruebas en otro equipo o entorno, ajusta esas rutas o elimina `executablePath` para utilizar el motor Chromium nativo de Playwright:
```bash
npx playwright install chromium
```

---

## 4. Credenciales de Prueba

En los datos semilla de la base de datos, la contraseña para todos los usuarios de prueba (`paciente1@mediconnect.com`, `dr.rodriguez@mediconnect.com`, `admin@mediconnect.com`, etc.) es:
* **Contraseña:** `password` (no `Admin123!`).

---

## 5. Pruebas de la Suite (`tests/flujos.spec.js`)

1. **`registro unico, login y dashboard paciente`**: Registra un paciente con documento y correo únicos, inicia sesión con éxito y valida acceso al dashboard.
2. **`agendar cita`**: Selecciona médico y franja horaria, reserva la cita con motivo único y verifica su redirección y presencia en mis-citas.
3. **`paciente ve solo su propio carné`**: Verifica que un paciente con `id=3` en la URL ignora el parámetro ajeno y visualiza exclusivamente sus datos propios.
4. **`medico carne digital de paciente no atendido redirige`**: Comprueba que un médico intentando consultar el carné de un paciente sin citas previas es redirigido al dashboard.
5. **`paciente entra a rutas admin`**: Comprueba que un paciente intentando acceder a la gestión de usuarios administradores es redirigido al dashboard.
6. **`medico paciente no atendido`**: Valida que un médico que intenta acceder a la historia clínica de un paciente sin cita previa es redirigido a la lista de pacientes.
7. **`logout GET da 405`**: Comprueba que una petición GET a la ruta de logout responde con código HTTP 405 (Method Not Allowed) y mantiene la sesión.
8. **`cancelar cita completada no cambia`**: Valida que intentar cancelar mediante POST una cita en estado completada es rechazado con error y mantiene su estado en el historial.
