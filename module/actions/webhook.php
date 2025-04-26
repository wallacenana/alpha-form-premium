<?php
// Caminho: module/actions/webhook.php

function alphaform_send_to_webhook($dados, $url)
{
    // error_log(json_encode($dados, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    if (empty($url)) {
        error_log('[AlphaForm] Webhook - URL não fornecida.');
        return false;
    }

    try {
        $response = wp_remote_post($url, [
            'method' => 'POST',
            'timeout' => 10,
            'headers' => ['Content-Type' => 'application/json'],
            'body' => wp_json_encode($dados),
        ]);

        if (is_wp_error($response)) {
            error_log('[AlphaForm] Webhook - Erro WP: ' . $response->get_error_message());
            return false;
        }

        if (wp_remote_retrieve_response_code($response) >= 400) {
            error_log('[AlphaForm] Webhook - Erro HTTP: ' . wp_remote_retrieve_response_message($response));
            return false;
        }

        return true;
    } catch (Exception $e) {
        error_log('[AlphaForm] Webhook - Exceção: ' . $e->getMessage());
        return false;
    }
}
