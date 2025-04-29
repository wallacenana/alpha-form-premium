jQuery(window).on('elementor:init', function () {
    elementor.channels.editor.on('alphaform:editor:send_click', function () {
        const panel = elementor.getPanelView();
        const model = panel?.getCurrentPageView()?.model;
        const widgetId = model?.id;
        const widgetType = model?.attributes?.widgetType;
        const postId = alphaFormVars.post_id || 0;

        if (!widgetId || widgetType !== 'alpha_form') return;

        // AJAX direto
        jQuery.ajax({
            url: alphaFormVars.ajaxurl,
            method: 'POST',
            dataType: 'json',
            data: {
                action: 'alphaform_get_repeater_fields',
                widget_id: widgetId,
                post_id: postId,
                nonce: alphaFormVars.nonce
            },
            success: function (res) {
                if (!res.success || !res.data) {
                    alert('Nenhum campo encontrado.');
                    return;
                }

                const campos = res.data;

                jQuery('select[data-setting^="map_field_"]').each(function () {
                    const $select = jQuery(this);
                    const campo = $select.data('setting');
                    const valorAtual = model.get('settings').get(campo);

                    $select.empty();
                    $select.append('<option value="">— Nenhum campo —</option>');

                    jQuery.each(campos, function (val, label) {
                        const selected = val === valorAtual ? 'selected' : '';
                        $select.append(`<option value="${val}" ${selected}>${label}</option>`);
                    });
                });

                elementor.notifications.showToast({
                    message: 'Campos atualizados!',
                    type: 'success',
                    timeout: 2000
                });
            },
            error: function (xhr) {
                console.error('[AlphaForm] Erro AJAX:', xhr.responseText);
                alert('Erro ao buscar campos. Verifique o console.');
            }
        });
    });
});
