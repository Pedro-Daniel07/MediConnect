import mysql from 'mysql2/promise';
import fs from 'fs';

async function run() {
    console.log('Conectando a MySQL con charset utf8mb4...');
    const conn = await mysql.createConnection({
        host: 'localhost',
        user: 'root',
        password: '',
        database: 'mediconnect',
        charset: 'utf8mb4',
        multipleStatements: true
    });

    console.log('Leyendo database/migracion_fase_c.sql...');
    const sql = fs.readFileSync('database/migracion_fase_c.sql', 'utf8');

    console.log('Ejecutando migración Fase C...');
    await conn.query(sql);
    console.log('¡Migración Fase C ejecutada con éxito rotundo!');

    const [tables] = await conn.query('SHOW TABLES;');
    console.log('Tablas actuales:', tables.map(t => Object.values(t)[0]));

    await conn.end();
}

run().catch(err => {
    console.error('Error al ejecutar migración:', err);
    process.exit(1);
});
