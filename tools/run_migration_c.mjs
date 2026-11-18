import mysql from 'mysql2/promise';
import fs from 'fs';

const host = process.env.DB_HOST || 'localhost';
const user = process.env.DB_USER || 'root';
const pass = process.env.DB_PASS || '';
const db = process.env.DB_NAME || 'mediconnect';

async function run() {
    const conn = await mysql.createConnection({host, user, password: pass, database: db, charset: 'utf8mb4', multipleStatements: true});
    const sql = fs.readFileSync('database/migracion_fase_c.sql', 'utf8');
    await conn.query(sql);
    await conn.end();
}
run().catch(e => { console.error(e); process.exit(1); });
