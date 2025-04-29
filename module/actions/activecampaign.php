<?php
// Caminho: module/actions/activecampaign.php
require_once ALPHA_FORM_PLUGIN_PATH . 'vendor/autoload.php';

use GuzzleHttp\Client;

function alphaform_send_to_activecampaign($form_data)
{
    $api_key = get_option('alpha_form_activecampaign_api_key');
    $api_url = rtrim(get_option('alpha_form_activecampaign_api_url'), '/') . '/';

    if (!$api_key || !$api_url) {
        // error_log('[AlphaForm] ActiveCampaign - Dados de API ausentes.');
        return false;
    }

    // 🔎 Dados mapeados já chegam prontos
    $email = $form_data['email'] ?? '';
    $firstName = $form_data['first_name'] ?? '';
    $lastName = $form_data['last_name'] ?? '';
    $phone = $form_data['phone'] ?? '';
    $organization = $form_data['organization'] ?? '';
    $list_id = $form_data['listaId'] ?? null;

    if (empty($email)) {
        // error_log('[AlphaForm] ActiveCampaign - Email ausente nos dados enviados.');
        return false;
    }

    $client = new Client();

    try {
        // 1. Cria ou atualiza o contato
        $response = $client->request('POST', $api_url . 'api/3/contact/sync', [
            'headers' => [
                'Api-Token' => $api_key,
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'contact' => [
                    'email' => $email,
                    'firstName' => $firstName,
                    'lastName' => $lastName,
                    'phone' => $phone,
                    'organization' => $organization,
                ]
            ]
        ]);

        $result = json_decode($response->getBody(), true);
        $contact_id = $result['contact']['id'] ?? null;

        if (!$contact_id) {
            // error_log('[AlphaForm] ActiveCampaign - Falha ao obter ID do contato.');
            return false;
        }

        // 2. Associa à lista, se enviada
        if (!empty($list_id)) {
            $client->request('POST', $api_url . 'api/3/contactLists', [
                'headers' => [
                    'Api-Token' => $api_key,
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'contactList' => [
                        'list' => $list_id,
                        'contact' => $contact_id,
                        'status' => 1
                    ]
                ]
            ]);
        }

        return true;
    } catch (\Exception $e) {
        // error_log('[AlphaForm] ActiveCampaign - Erro: ' . $e->getMessage());
        return false;
    }
}
