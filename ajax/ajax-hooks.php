<?php

add_action('wp_ajax_alphaform_get_repeater_fields', function () {
    check_ajax_referer('alpha_form_nonce', 'nonce');

    $widget_id = sanitize_text_field($_POST['widget_id'] ?? '');
    $post_id = intval($_POST['post_id'] ?? 0);

    if (!$widget_id || !$post_id) {
        wp_send_json_error('ID do widget ou post não informado');
    }

    $raw_data = get_post_meta($post_id, '_elementor_data', true);
    if (!$raw_data) {
        wp_send_json_error('Sem dados do Elementor');
    }

    $data = json_decode($raw_data, true);
    if (!is_array($data)) {
        wp_send_json_error('Erro ao decodificar JSON');
    }

    foreach ($data as $container) {
        if (!empty($container['elements'])) {
            foreach ($container['elements'] as $child) {
                if (isset($child['id']) && $child['id'] === $widget_id) {
                    $campos = $child['settings']['form_fields'] ?? [];
                    $resultado = [];

                    foreach ($campos as $campo) {
                        $id = $campo['_id'] ?? '';
                        $label = $campo['field_label'] ?? '';
                        if ($id && $label) {
                            $resultado[$id] = $label;
                        }
                    }

                    wp_send_json_success($resultado);
                }
            }
        }
    }

    wp_send_json_error('Campos não encontrados');
});
