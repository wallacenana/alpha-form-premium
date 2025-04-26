<?php
function alphaform_send_email($form_data)
{
    $widget_id = $form_data['widget_id'];
    $post_id = $form_data['post_id'];
    $raw = get_post_meta($post_id, '_elementor_data', true);
    if (!$raw) return false;

    $data = json_decode($raw, true);
    if (!is_array($data)) return false;

    // Localiza o widget específico
    $settings = null;
    foreach ($data as $element) {
        if (!empty($element['elements'])) {
            foreach ($element['elements'] as $child) {
                if (($child['id'] ?? '') === $widget_id) {
                    $settings = $child['settings'] ?? null;
                    break 2;
                }
            }
        }
    }

    if (!$settings) return false;

    $to = $settings['email_to'] ?? '';
    $subject = $settings['email_subject'] ?? 'Nova submissão';
    $from_email = $settings['email_from'] ?? get_option('admin_email');
    $from_name = $settings['email_from_name'] ?? 'Formulário';
    $reply_to = $settings['email_reply_to'] ?? '';
    $body_raw = $settings['email_message'] ?? '[all_fields]';

    // Substitui shortcodes
    $body = $body_raw;

    if (strpos($body_raw, '[all_fields]') !== false) {
        $all = '';
        foreach ($form_data as $key => $val) {
            if (strpos($key, 'field_') === 0) {
                $label = ucfirst(str_replace('_', ' ', str_replace('field_', '', $key)));
                $value = is_array($val) ? implode(', ', $val) : $val;
                $all .= "<p><strong>$label:</strong> $value</p>";
            }
        }
        $body = str_replace('[all_fields]', $all, $body);
    }

    // Headers
    $headers = ['Content-Type: text/html; charset=UTF-8'];
    $headers[] = 'From: ' . $from_name . ' <' . $from_email . '>';
    if ($reply_to) {
        $headers[] = 'Reply-To: ' . $reply_to;
    }

    return wp_mail($to, $subject, $body, $headers);
}
