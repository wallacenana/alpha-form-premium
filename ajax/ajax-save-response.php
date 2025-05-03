<?php
add_action('wp_ajax_alpha_form_save_response', 'alpha_form_save_response');
add_action('wp_ajax_nopriv_alpha_form_save_response', 'alpha_form_save_response');

function alpha_form_save_response()
{
    check_ajax_referer('alpha_form_nonce', 'nonce');
    global $wpdb;

    $form_id      = sanitize_text_field(wp_unslash($_POST['form_id'] ?? ''));
    $session_id   = sanitize_text_field(wp_unslash($_POST['session_id'] ?? ''));
    $widget_id    = sanitize_text_field(wp_unslash($_POST['widgetId'] ?? ''));
    $postId       = sanitize_text_field(wp_unslash($_POST['postId'] ?? ''));
    $duration     = intval(wp_unslash($_POST['duration'] ?? 0));
    $lang         = sanitize_text_field(wp_unslash($_POST['lang'] ?? ''));
    $platform     = sanitize_text_field(wp_unslash($_POST['platform'] ?? ''));
    $device_type  = sanitize_text_field(wp_unslash($_POST['device_type'] ?? ''));
    $timezone     = sanitize_text_field(wp_unslash($_POST['timezone'] ?? ''));
    $user_agent   = sanitize_textarea_field(wp_unslash($_POST['user_agent'] ?? ''));
    $ip_address   = isset($_SERVER['REMOTE_ADDR']) ? sanitize_text_field(wp_unslash($_SERVER['REMOTE_ADDR'])) : '';
    $geo_lat      = sanitize_text_field(wp_unslash($_POST['latitude'] ?? ''));
    $geo_lng      = isset($_POST['longitude']) ? sanitize_text_field(wp_unslash($_POST['longitude'])) : null;
    $browser      = isset($_POST['browser']) ? sanitize_text_field(wp_unslash($_POST['browser'])) : '';

    $response = isset($_POST['response']) ? json_decode(sanitize_text_field(wp_unslash($_POST['response'])), true) : [];

    if (!$form_id || !$session_id || empty($response) || empty($widget_id)) {
        wp_send_json_error(['message' => 'Campos obrigatórios ausentes.']);
    }

    $table = $wpdb->prefix . 'alpha_form_responses';

    // Verifica se já existe registro para essa sessão + formulário
    $cache_key_existing = 'alpha_form_existing_' . md5(implode('_', [$form_id, $session_id, $widget_id, $postId]));

    $existing = wp_cache_get($cache_key_existing, 'alpha_form');

    if (false === $existing) {
        // phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.PreparedSQL.InterpolatedNotPrepared	
        $existing = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT id FROM $table WHERE form_id = %s AND session_id = %s AND widget_id = %s AND postId = %d",
                $form_id,
                $session_id,
                $widget_id,
                $postId
            )
        );

        if (!is_null($existing)) {
            wp_cache_set($cache_key_existing, $existing, 'alpha_form', 300); // cache de 5 minutos
        }
    }

    if ($existing) {
        // Atualiza o JSON existente
        $cache_key_existing_json = 'alpha_form_existing_json_' . (int) $existing;

        $existing_json = wp_cache_get($cache_key_existing_json, 'alpha_form');

        if (false === $existing_json) {
            $existing_json = $wpdb->get_var(
                $wpdb->prepare(
                    "SELECT data FROM $table WHERE id = %d",
                    $existing
                )
            );

            if (!is_null($existing_json)) {
                wp_cache_set($cache_key_existing_json, $existing_json, 'alpha_form', 300); // cache de 5 minutos
            }
        }

        $existing_data = json_decode($existing_json, true) ?? [];
        // phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.PreparedSQL.InterpolatedNotPrepared	

        // Mescla nova resposta ao JSON existente
        $merged_data = array_merge($existing_data, $response);

        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
        $updated = $wpdb->update(
            $table,
            [
                'data' => wp_json_encode($merged_data),
                'submitted_at' => current_time('mysql'),
                'start_form' => 1
            ],
            ['id' => $existing],
            ['%s', '%s', '%d'], // Aqui estava errado! precisa de 3 formatos!
            ['%d']
        );


        if ($updated === false) {
            wp_send_json_error(['message' => 'Erro ao atualizar a submissão.']);
        }

        wp_send_json_success(['message' => 'Resposta atualizada com sucesso.']);
    } else {
        // Insere nova linha com a primeira resposta
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
        $inserted = $wpdb->insert(
            $table,
            [
                'form_id'      => $form_id,
                'session_id'   => $session_id,
                'widget_id'    => $widget_id,
                'postId'       => $postId,
                'data'         => wp_json_encode($response),
                'submitted_at' => current_time('mysql'),
                'created_at'   => current_time('mysql'),
                'duration'     => $duration,
                'lang'         => $lang,
                'platform'     => $platform,
                'device_type'  => $device_type,
                'timezone'     => $timezone,
                'user_agent'   => $user_agent,
                'ip_address'   => $ip_address,
                'latitude'     => $geo_lat,
                'longitude'    => $geo_lng,
                'browser'      => $browser,
                'page_view'    => 1,
            ],
            [
                '%s', // form_id
                '%s', // session_id
                '%s', // widget_id
                '%d', // postId
                '%s', // data
                '%s', // submitted_at
                '%s', // created_at
                '%d', // duration
                '%s', // lang
                '%s', // platform
                '%s', // device_type
                '%s', // timezone
                '%s', // user_agent
                '%s', // ip_address
                '%s', // latitude
                '%s', // longitude
                '%s', // browser
                '%d', // page_view
            ]
        );


        if ($inserted === false) {
            wp_send_json_error(['message' => 'Erro ao salvar nova submissão.', 'sql' => $wpdb->last_error]);
        }

        wp_send_json_success(['message' => 'Resposta salva com sucesso.']);
    }
}


add_action('wp_ajax_alpha_form_save_geo', 'alpha_form_save_geo_callback');

function alpha_form_save_geo_callback()
{
    check_ajax_referer('alpha_form_nonce', 'nonce');

    $session_id = isset($_POST['session_id']) ? sanitize_text_field(wp_unslash($_POST['session_id'])) : '';
    $latitude = isset($_POST['latitude']) ? floatval($_POST['latitude']) : null;
    $longitude = isset($_POST['longitude']) ? floatval($_POST['longitude']) : null;

    if (empty($session_id) || is_null($latitude) || is_null($longitude)) {
        wp_send_json_error(['message' => 'Dados obrigatórios faltando.']);
    }

    // Consulta ao OpenStreetMap para pegar cidade/estado/país
    $url = "https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat={$latitude}&lon={$longitude}";

    $response = wp_remote_get($url, [
        'timeout' => 8,
        'headers' => ['User-Agent' => 'AlphaFormBot/1.0']
    ]);

    if (is_wp_error($response)) {
        wp_send_json_error(['message' => 'Erro ao consultar API.', 'error' => $response->get_error_message()]);
    }

    $body = wp_remote_retrieve_body($response);
    $data = json_decode($body, true);
    $address = $data['address'] ?? [];

    $city = $address['city'] ?? ($address['town'] ?? ($address['village'] ?? ''));
    $state = $address['state'] ?? '';
    $country = $address['country'] ?? '';
    $country_code = strtoupper($address['country_code'] ?? '');

    global $wpdb;
    $table = $wpdb->prefix . 'alpha_form_responses';

    // Sempre faz UPDATE
    // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
    $result = $wpdb->update(
        $table,
        [
            'latitude' => $latitude,
            'longitude' => $longitude,
            'city' => $city,
            'state' => $state,
            'country' => $country,
            'country_code' => $country_code,
        ],
        ['session_id' => $session_id],
        [
            '%f',
            '%f',
            '%s',
            '%s',
            '%s',
            '%s'
        ],
        ['%s']
    );

    if ($result === false) {
        wp_send_json_error([
            'message' => 'Erro ao atualizar no banco de dados.',
            'sql_error' => $wpdb->last_error
        ]);
    }

    wp_send_json_success([
        'message' => 'Localização atualizada com sucesso.',
        'location' => [
            'city' => $city,
            'state' => $state,
            'country' => $country,
            'country_code' => $country_code
        ]
    ]);
}
