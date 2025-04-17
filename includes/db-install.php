<?php
defined('ABSPATH') || exit;

function alpha_form_create_response_table() {
    global $wpdb;

    $charset_collate = $wpdb->get_charset_collate();

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';

    $responses_table = $wpdb->prefix . 'alpha_form_responses';
    $integrations_table = $wpdb->prefix . 'alpha_form_integrations';

    // Tabela de respostas do formulário
    $sql1 = "
        CREATE TABLE $responses_table (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            form_id VARCHAR(50) NOT NULL,
            session_id VARCHAR(100) NOT NULL,
            data LONGTEXT,
            submitted_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            INDEX (form_id),
            INDEX (session_id)
        ) $charset_collate;
    ";

    // Tabela de integrações configuradas
    $sql2 = "
        CREATE TABLE $integrations_table (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            type VARCHAR(50) NOT NULL,
            settings LONGTEXT,
            status TINYINT(1) DEFAULT 1,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            INDEX (type),
            INDEX (status)
        ) $charset_collate;
    ";

    dbDelta($sql1);
    dbDelta($sql2);
}
