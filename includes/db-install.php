<?php
defined('ABSPATH') || exit;

function alpha_form_create_response_table()
{
    global $wpdb;

    $charset_collate = $wpdb->get_charset_collate();
    require_once ABSPATH . 'wp-admin/includes/upgrade.php';

    $responses_table = $wpdb->prefix . 'alpha_form_responses';
    $integrations_table = $wpdb->prefix . 'alpha_form_integrations';

    $sql1 = "
        CREATE TABLE $responses_table (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            form_id VARCHAR(50) NOT NULL,
            session_id VARCHAR(100) NOT NULL,
            data LONGTEXT,
            widget_id VARCHAR(50) NOT NULL,
            postId BIGINT UNSIGNED NOT NULL,
            duration INT DEFAULT NULL,
            lang VARCHAR(10) DEFAULT NULL,
            platform VARCHAR(50) DEFAULT NULL,
            device_type VARCHAR(20) DEFAULT NULL,
            timezone VARCHAR(50) DEFAULT NULL,
            user_agent TEXT DEFAULT NULL,
            ip_address VARCHAR(45) DEFAULT NULL,
            latitude DECIMAL(10, 6) DEFAULT NULL,
            longitude DECIMAL(10, 6) DEFAULT NULL,
            city VARCHAR(128),
            state VARCHAR(128),
            country VARCHAR(128),
            country_code CHAR(2),
            browser VARCHAR(100) DEFAULT NULL,
            concluido TINYINT(1) DEFAULT 0,
            submited TINYINT(1) DEFAULT 0,
            page_view TINYINT(1) DEFAULT 0,
            start_form TINYINT(1) DEFAULT 0,
            submitted_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY session_id (session_id)
        ) $charset_collate;
    ";

    $sql2 = "
        CREATE TABLE $integrations_table (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            type VARCHAR(50) NOT NULL,
            settings LONGTEXT,
            status TINYINT(1) DEFAULT 1,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY type (type),
            KEY status (status)
        ) $charset_collate;
    ";

    dbDelta($sql1);
    dbDelta($sql2);
}
