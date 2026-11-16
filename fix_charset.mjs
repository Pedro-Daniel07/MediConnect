import mysql from 'mysql2/promise';

async function fix() {
    const conn = await mysql.createConnection({
        host: 'localhost',
        user: 'root',
        password: '',
        database: 'mediconnect',
        charset: 'utf8mb4'
    });

    console.log('Actualizando especialidades...');
    await conn.execute("UPDATE especialidades SET nombre = 'Oftalmología' WHERE id = 6");
    await conn.execute("UPDATE especialidades SET nombre = 'Neurología' WHERE id = 8");
    await conn.execute("UPDATE especialidades SET nombre = 'Ortopedia y Traumatología' WHERE id = 7");

    console.log('Actualizando biografías de médicos...');
    await conn.execute("UPDATE medicos SET biografia = 'Traumatólogo deportivo. Ha sido médico de selecciones juveniles y es experto en artroscopia de rodilla y hombro.' WHERE id = 7");
    await conn.execute("UPDATE medicos SET biografia = 'Oftalmóloga especializada en retina médica, glaucoma y cirugía refractiva láser.' WHERE id = 6");
    await conn.execute("UPDATE medicos SET biografia = 'Neuróloga clínica. Especializada en cefaleas, migrañas complejas, trastornos del sueño y epilepsia.' WHERE id = 8");
    await conn.execute("UPDATE medicos SET biografia = 'Cardióloga intervencionista, miembro de la Sociedad Interamericana de Cardiología. Especialista en ecocardiografía y prevención de riesgo cardiovascular.' WHERE id = 2");

    await conn.end();
    console.log('¡Textos UTF-8 corregidos exitosamente!');
}

fix().catch(err => {
    console.error(err);
    process.exit(1);
});
