// Configuración de Playwright para pruebas de MediConnect
import { defineConfig } from '@playwright/test';

export default defineConfig({
  testDir: './tests',
  use: {
    baseURL: 'http://localhost:8000',
    headless: true,
    // Usa Brave (basado en Chromium) para no descargar otro navegador
    launchOptions: {
      executablePath: '/usr/bin/brave-browser',
    },
  },
  webServer: {
    // Mismo PHP que usa la app con XAMPP
    command: '/opt/lampp/bin/php -S localhost:8000 -t public',
    port: 8000,
    reuseExistingServer: true,
    timeout: 120000,
  },
});
