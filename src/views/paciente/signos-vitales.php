<?php require_once __DIR__ . '/../layout/header.php'; ?>

<div class="page-header" style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: var(--space-4);">
    <div>
        <div style="display: flex; align-items: center; gap: var(--space-2); margin-bottom: var(--space-2);">
            <span class="badge badge-info">Biometría y Salud Predictiva</span>
            <span class="badge badge-completed">Score Algorítmico 2030</span>
        </div>
        <h1 class="page-title">Signos Vitales y Puntaje de Salud</h1>
        <p class="page-subtitle">Monitoreo hemodinámico continuo, índice de masa corporal y score de bienestar integral</p>
    </div>
    <div style="display: flex; gap: var(--space-3);">
        <button type="button" class="btn btn-primary" onclick="document.getElementById('form-registro-signos').scrollIntoView({ behavior: 'smooth' })">
            <svg class="icon icon-sm" aria-hidden="true"><use href="<?php echo asset('img/icons.svg#activity'); ?>"></use></svg>
            <span>Nuevo Registro</span>
        </button>
    </div>
</div>

<?php if (isset($mensaje_exito)): ?>
    <div class="alert alert-success">
        <svg class="icon" aria-hidden="true"><use href="<?php echo asset('img/icons.svg#check'); ?>"></use></svg>
        <span><?php echo e($mensaje_exito); ?></span>
    </div>
<?php endif; ?>

<?php if (isset($error)): ?>
    <div class="alert alert-error">
        <svg class="icon" aria-hidden="true"><use href="<?php echo asset('img/icons.svg#alert-triangle'); ?>"></use></svg>
        <span><?php echo e($error); ?></span>
    </div>
<?php endif; ?>

<!-- Panel Superior: Score de Salud y Métricas en Tiempo Real -->
<div class="grid grid-2" style="margin-bottom: var(--space-8);">
    <!-- Arco / Velocímetro SVG del Puntaje de Salud -->
    <div class="glass-card" style="display: flex; flex-direction: column; justify-content: space-between;">
        <div class="card-header">
            <h3 class="card-title">Puntaje de Salud Integral</h3>
            <p class="card-subtitle">Algoritmo clínico ponderado sobre 100 puntos</p>
        </div>

        <div class="score-gauge-box">
            <!-- Arco SVG Gradiente Animado -->
            <svg width="240" height="150" viewBox="0 0 240 150">
                <defs>
                    <linearGradient id="scoreGrad" x1="0%" y1="0%" x2="100%" y2="0%">
                        <stop offset="0%" stop-color="#ef4444" />
                        <stop offset="50%" stop-color="#f59e0b" />
                        <stop offset="100%" stop-color="#10b981" />
                    </linearGradient>
                </defs>
                <!-- Pista de fondo gris -->
                <path d="M 30 130 A 90 90 0 0 1 210 130" fill="none" stroke="rgba(148, 163, 184, 0.15)" stroke-width="16" stroke-linecap="round"/>
                <!-- Arco coloreado dinámico -->
                <?php
                    // Mapeo de score (0 a 100) a stroke-dashoffset (longitud de arco ~283px)
                    $dashTotal = 283;
                    $dashOffset = $dashTotal - (($score_actual / 100) * $dashTotal);
                ?>
                <path id="score-arc-path" d="M 30 130 A 90 90 0 0 1 210 130" fill="none" stroke="url(#scoreGrad)" stroke-width="16" stroke-linecap="round"
                      stroke-dasharray="283" stroke-dashoffset="<?php echo $dashOffset; ?>" style="transition: stroke-dashoffset 1s ease;"/>
            </svg>

            <!-- Valor numérico central -->
            <div style="position: absolute; bottom: 20px; text-align: center;">
                <div style="font-size: 48px; font-weight: var(--weight-bold); font-family: var(--font-mono); color: var(--text-primary); line-height: 1;">
                    <span id="score-display-value"><?php echo $score_actual; ?></span><span style="font-size: 20px; color: var(--text-tertiary);">/100</span>
                </div>
                <div style="margin-top: 4px;">
                    <?php if ($score_actual >= 85): ?>
                        <span class="badge badge-completed">Estado Óptimo</span>
                    <?php elseif ($score_actual >= 70): ?>
                        <span class="badge badge-pending">Nivel Aceptable</span>
                    <?php else: ?>
                        <span class="badge badge-cancelled">Alerta Clínica</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Desglose de Puntos Clínicos -->
        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: var(--space-2); margin-top: var(--space-4); padding-top: var(--space-4); border-top: 1px solid var(--glass-border); text-align: center; font-size: var(--text-xs);">
            <div style="background: var(--glass-bg-secondary); padding: var(--space-2); border-radius: var(--radius-md);">
                <span style="color: var(--text-tertiary); display: block;">Tensión Art.</span>
                <strong style="color: var(--text-primary);"><?php echo $ultimo_signo ? $ultimo_signo['presion_sistolica'].'/'.$ultimo_signo['presion_diastolica'] : '120/80'; ?></strong>
            </div>
            <div style="background: var(--glass-bg-secondary); padding: var(--space-2); border-radius: var(--radius-md);">
                <span style="color: var(--text-tertiary); display: block;">Oxígeno SpO2</span>
                <strong style="color: var(--accent-primary);"><?php echo $ultimo_signo ? $ultimo_signo['saturacion_oxigeno'].'%' : '98%'; ?></strong>
            </div>
            <div style="background: var(--glass-bg-secondary); padding: var(--space-2); border-radius: var(--radius-md);">
                <span style="color: var(--text-tertiary); display: block;">Glucemia</span>
                <strong style="color: var(--success);"><?php echo $ultimo_signo ? $ultimo_signo['glucosa'].' mg/dL' : '95 mg/dL'; ?></strong>
            </div>
        </div>
    </div>

    <!-- Indicador y Semáforo de IMC en Vivo -->
    <div class="glass-card" style="display: flex; flex-direction: column; justify-content: space-between;">
        <div class="card-header">
            <h3 class="card-title">Índice de Masa Corporal (IMC)</h3>
            <p class="card-subtitle">Relación masa / estatura y distribución antropométrica</p>
        </div>

        <div>
            <?php
                $peso_act = $ultimo_signo['peso'] ?? 72.0;
                $alt_act = $ultimo_signo['altura'] ?? 175.0;
                $imc_act = $ultimo_signo['imc'] ?? 23.5;
                $clasif_act = $ultimo_signo['clasificacion_imc'] ?? 'Normal';
            ?>
            <div style="display: flex; justify-content: space-between; align-items: baseline; margin-bottom: var(--space-4);">
                <div>
                    <span style="font-size: var(--text-sm); color: var(--text-tertiary);">IMC Actual:</span>
                    <div style="font-size: var(--text-4xl); font-weight: var(--weight-bold); font-family: var(--font-mono); color: var(--text-primary);">
                        <?php echo $imc_act; ?> <span style="font-size: var(--text-sm); font-weight: normal; color: var(--text-tertiary);">kg/m²</span>
                    </div>
                </div>
                <div>
                    <?php if ($clasif_act === 'Normal'): ?>
                        <span class="badge badge-completed" style="font-size: var(--text-sm); padding: var(--space-2) var(--space-4);">Normal</span>
                    <?php elseif ($clasif_act === 'Sobrepeso'): ?>
                        <span class="badge badge-pending" style="font-size: var(--text-sm); padding: var(--space-2) var(--space-4);">Sobrepeso</span>
                    <?php elseif ($clasif_act === 'Obesidad'): ?>
                        <span class="badge badge-cancelled" style="font-size: var(--text-sm); padding: var(--space-2) var(--space-4);">Obesidad</span>
                    <?php else: ?>
                        <span class="badge badge-info" style="font-size: var(--text-sm); padding: var(--space-2) var(--space-4);">Bajo Peso</span>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Semáforo Visual del IMC -->
            <div style="margin-bottom: var(--space-4);">
                <div style="height: 12px; width: 100%; border-radius: var(--radius-full); background: linear-gradient(90deg, #0ea5e9 0%, #10b981 35%, #f59e0b 70%, #ef4444 100%); position: relative; overflow: visible;">
                    <!-- Aguja / Indicador posicionado -->
                    <?php
                        // Escala: 15 a 35 IMC mapeado de 0% a 100%
                        $porcImc = min(100, max(0, (($imc_act - 15) / 20) * 100));
                    ?>
                    <div style="position: absolute; left: <?php echo $porcImc; ?>%; top: -4px; width: 6px; height: 20px; background: white; border: 2px solid #0f172a; border-radius: 3px; transform: translateX(-50%); box-shadow: 0 0 6px rgba(0,0,0,0.5);"></div>
                </div>
                <div style="display: flex; justify-content: space-between; font-size: 10px; color: var(--text-tertiary); margin-top: 6px;">
                    <span>Bajo (&lt;18.5)</span>
                    <span>Normal (18.5-24.9)</span>
                    <span>Sobrepeso (25-29.9)</span>
                    <span>Obesidad (&ge;30)</span>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: var(--space-4); background: var(--glass-bg-secondary); padding: var(--space-4); border-radius: var(--radius-md); border: 1px solid var(--glass-border);">
                <div>
                    <span style="font-size: var(--text-xs); color: var(--text-tertiary); display: block;">Peso Registrado</span>
                    <strong style="font-size: var(--text-lg); color: var(--text-primary); font-family: var(--font-mono);"><?php echo $peso_act; ?> kg</strong>
                </div>
                <div>
                    <span style="font-size: var(--text-xs); color: var(--text-tertiary); display: block;">Estatura</span>
                    <strong style="font-size: var(--text-lg); color: var(--text-primary); font-family: var(--font-mono);"><?php echo $alt_act; ?> cm</strong>
                </div>
            </div>
        </div>

        <p style="font-size: var(--text-xs); color: var(--text-tertiary); margin-top: var(--space-4);">
            * El IMC es una referencia antropométrica estándar aprobada por la Organización Mundial de la Salud.
        </p>
    </div>
</div>

<!-- Formulario de Registro Interactivo de Signos Vitales -->
<div id="form-registro-signos" class="glass-card" style="margin-bottom: var(--space-8);">
    <div class="card-header">
        <h3 class="card-title">Captura Biométrica de Signos Vitales</h3>
        <p class="card-subtitle">Calcula automáticamente el IMC y el Puntaje de Salud antes de registrar</p>
    </div>

    <form method="POST" action="index.php?page=signos-vitales" style="display: flex; flex-direction: column; gap: var(--space-5);">
        <input type="hidden" name="action" value="guardar_signos">
        <input type="hidden" name="csrf_token" value="<?php echo obtener_csrf_token(); ?>">

        <div class="grid grid-4">
            <div class="form-group" style="margin-bottom: 0;">
                <label for="peso" class="form-label required">Peso (kg)</label>
                <input type="number" step="0.1" id="peso" name="peso" class="form-input" required value="72.0" oninput="recalcularEnVivo()">
            </div>
            <div class="form-group" style="margin-bottom: 0;">
                <label for="altura" class="form-label required">Estatura (cm)</label>
                <input type="number" step="0.5" id="altura" name="altura" class="form-input" required value="175.0" oninput="recalcularEnVivo()">
            </div>
            <div class="form-group" style="margin-bottom: 0;">
                <label for="presion_sistolica" class="form-label required">Sistólica (mmHg)</label>
                <input type="number" id="presion_sistolica" name="presion_sistolica" class="form-input" required value="118" oninput="recalcularEnVivo()">
            </div>
            <div class="form-group" style="margin-bottom: 0;">
                <label for="presion_diastolica" class="form-label required">Diastólica (mmHg)</label>
                <input type="number" id="presion_diastolica" name="presion_diastolica" class="form-input" required value="76" oninput="recalcularEnVivo()">
            </div>
        </div>

        <div class="grid grid-4">
            <div class="form-group" style="margin-bottom: 0;">
                <label for="frecuencia_cardiaca" class="form-label required">Frec. Cardiaca (ppm)</label>
                <input type="number" id="frecuencia_cardiaca" name="frecuencia_cardiaca" class="form-input" required value="72" oninput="recalcularEnVivo()">
            </div>
            <div class="form-group" style="margin-bottom: 0;">
                <label for="saturacion_oxigeno" class="form-label required">SpO2 (%)</label>
                <input type="number" id="saturacion_oxigeno" name="saturacion_oxigeno" class="form-input" required value="98" oninput="recalcularEnVivo()">
            </div>
            <div class="form-group" style="margin-bottom: 0;">
                <label for="glucosa" class="form-label required">Glucosa (mg/dL)</label>
                <input type="number" id="glucosa" name="glucosa" class="form-input" required value="92" oninput="recalcularEnVivo()">
            </div>
            <div class="form-group" style="margin-bottom: 0;">
                <label for="temperatura" class="form-label required">Temperatura (°C)</label>
                <input type="number" step="0.1" id="temperatura" name="temperatura" class="form-input" required value="36.5">
            </div>
        </div>

        <div class="form-group" style="margin-bottom: 0;">
            <label for="notas" class="form-label">Notas Clínicas u Observaciones</label>
            <input type="text" id="notas" name="notas" class="form-input" placeholder="Ej. Medición tomada en reposo matutino tras 8 horas de ayuno...">
        </div>

        <!-- Previsualización en Vivo de IMC y Score -->
        <div style="background: var(--glass-bg-secondary); border-radius: var(--radius-md); padding: var(--space-4); border: 1px solid var(--glass-border); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: var(--space-4);">
            <div style="display: flex; align-items: center; gap: var(--space-4);">
                <div>
                    <span style="font-size: var(--text-xs); color: var(--text-tertiary);">IMC Estimado:</span>
                    <strong id="live-imc" style="font-size: var(--text-lg); font-family: var(--font-mono); color: var(--accent-primary); display: block;">23.5 (Normal)</strong>
                </div>
                <div>
                    <span style="font-size: var(--text-xs); color: var(--text-tertiary);">Score Salud Estimado:</span>
                    <strong id="live-score" style="font-size: var(--text-lg); font-family: var(--font-mono); color: var(--success); display: block;">96 / 100</strong>
                </div>
            </div>

            <button type="submit" class="btn btn-primary btn-lg">
                <svg class="icon icon-sm" aria-hidden="true"><use href="<?php echo asset('img/icons.svg#check'); ?>"></use></svg>
                <span>Guardar Registro Biométrico</span>
            </button>
        </div>
    </form>
</div>

<!-- Tabla Histórica de Signos Vitales -->
<div class="glass-card">
    <div class="card-header">
        <h3 class="card-title">Bitácora Histórica de Signos Vitales</h3>
        <p class="card-subtitle">Evolución longitudinal de constantes fisiológicas</p>
    </div>

    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Fecha / Hora</th>
                    <th>Presión (mmHg)</th>
                    <th>Pulso</th>
                    <th>SpO2</th>
                    <th>Glucosa</th>
                    <th>IMC</th>
                    <th>Score</th>
                    <th>Observaciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($historial_signos as $s): ?>
                    <tr>
                        <td style="font-family: var(--font-mono); font-size: var(--text-xs); white-space: nowrap;">
                            <?php echo formatear_fecha($s['fecha_registro'], true); ?>
                        </td>
                        <td>
                            <strong style="color: var(--text-primary);"><?php echo $s['presion_sistolica'] . '/' . $s['presion_diastolica']; ?></strong>
                        </td>
                        <td><?php echo $s['frecuencia_cardiaca']; ?> ppm</td>
                        <td>
                            <span class="badge badge-info"><?php echo $s['saturacion_oxigeno']; ?>%</span>
                        </td>
                        <td><?php echo $s['glucosa']; ?> mg/dL</td>
                        <td>
                            <span class="badge <?php echo $s['clasificacion_imc'] === 'Normal' ? 'badge-completed' : 'badge-pending'; ?>">
                                <?php echo $s['imc']; ?> (<?php echo $s['clasificacion_imc']; ?>)
                            </span>
                        </td>
                        <td>
                            <strong style="color: <?php echo $s['puntaje_salud'] >= 85 ? 'var(--success)' : ($s['puntaje_salud'] >= 70 ? 'var(--warning)' : 'var(--error)'); ?>; font-family: var(--font-mono);">
                                <?php echo $s['puntaje_salud']; ?>/100
                            </strong>
                        </td>
                        <td style="font-size: var(--text-xs); color: var(--text-secondary); max-width: 200px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                            <?php echo e($s['notas'] ?? 'Sin observaciones'); ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
function recalcularEnVivo() {
    const peso = parseFloat(document.getElementById('peso')?.value) || 0;
    const altura = parseFloat(document.getElementById('altura')?.value) || 0;
    const sistolica = parseInt(document.getElementById('presion_sistolica')?.value) || 120;
    const diastolica = parseInt(document.getElementById('presion_diastolica')?.value) || 80;
    const fc = parseInt(document.getElementById('frecuencia_cardiaca')?.value) || 72;
    const spo2 = parseInt(document.getElementById('saturacion_oxigeno')?.value) || 98;
    const glucosa = parseInt(document.getElementById('glucosa')?.value) || 95;

    if (peso > 0 && altura > 0) {
        const altM = altura / 100;
        const imc = (peso / (altM * altM)).toFixed(1);
        let clasif = 'Normal';
        if (imc < 18.5) clasif = 'Bajo peso';
        else if (imc >= 25 && imc < 30) clasif = 'Sobrepeso';
        else if (imc >= 30) clasif = 'Obesidad';

        document.getElementById('live-imc').textContent = `${imc} (${clasif})`;

        // Cálculo de Score
        let score = 0;
        if (sistolica >= 110 && sistolica <= 125 && diastolica >= 70 && diastolica <= 85) score += 25;
        else if (sistolica <= 135 && diastolica <= 89) score += 18;
        else score += 10;

        if (fc >= 60 && fc <= 85) score += 20;
        else if (fc <= 100) score += 14;
        else score += 8;

        if (spo2 >= 96) score += 20;
        else if (spo2 >= 92) score += 12;
        else score += 5;

        if (glucosa >= 70 && glucosa <= 100) score += 20;
        else if (glucosa <= 125) score += 12;
        else score += 6;

        if (imc >= 18.5 && imc <= 24.9) score += 15;
        else if (imc <= 29.9) score += 9;
        else score += 4;

        score = Math.min(100, Math.max(0, score));
        document.getElementById('live-score').textContent = `${score} / 100`;
    }
}
</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
