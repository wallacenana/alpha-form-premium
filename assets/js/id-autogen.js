(function ($) {
    if (!window.elementor) return;

    function updateShortcodeFields() {
        $('.elementor-repeater .elementor-repeater-row').each(function () {
            const $row = $(this);
            const $fieldIdInput = $row.find('.elementor-control-field_id input');
            const $shortcodeInput = $row.find('.elementor-control-field_shortcode input');

            if (!$fieldIdInput.length || !$shortcodeInput.length) return;

            // Gera ID se vazio
            if (!$fieldIdInput.val().trim()) {
                const uniqueId = 'id_' + Math.random().toString(36).substr(2, 6);
                $fieldIdInput.val(uniqueId).trigger('input');
            }

            // Atualiza shortcode ao editar
            $fieldIdInput.off('input.alphaform').on('input.alphaform', function () {
                const newId = $(this).val().trim();
                $shortcodeInput.val(`[field-${newId}]`);
            });
        });
    }

    // Quando abre o painel do widget
    elementor.hooks.addAction('panel/open_editor/widget', function () {
        setTimeout(updateShortcodeFields, 300);

        // Observador de mudanças no DOM
        const observer = new MutationObserver(function () {
            updateShortcodeFields();
        });

        const target = document.querySelector('.elementor-panel');
        if (target) {
            observer.observe(target, {
                childList: true,
                subtree: true
            });
        }
    });
})(jQuery);
