import { chromium } from 'playwright';

async function capture() {
    console.log('Iniciando navegador Chromium...');
    const browser = await chromium.launch();

    // 1. CAPTURAR LOGIN A 1440px y 390px
    console.log('Capturando pantalla de Login...');
    const page1440 = await browser.newPage({ viewport: { width: 1440, height: 900 } });
    await page1440.goto('http://localhost/ruthxd/public/index.php?page=login', { waitUntil: 'networkidle' });
    await page1440.screenshot({ path: '.impeccable/review/login-1440.png', fullPage: true });

    const page390 = await browser.newPage({ viewport: { width: 390, height: 844 } });
    await page390.goto('http://localhost/ruthxd/public/index.php?page=login', { waitUntil: 'networkidle' });
    await page390.screenshot({ path: '.impeccable/review/login-390.png', fullPage: true });

    // 2. INICIAR SESIÓN COMO ADMIN Y CAPTURAR DASHBOARD Y MÉDICOS
    console.log('Iniciando sesión como Admin...');
    const adminPage = await browser.newPage({ viewport: { width: 1440, height: 900 } });
    await adminPage.goto('http://localhost/ruthxd/public/index.php?page=login');
    await adminPage.fill('#email', 'admin@mediconnect.com');
    await adminPage.fill('#password', 'Admin123!');
    await adminPage.click('button[type="submit"]');
    await adminPage.waitForLoadState('networkidle');

    console.log('Capturando Dashboard de Administrador...');
    await adminPage.screenshot({ path: '.impeccable/review/admin-dashboard-1440.png', fullPage: true });

    console.log('Capturando Vista de Médicos (Admin)...');
    await adminPage.goto('http://localhost/ruthxd/public/index.php?page=medicos', { waitUntil: 'networkidle' });
    await adminPage.screenshot({ path: '.impeccable/review/admin-medicos-1440.png', fullPage: true });

    const adminPage390 = await browser.newPage({ viewport: { width: 390, height: 844 } });
    await adminPage390.goto('http://localhost/ruthxd/public/index.php?page=login');
    await adminPage390.fill('#email', 'admin@mediconnect.com');
    await adminPage390.fill('#password', 'Admin123!');
    await adminPage390.click('button[type="submit"]');
    await adminPage390.waitForLoadState('networkidle');
    await adminPage390.screenshot({ path: '.impeccable/review/admin-dashboard-390.png', fullPage: true });

    await browser.close();
    console.log('¡Todas las capturas guardadas con éxito en .impeccable/review/!');
}

capture().catch(err => {
    console.error('Error al capturar:', err);
    process.exit(1);
});
