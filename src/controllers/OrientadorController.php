<?php
/**
 * Controlador del Orientador de Síntomas
 */

require_once __DIR__ . '/../../config/config.php';

class OrientadorController {
    /**
     * Mostrar orientador de síntomas
     */
    public function index() {
        if (!esta_autenticado()) {
            redirigir('login');
            return;
        }

        $resultado = null;
        $sintomas_seleccionados = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $sintomas_seleccionados = $_POST['sintomas'] ?? [];
            $resultado = $this->evaluarSintomas($sintomas_seleccionados);
        }

        $titulo = 'Orientador de Síntomas';
        require_once __DIR__ . '/../views/orientador/index.php';
    }

    /**
     * Evaluar síntomas (reglas básicas)
     */
    private function evaluarSintomas($sintomas) {
        if (empty($sintomas)) {
            return [
                'especialidad' => 'Medicina General',
                'urgencia' => 'baja',
                'recomendacion' => 'Si experimentas algún síntoma, agenda una cita de control con medicina general.'
            ];
        }

        $urgentes = ['dolor_pecho', 'dificultad_respirar_severa', 'confusion', 'perdida_consciencia'];
        $tiene_urgente = !empty(array_intersect($sintomas, $urgentes));

        if ($tiene_urgente) {
            return [
                'especialidad' => 'Urgencias',
                'urgencia' => 'alta',
                'recomendacion' => '⚠️ Acude INMEDIATAMENTE a urgencias o llama al número de emergencias. Los síntomas que describes requieren atención médica urgente.'
            ];
        }

        // Reglas simples por especialidad
        if (in_array('dolor_pecho_leve', $sintomas) || in_array('palpitaciones', $sintomas)) {
            return [
                'especialidad' => 'Cardiología',
                'urgencia' => 'media',
                'recomendacion' => 'Se recomienda agendar una cita con cardiología para evaluación.'
            ];
        }

        if (in_array('erupcion_piel', $sintomas) || in_array('picazon', $sintomas)) {
            return [
                'especialidad' => 'Dermatología',
                'urgencia' => 'baja',
                'recomendacion' => 'Agenda una cita con dermatología para evaluación de tu piel.'
            ];
        }

        if (in_array('fiebre', $sintomas) && in_array('tos', $sintomas)) {
            return [
                'especialidad' => 'Medicina General',
                'urgencia' => 'media',
                'recomendacion' => 'Podrías tener una infección respiratoria. Agenda cita con medicina general.'
            ];
        }

        return [
            'especialidad' => 'Medicina General',
            'urgencia' => 'baja',
            'recomendacion' => 'Agenda una cita con medicina general para evaluación de tus síntomas.'
        ];
    }
}
