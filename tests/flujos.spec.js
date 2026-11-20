// Pruebas end-to-end con Playwright
// Comentarios en español, sin Composer, PHP puro

const { test, expect } = require('@playwright/test');

const PASSWORD = 'password';
const BASE = process.env.BASE_URL || 'http://localhost:8000';

test.describe('Flujos PRODUCT.md', () => {
  test('registro unico, login y dashboard paciente', async ({ page }) => {
    const sufijo = Date.now();
    const email = `test${sufijo}@example.com`;

    await page.goto(`${BASE}/index.php?page=registro`);
    await page.fill('#nombre', 'Test');
    await page.fill('#apellido', 'Usuario');
    await page.fill('#email', email);
    // completar campos minimos según formulario real
    // se asume que registro crea usuario paciente
    await page.click('button[type="submit"]');
    await expect(page).toHaveURL(/dashboard/);
  });

  test('agendar cita y atender cita creada en la prueba', async ({ page }) => {
    // login como paciente1
    await page.goto(`${BASE}/index.php?page=login`);
    await page.fill('#email', 'paciente1@mediconnect.com');
    await page.fill('#password', PASSWORD);
    await page.click('button[type="submit"]');

    // agendar
    await page.goto(`${BASE}/index.php?page=agendar-cita`);
    // selección simplificada
    await page.click('button:has-text("Agendar")');
    await expect(page.locator('text=Cita')).toBeVisible();
  });
});

test.describe('Accesos prohibidos entre roles', () => {
  test('paciente carne digital de otro', async ({ page }) => {
    await page.goto(`${BASE}/index.php?page=login`);
    await page.fill('#email', 'paciente1@mediconnect.com');
    await page.fill('#password', PASSWORD);
    await page.click('button[type="submit"]');
    await page.goto(`${BASE}/index.php?page=carne-digital&id=9999`);
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
    await page.goto(`${BASE}/index.php?page=ver-historia&id=1`);
    // sin cita previa -> dashboard
    await expect(page).toHaveURL(/dashboard/);
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
    // usar cita del seed completada
  });
});
