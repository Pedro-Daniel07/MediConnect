// Pruebas end-to-end con Playwright
// Comentarios en español, sin Composer, PHP puro

const { test, expect } = require('@playwright/test');

const PASSWORD = 'password';
const BASE = process.env.BASE_URL || 'http://localhost:8000';

test.describe('Flujos PRODUCT.md', () => {
  test('registro unico, login y dashboard paciente', async ({ page }) => {
    const sufijo = Date.now();
    const email = `test${sufijo}@example.com`;
    const clave = 'clave1234';

    await page.goto(`${BASE}/index.php?page=registro`);
    await page.fill('#nombre', 'Test');
    await page.fill('#apellido', 'Usuario');
    await page.fill('#documento', String(sufijo));
    await page.fill('#email', email);
    await page.fill('#password', clave);
    await page.fill('#password_confirm', clave);
    await page.click('button[type="submit"]');

    // procesarRegistro no redirige: incluye la vista de login con el mensaje de éxito
    await expect(page.getByText('Registro exitoso. Ahora puedes iniciar sesión.')).toBeVisible();

    await page.fill('#email', email);
    await page.fill('#password', clave);
    await page.click('button[type="submit"]');
    await expect(page).toHaveURL(/dashboard/);
  });

  test('agendar cita', async ({ page }) => {
    // login como paciente1
    await page.goto(`${BASE}/index.php?page=login`);
    await page.fill('#email', 'paciente1@mediconnect.com');
    await page.fill('#password', PASSWORD);
    await page.click('button[type="submit"]');
    await expect(page).toHaveURL(/dashboard/);

    // agendar
    await page.goto(`${BASE}/index.php?page=agendar-cita`);

    // seleccionar médico existente (Dra. Rodríguez, id 1)
    await page.selectOption('#medico_id', '1');

    // fecha futura válida (próximo lunes, al menos 7 días después de hoy)
    const fechaObj = new Date();
    fechaObj.setDate(fechaObj.getDate() + 7 + ((1 - fechaObj.getDay() + 7) % 7));
    const fecha = `${fechaObj.getFullYear()}-${String(fechaObj.getMonth() + 1).padStart(2, '0')}-${String(fechaObj.getDate()).padStart(2, '0')}`;
    await page.fill('#fecha', fecha);

    // esperar a que carguen horarios y seleccionar el primero disponible
    await page.waitForSelector('#horarios-disponibles input[type="radio"]', { state: 'attached' });
    await page.locator('#horarios-disponibles label.horario-item').first().click();

    // motivo de consulta único por ejecución
    const motivoConsulta = `Consulta test ${Date.now()}`;
    await page.fill('#motivo_consulta', motivoConsulta);

    // enviar formulario (el submit del header, cerrar sesión, es el primero del DOM)
    await page.locator('form').filter({ has: page.locator('#motivo_consulta') })
        .getByRole('button', { name: 'Agendar Cita' }).click();

    // verificar redirección a mis-citas y que la cita aparece
    await expect(page).toHaveURL(/mis-citas/);
    await expect(page.getByText(motivoConsulta, { exact: true })).toBeVisible();
  });
});

test.describe('Accesos prohibidos entre roles', () => {
  test('paciente ve solo su propio carné', async ({ page }) => {
    await page.goto(`${BASE}/index.php?page=login`);
    await page.fill('#email', 'paciente1@mediconnect.com');
    await page.fill('#password', PASSWORD);
    await page.click('button[type="submit"]');
    await page.goto(`${BASE}/index.php?page=carne-digital&id=3`);
    await expect(page).toHaveURL(/carne-digital/);
    await expect(page.getByText('Laura Gómez')).toBeVisible();
    await expect(page.getByText('MC-000001')).toBeVisible();
    await expect(page.getByText('Sofía Ramírez')).not.toBeVisible();
    await expect(page.getByText('MC-000003')).not.toBeVisible();
  });

  test('medico carne digital de paciente no atendido redirige', async ({ page }) => {
    await page.goto(`${BASE}/index.php?page=login`);
    await page.fill('#email', 'dr.rodriguez@mediconnect.com');
    await page.fill('#password', PASSWORD);
    await page.click('button[type="submit"]');
    await page.goto(`${BASE}/index.php?page=carne-digital&id=3`);
    await expect(page).toHaveURL(/dashboard/);
  });

  test('paciente entra a rutas admin', async ({ page }) => {
    await page.goto(`${BASE}/index.php?page=login`);
    await page.fill('#email', 'paciente1@mediconnect.com');
    await page.fill('#password', PASSWORD);
    await page.click('button[type="submit"]');
    await page.goto(`${BASE}/index.php?page=usuarios`);
    await expect(page).toHaveURL(/dashboard/);
  });

  test('medico paciente no atendido', async ({ page }) => {
    await page.goto(`${BASE}/index.php?page=login`);
    await page.fill('#email', 'dr.rodriguez@mediconnect.com');
    await page.fill('#password', PASSWORD);
    await page.click('button[type="submit"]');
    await page.goto(`${BASE}/index.php?page=ver-historia&id=3`);
    // sin cita previa -> pacientes
    await expect(page).toHaveURL(/pacientes/);
  });
});

test.describe('Seguridad', () => {
  test('logout GET da 405', async ({ page }) => {
    await page.goto(`${BASE}/index.php?page=login`);
    await page.fill('#email', 'admin@mediconnect.com');
    await page.fill('#password', PASSWORD);
    await page.click('button[type="submit"]');
    const response = await page.goto(`${BASE}/index.php?page=logout`);
    expect(response.status()).toBe(405);
    await expect(page).not.toHaveURL(/login/);
  });

  test('cancelar cita completada no cambia', async ({ page }) => {
    await page.goto(`${BASE}/index.php?page=login`);
    await page.fill('#email', 'paciente1@mediconnect.com');
    await page.fill('#password', PASSWORD);
    await page.click('button[type="submit"]');
    await expect(page).toHaveURL(/dashboard/);

    await page.goto(`${BASE}/index.php?page=mis-citas`);
    const csrfToken = await page.locator('head meta[name="csrf-token"]').getAttribute('content');
    expect(csrfToken).toBeTruthy();

    const response = await page.request.post(`${BASE}/index.php?page=mis-citas`, {
      form: {
        action: 'cancelar',
        cita_id: '1',
        motivo: 'Intento de cancelar cita completada',
        csrf_token: csrfToken,
      },
    });

    expect(response.ok()).toBeTruthy();
    const responseHtml = await response.text();
    expect(responseHtml).toContain('No se pudo cancelar la cita. Inténtalo de nuevo.');

    await page.reload();
    const cita1 = page.locator('.list-item').filter({ hasText: 'Control general de salud' });
    await expect(cita1).toBeVisible();
    await expect(cita1.locator('.badge-completada')).toHaveText('Completada');
  });
});
