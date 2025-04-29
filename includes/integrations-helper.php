<?php
defined('ABSPATH') || exit;

/**
 * Salva ou atualiza os dados da integração.
 *
 * @param string $type    Tipo da integração (ex: 'mailchimp', 'hubspot').
 * @param array  $settings Dados a serem salvos (ex: ['api_key' => '...']).
 * @param int    $status  Status da integração (1 = ativa, 0 = desativada).
 */
function alpha_form_save_integration($type, $settings, $status = 1)
{
    global $wpdb;
    $table = $wpdb->prefix . 'alpha_form_integrations';

    $encoded = wp_json_encode($settings);

    // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
    $existing = $wpdb->get_var(
        $wpdb->prepare(
            "SELECT id FROM %i WHERE type = %s LIMIT 1",
            $table,
            $type
        )
    );

    if ($existing) {
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
        $wpdb->update(
            $table,
            [
                'settings'   => $encoded,
                'status'     => $status,
                'updated_at' => current_time('mysql'),
            ],
            ['id' => $existing],
            ['%s', '%d', '%s'],
            ['%d']
        );
    } else {
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
        $wpdb->insert(
            $table,
            [
                'type'       => $type,
                'settings'   => $encoded,
                'status'     => $status,
                'created_at' => current_time('mysql'),
                'updated_at' => current_time('mysql'),
            ],
            ['%s', '%s', '%d', '%s', '%s']
        );
    }

    foreach ($settings as $key => $value) {
        update_option("alpha_form_{$type}_{$key}", $value);
    }
}


/**
 * Retorna os dados de uma integração específica.
 *
 * @param string $type
 * @return array
 */
function alpha_form_get_integration($type)
{
    global $wpdb;
    $table = $wpdb->prefix . 'alpha_form_integrations';

    // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
    $json = $wpdb->get_var(
        $wpdb->prepare(
            "SELECT settings FROM %i WHERE type = %s LIMIT 1",
            $table,
            $type
        )
    );
    return json_decode($json, true) ?? [];
}

/**
 * Retorna todas as integrações salvas e ativas.
 *
 * @return array
 */
function alpha_form_get_available_integrations()
{
    global $wpdb;
    $table = $wpdb->prefix . 'alpha_form_integrations';
    // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
    $rows = $wpdb->get_results(
        $wpdb->prepare(
            "SELECT type, settings FROM %i WHERE status = 1",
            $table
        ),
        ARRAY_A
    );

    $output = [];
    foreach ($rows as $row) {
        $type = $row['type'];
        $settings = json_decode($row['settings'], true);
        if (!empty($settings)) {
            $output[$type] = $settings;
        }
    }
    return $output;
}

/**
 * Retorna as listas disponíveis de uma integração específica.
 */
function alpha_form_get_integration_lists($slug)
{
    $data = alpha_form_get_integration($slug);
    if (!$data) return [];

    switch ($slug) {
        case 'mailchimp':
            require_once dirname(ALPHA_FORM_PLUGIN_PATH) . '/alpha-form-premium/vendor/autoload.php';
            $api_key = $data['api_key'];
            $server = substr($api_key, strpos($api_key, '-') + 1);
            $client = new \MailchimpMarketing\ApiClient();
            $client->setConfig(['apiKey' => $api_key, 'server' => $server]);
            try {
                $response = $client->lists->getAllLists(['count' => 100]);
                $lists = [];

                if (isset($response->lists) && is_array($response->lists)) {
                    foreach ($response->lists as $list) {
                        $lists[$list->id] = $list->name;
                    }
                }

                return $lists;
            } catch (Throwable $e) {
                return [];
            }

        case 'activecampaign':
            $api_url = trailingslashit($data['api_url']) . 'api/3/lists';
            $api_key = $data['api_key'];
            $response = wp_remote_get($api_url, [
                'headers' => ['Api-Token' => $api_key],
                'timeout' => 10,
            ]);
            if (!is_wp_error($response)) {
                $body = json_decode(wp_remote_retrieve_body($response));
                $lists = [];
                foreach ($body->lists ?? [] as $list) {
                    $lists[$list->id] = $list->name;
                }
                return $lists;
            }
            return [];

        case 'hubspot':
            $token = $data['access_token'];
            $response = wp_remote_get('https://api.hubapi.com/crm/v3/properties/contacts', [
                'headers' => ['Authorization' => 'Bearer ' . $token],
                'timeout' => 10,
            ]);
            return ['default' => 'Contatos']; // Por enquanto, lista única
    }

    return [];
}

/**
 * Retorna os campos (merge fields) de uma lista da integração.
 */
function alpha_form_get_list_fields($slug, $list_id)
{
    $data = alpha_form_get_integration($slug);
    if (!$data || !$list_id) return [];

    switch ($slug) {
        case 'mailchimp':
            require_once dirname(ALPHA_FORM_PLUGIN_PATH) . '/alpha-form-premium/vendor/autoload.php';
            $api_key = $data['api_key'];
            $server = substr($api_key, strpos($api_key, '-') + 1);
            $client = new \MailchimpMarketing\ApiClient();
            $client->setConfig(['apiKey' => $api_key, 'server' => $server]);

            try {
                $response = $client->lists->getListMergeFields($list_id);
                $fields = [];
                foreach ($response->merge_fields as $field) {
                    $fields[$field->tag] = $field->name;
                }
                return $fields;
            } catch (Throwable $e) {
                return [];
            }
    }

    return [];
}

/**
 * Shortcodes disponíveis para mapeamento de campos.
 */
function alpha_form_get_available_fields_from_widget_array($fields)
{
    $options = [];

    if (!is_array($fields)) {
        return $options;
    }

    foreach ($fields as $field) {
        if (!empty($field['custom_id']) && !empty($field['field_label'])) {
            $id = sanitize_title($field['custom_id']);
            $label = $field['field_label'];
            $options[$id] = $label;
        }
    }

    return $options;
}


function alpha_form_get_remote_fields_activecampaign()
{
    $data = alpha_form_get_integration('activecampaign');
    if (empty($data['api_key']) || empty($data['api_url'])) {
        return [];
    }

    $api_url = rtrim($data['api_url'], '/') . '/api/3/fields';
    $api_key = $data['api_key'];

    $response = wp_remote_get($api_url, [
        'headers' => [
            'Api-Token' => $api_key
        ],
        'timeout' => 10,
    ]);

    if (is_wp_error($response)) {
        return [];
    }

    $body = json_decode(wp_remote_retrieve_body($response), true);
    if (empty($body['fields']) || !is_array($body['fields'])) {
        return [];
    }

    $fields = [];
    foreach ($body['fields'] as $field) {
        if (!empty($field['id']) && !empty($field['title'])) {
            $fields[$field['id']] = $field['title'];
        }
    }

    return $fields;
}
