<?php
add_action('wp_ajax_alpha_form_save_response', 'alpha_form_save_response');
add_action('wp_ajax_nopriv_alpha_form_save_response', 'alpha_form_save_response');

function alpha_form_save_response()
{
    check_ajax_referer('alpha_form_nonce', 'nonce');
    global $wpdb;

    $form_id      = sanitize_text_field($_POST['form_id'] ?? '');
    $event_type   = intval($_POST['event_type'] ?? '');
    $session_id   = sanitize_text_field($_POST['session_id'] ?? '');
    $widget_id    = sanitize_text_field($_POST['widgetId'] ?? '');
    $postId       = sanitize_text_field($_POST['postId'] ?? '');
    $duration     = intval($_POST['duration'] ?? 0);
    $lang         = sanitize_text_field($_POST['lang'] ?? '');
    $platform     = sanitize_text_field($_POST['platform'] ?? '');
    $device_type  = sanitize_text_field($_POST['device_type'] ?? '');
    $timezone     = sanitize_text_field($_POST['timezone'] ?? '');
    $user_agent   = sanitize_textarea_field($_POST['user_agent'] ?? '');
    $ip_address   = $_SERVER['REMOTE_ADDR'] ?? '';
    $geo_lat      = sanitize_text_field($_POST['latitude']) ?? null;
    $geo_lng      =  sanitize_text_field($_POST['longitude']) ?? null;
    $browser      = sanitize_text_field($_POST['browser'] ?? '');

    $response   = isset($_POST['response']) ? json_decode(stripslashes($_POST['response']), true) : [];

    if (!$form_id || !$session_id || empty($response) || empty($widget_id)) {
        wp_send_json_error(['message' => 'Campos obrigatórios ausentes.']);
    }

    error_log($event_type);

    $table = $wpdb->prefix . 'alpha_form_responses';

    // Verifica se já existe registro para essa sessão + formulário
    $existing = $wpdb->get_var($wpdb->prepare(
        "SELECT id FROM $table WHERE form_id = %s AND session_id = %s AND widget_id = %s AND postId = %d",
        $form_id,
        $session_id,
        $widget_id,
        $postId
    ));

    if ($existing) {
        // Atualiza o JSON existente
        $existing_json = $wpdb->get_var($wpdb->prepare(
            "SELECT data FROM $table WHERE id = %d",
            $existing
        ));

        $existing_data = json_decode($existing_json, true) ?? [];

        // Mescla nova resposta ao JSON existente
        $merged_data = array_merge($existing_data, $response);

        $updated = $wpdb->update(
            $table,
            [
                'data' => wp_json_encode($merged_data),
                'submitted_at' => current_time('mysql'),
                'start_form' => 1
            ],
            ['id' => $existing],
            ['%s', '%s'],
            ['%d']
        );

        if ($updated === false) {
            wp_send_json_error(['message' => 'Erro ao atualizar a submissão.']);
        }

        wp_send_json_success(['message' => 'Resposta atualizada com sucesso.']);
    } else {
        // Insere nova linha com a primeira resposta
        $inserted = $wpdb->insert($table, [
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
        ]);

        if ($inserted === false) {
            wp_send_json_error(['message' => 'Erro ao salvar nova submissão.', 'sql' => $wpdb->last_error]);
        }

        wp_send_json_success(['message' => 'Resposta salva com sucesso.']);
    }
}


add_action('wp_ajax_alphaform_get_stats_overview', 'alphaform_get_stats_overview');
function alphaform_get_stats_overview()
{
    check_ajax_referer('alpha_form_nonce', 'nonce');

    global $wpdb;
    $table = $wpdb->prefix . 'alpha_form_responses';

    $hoje = date('Y-m-d');
    $semana = date('Y-m-d', strtotime('-7 days'));
    $mes = date('Y-m-d', strtotime('-30 days'));

    $results = [];

    // Total hoje
    $results['today'] = (int) $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM $table WHERE DATE(submitted_at) = %s",
        $hoje
    ));

    // Total semana
    $results['week'] = (int) $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM $table WHERE DATE(submitted_at) >= %s",
        $semana
    ));

    // Total mês
    $results['month'] = (int) $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM $table WHERE DATE(submitted_at) >= %s",
        $mes
    ));

    // Sessões únicas (visitas)
    $results['visits'] = (int) $wpdb->get_var(
        "SELECT COUNT(DISTINCT session_id) FROM $table"
    );

    wp_send_json_success($results);
}



add_action('wp_ajax_alpha_form_save_geo', 'alpha_form_save_geo_callback');

function alpha_form_save_geo_callback()
{
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'alpha_form_nonce')) {
        wp_send_json_error(['message' => 'Nonce inválido.']);
    }

    $session_id = sanitize_text_field($_POST['session_id'] ?? '');
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
        ['session_id' => $session_id], // condição WHERE
        [
            '%f',
            '%f',
            '%s',
            '%s',
            '%s',
            '%s'
        ],
        ['%s'] // tipo do WHERE (session_id)
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
