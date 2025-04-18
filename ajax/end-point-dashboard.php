<?php

add_action('wp_ajax_alphaform_get_form_widget_count', 'alphaform_get_form_widget_count_handle');

function alphaform_get_form_widget_count_handle()
{
    check_ajax_referer('alphaFormDashboardVars', 'nonce');

    global $wpdb;
    $response = [];

    // Quantidade de formulários únicos (baseado em widget_id)
    $response['total_forms'] = (int) $wpdb->get_var("
        SELECT COUNT(DISTINCT widget_id)
        FROM {$wpdb->prefix}alpha_form_responses
    ");

    // Total de respostas
    $response['total_responses'] = (int) $wpdb->get_var("
        SELECT COUNT(*)
        FROM {$wpdb->prefix}alpha_form_responses
    ");

    // Última submissão
    $response['total_integrations'] = (int) $wpdb->get_var("
        SELECT COUNT(*)
        FROM {$wpdb->prefix}alpha_form_integrations
    ");

    // Retorno final
    wp_send_json_success($response);
}
