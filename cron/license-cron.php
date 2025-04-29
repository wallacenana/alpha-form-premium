<?php
defined('ABSPATH') || exit;

/**
 * Agendamento do cron na ativação
 */
register_activation_hook(__FILE__, function () {
    if (!wp_next_scheduled('alpha_form_daily_license_check')) {
        wp_schedule_event(time(), 'daily', 'alpha_form_daily_license_check');
    }
});

/**
 * Remove cron na desativação
 */
register_deactivation_hook(__FILE__, function () {
    wp_clear_scheduled_hook('alpha_form_daily_license_check');
});

/**
 * Função executada diariamente
 */
add_action('alpha_form_daily_license_check', 'alpha_form_check_license_daily');
function alpha_form_check_license_daily() {
    $license = get_option('alpha_form_license_key');
    $domain  = home_url();

    if (!$license) return;

    $response = wp_remote_get("https://alphaform.com.br/wp-json/alphaform/v2/validate?license={$license}&domain={$domain}");

    if (is_wp_error($response)) return;

    $body = json_decode(wp_remote_retrieve_body($response), true);

    if (isset($body['success'])) {
        update_option('alpha_form_license_status', $body['status'] ?? 'invalid');
        update_option('alpha_form_license_expires', $body['expires'] ?? '');
        update_option('alpha_form_license_domain', $domain);
        update_option('alpha_form_license_checked_at', current_time('mysql'));
        update_option('alpha_form_license_is_active', ($body['status'] ?? '') === 'valid' ? 1 : 0);
    }
}
