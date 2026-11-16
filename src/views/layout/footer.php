    </main>

    <?php if (esta_autenticado()): ?>
        <footer class="footer">
            <p>&copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?>. Sistema de Gestión de Salud.</p>
            <p><small>Versión 1.0 - Proyecto Universitario</small></p>
        </footer>
    <?php endif; ?>

    <script src="<?php echo asset('js/chart.min.js'); ?>"></script>
    <script src="<?php echo asset('js/app.js'); ?>"></script>
</body>
</html>
