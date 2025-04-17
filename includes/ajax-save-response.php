<?php
add_action('wp_ajax_alpha_form_save_response', 'alpha_form_save_response');
add_action('wp_ajax_nopriv_alpha_form_save_response', 'alpha_form_save_response');

function alpha_form_save_response() {
    global $wpdb;

    $form_id    = sanitize_text_field($_POST['form_id'] ?? '');
    $session_id = sanitize_text_field($_POST['session_id'] ?? '');
    $response   = isset($_POST['response']) ? json_decode(stripslashes($_POST['response']), true) : [];

    if (!$form_id || !$session_id || empty($response)) {
        wp_send_json_error(['message' => 'Campos obrigatórios ausentes.']);
    }

    $table = $wpdb->prefix . 'alpha_form_responses';

    // Verifica se já existe registro para essa sessão + formulário
    $existing = $wpdb->get_var($wpdb->prepare(
        "SELECT id FROM $table WHERE form_id = %s AND session_id = %s",
        $form_id, $session_id
    ));

    if ($existing) {
        // Atualiza o JSON existente
        $existing_json = $wpdb->get_var($wpdb->prepare(
            "SELECT data FROM $table WHERE id = %d", $existing
        ));

        $existing_data = json_decode($existing_json, true) ?? [];

        // Mescla nova resposta ao JSON existente
        $merged_data = array_merge($existing_data, $response);

        $updated = $wpdb->update(
            $table,
            [
                'data' => wp_json_encode($merged_data),
                'submitted_at' => current_time('mysql')
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
            'data'         => wp_json_encode($response),
            'submitted_at' => current_time('mysql'),
        ]);

        if ($inserted === false) {
            wp_send_json_error(['message' => 'Erro ao salvar nova submissão.', 'sql' => $wpdb->last_error]);
        }

        wp_send_json_success(['message' => 'Resposta salva com sucesso.']);
    }
}
