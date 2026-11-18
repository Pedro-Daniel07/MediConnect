import mysql from 'mysql2/promise';

const host = process.env.DB_HOST || 'localhost';
const user = process.env.DB_USER || 'root';
const pass = process.env.DB_PASS || '';
const db = process.env.DB_NAME || 'mediconnect';

async function fix() {
    const conn = await mysql.createConnection({host, user, password: pass, database: db, charset: 'utf8mb4'});
    await conn.execute("UPDATE especialidades SET nombre = 'Oftalmología' WHERE id = 6");
    await conn.end();
}
fix();
