<?php
// Caminho: includes/ajax/alphaform-actions.php

add_action('wp_ajax_alphaform_get_widget_actions', 'alphaform_get_widget_actions');
add_action('wp_ajax_nopriv_alphaform_get_widget_actions', 'alphaform_get_widget_actions');

function buscar_widget_recursivo($elements, $widget_id)
{
    foreach ($elements as $element) {
        if (($element['id'] ?? '') === $widget_id && ($element['widgetType'] ?? '') === 'alpha_form') {
            return $element;
        }

        if (!empty($element['elements']) && is_array($element['elements'])) {
            $found = buscar_widget_recursivo($element['elements'], $widget_id);
            if ($found) return $found;
        }
    }

    return null;
}

function alphaform_get_widget_actions()
{
    check_ajax_referer('alpha_form_nonce', 'nonce');

    $post_id = intval($_POST['post_id'] ?? 0);
    $widget_id = isset($_POST['widget_id']) ? sanitize_text_field(wp_unslash($_POST['widget_id'])) : '';

    if (!$post_id || !$widget_id) {
        wp_send_json_error('ID do post ou widget ausente');
    }

    $raw = get_post_meta($post_id, '_elementor_data', true);
    if (!$raw) wp_send_json_error('Elemento não encontrado.');

    $data = json_decode($raw, true);
    if (!is_array($data)) wp_send_json_error('Erro ao processar JSON.');

    $widget = buscar_widget_recursivo($data, $widget_id);
    if (!$widget) wp_send_json_error('Widget não encontrado no post.');

    $settings = $widget['settings'] ?? [];
    $actions = $settings['actions'] ?? [];
    $listaId = $settings['listasExistentes'] ?? [];
    $listaIdMC = $settings['mailchimp_list_id'] ?? [];
    $webhook_url = $settings['webhook_url'] ?? [];

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
        'listaId' => $listaId,
        'listaIdMC' => $listaIdMC,
        'webhook_url' => $webhook_url,
    ]);
}



add_action('wp_ajax_alphaform_send_integrations', 'alphaform_send_integrations_handler');
add_action('wp_ajax_nopriv_alphaform_send_integrations', 'alphaform_send_integrations_handler');

function alphaform_send_integrations_handler()
{
    check_ajax_referer('alpha_form_nonce', 'nonce');

    global $wpdb;

    $actions = isset($_POST['actions']) ? json_decode(sanitize_textarea_field(wp_unslash($_POST['actions'])), true) : [];

    if (!is_array($actions)) {
        wp_send_json_error('Ações inválidas.');
    }

    $form_data = [];
    foreach ($_POST as $key => $value) {
        if (in_array($key, ['action', 'nonce', 'actions'])) continue;
        $form_data[$key] = sanitize_text_field($value);
    }

    if (!empty($_POST['is_final_submission'])) {
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
        $wpdb->update(
            "{$wpdb->prefix}alpha_form_responses",
            ['concluido' => 1],
            ['session_id' => sanitize_text_field($form_data['session_id'])],
            ['%d'],
            ['%s']
        );
    }


    // ActiveCampaign
    if (in_array('integration_activecampaign', $actions)) {
        require_once ALPHA_FORM_PLUGIN_PATH . 'module/actions/activecampaign.php';
        $ok = alphaform_send_to_activecampaign($form_data);
        if (!$ok) wp_send_json_error('Erro no envio para o ActiveCampaign');
    }

    // Mailchimp
    if (in_array('integration_mailchimp', $actions)) {
        require_once ALPHA_FORM_PLUGIN_PATH . 'module/actions/mailchimp.php';
        $ok = alphaform_send_to_mailchimp($form_data);
        if (!$ok) wp_send_json_error('Erro no envio para o Mailchimp');
    }

    if (in_array('email', $actions)) {
        require_once ALPHA_FORM_PLUGIN_PATH . 'module/actions/email.php';
        $ok = alphaform_send_email($form_data);
        if (!$ok) wp_send_json_error('Erro ao enviar o e-mail.');
    }

    if (in_array('webhook', $actions)) {
        $form_data_hook = [];
        $ignorar = ['action', 'nonce', 'actions', 'post_id', 'widget_id', 'listaId', 'listaIdMC', 'email', 'first_name'];


        foreach ($_POST as $key => $value) {
            if (in_array($key, $ignorar)) continue;

            // Permite arrays (como campos de múltipla escolha)
            $form_data_hook[$key] = is_array($value)
                ? array_map('sanitize_text_field', $value)
                : sanitize_text_field($value);
        }
        // error_log(json_encode($form_data_hook, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        require_once ALPHA_FORM_PLUGIN_PATH . 'module/actions/webhook.php';
        $ok = alphaform_send_to_webhook(
            $form_data_hook,
            isset($_POST['webhook_url']) ? sanitize_text_field(wp_unslash($_POST['webhook_url'])) : ''
        );
        if (!$ok) wp_send_json_error('Erro no envio para o Webhook.');
    }



    wp_send_json_success('Dados enviados com sucesso para as integrações.');
}
