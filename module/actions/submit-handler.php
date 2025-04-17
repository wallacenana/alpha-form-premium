<?php
// Caminho: includes/ajax/alphaform-actions.php

add_action('wp_ajax_alphaform_get_widget_actions', 'alphaform_get_widget_actions');
add_action('wp_ajax_nopriv_alphaform_get_widget_actions', 'alphaform_get_widget_actions');

function alphaform_get_widget_actions()
{
    check_ajax_referer('alpha_form_nonce', 'nonce');

    $post_id = intval($_POST['post_id'] ?? 0);
    $widget_id = sanitize_text_field($_POST['widget_id'] ?? '');

    if (!$post_id || !$widget_id) {
        wp_send_json_error('ID do post ou widget ausente');
    }

    $raw = get_post_meta($post_id, '_elementor_data', true);
    if (!$raw) wp_send_json_error('Elemento não encontrado.');

    $data = json_decode($raw, true);
    if (!is_array($data)) wp_send_json_error('Erro ao processar JSON.');

    foreach ($data as $element) {
        if (!empty($element['elements']) && is_array($element['elements'])) {
            foreach ($element['elements'] as $child) {
                if (($child['id'] ?? '') === $widget_id && ($child['widgetType'] ?? '') === 'alpha_form') {
                    $settings = $child['settings'] ?? [];
                    $actions = $settings['actions'] ?? [];
                    $listaId = $settings['listasExistentes'] ?? [];

                    $map = [];
                    foreach ($settings as $key => $value) {
                        if (strpos($key, 'map_field_') === 0) {
                            $field_key = str_replace('map_field_', '', $key);
                            $map[$field_key] = $value;
                        }
                    }

                    wp_send_json_success([
                        'actions' => $actions,
                        'map' => $map,
                        'listaId' => $listaId
                    ]);
                }
            }
        }
    }

    wp_send_json_error('Widget não encontrado no post.');
}


add_action('wp_ajax_alphaform_send_to_activecampaign', 'alphaform_send_to_activecampaign_handler');
add_action('wp_ajax_nopriv_alphaform_send_to_activecampaign', 'alphaform_send_to_activecampaign_handler');

function alphaform_send_to_activecampaign_handler() {
    check_ajax_referer('alpha_form_nonce', 'nonce');

    require_once ALPHA_FORM_PLUGIN_PATH . 'module/actions/activecampaign.php';

    // Monta array com os dados recebidos
    $form_data = [];
    foreach ($_POST as $key => $value) {
        if (in_array($key, ['action', 'nonce'])) continue;
        $form_data[$key] = sanitize_text_field($value);
    }

    $ok = alphaform_send_to_activecampaign($form_data);

    if ($ok) {
        wp_send_json_success('Dados enviados com sucesso para ActiveCampaign.');
    } else {
        wp_send_json_error('Erro ao enviar dados para ActiveCampaign.');
    }
}
