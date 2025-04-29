<?php
// Caminho: module/actions/mailchimp.php
require_once ALPHA_FORM_PLUGIN_PATH . 'vendor/autoload.php';

use MailchimpMarketing\ApiClient;

function alphaform_send_to_mailchimp($form_data)
{
    $api_key = get_option('alpha_form_mailchimp_api_key');
    $list_id = sanitize_text_field($form_data['listaIdMC'] ?? '');
    $server_prefix = '';

    if (!$api_key || !$list_id) {
        // error_log('[AlphaForm] Mailchimp - API key ou lista ausente.');
        return false;
    }

    // Extrai prefixo do servidor a partir da API key (ex: us14)
    if (strpos($api_key, '-') !== false) {
        [, $server_prefix] = explode('-', $api_key);
    }

    if (!$server_prefix) {
        // error_log('[AlphaForm] Mailchimp - Prefixo do servidor inválido.');
        return false;
    }

    $email = $form_data['email'] ?? '';
    if (empty($email)) {
        // error_log('[AlphaForm] Mailchimp - Email não fornecido.');
        return false;
    }

    $payload = [
        'email_address' => $email,
        'status_if_new' => 'subscribed',
        'status'        => 'subscribed',
        'merge_fields'  => [
            'FNAME' => $form_data['fname_mc'] ?? '',
            'LNAME' => $form_data['last_name'] ?? '',
        ],
    ];

    $email_hash = md5(strtolower($email));

    try {
        $mailchimp = new ApiClient();
        $mailchimp->setConfig([
            'apiKey' => $api_key,
            'server' => $server_prefix,
        ]);

        // Tenta criar ou atualizar (PUT é seguro nesse caso)
        $mailchimp->lists->setListMember($list_id, $email_hash, $payload);

        return true;
    } catch (\Exception $e) {
        // error_log('[AlphaForm] Mailchimp - Erro Detalhado: ' . $e->getMessage());

        if (method_exists($e, 'getResponse')) {
            $body = (string) $e->getResponse()->getBody();
            // error_log('[AlphaForm] Mailchimp - Corpo da resposta: ' . $body);
        }

        return false;
    }
}
