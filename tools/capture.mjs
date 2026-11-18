import { chromium } from 'playwright';
async function capture() {
    console.log('Iniciando navegador Chromium...');
    const browser = await chromium.launch();
    await browser.close();
}
capture();
