<?php

function alphaform_get_repeater_fields_from_db($widget_id, $post_id)
{
    $post_id = get_the_ID(); // usa o post atual, sem parâmetro externo

    $raw_data = get_post_meta($post_id, '_elementor_data', true);
    if (empty($raw_data)) return [];

    $widgets = json_decode($raw_data, true);
    if (!is_array($widgets)) return [];

    $results = [];

    foreach ($widgets as $element) {
        if (!empty($element['elements']) && is_array($element['elements'])) {
            foreach ($element['elements'] as $child) {
                if (isset($child['id']) && $child['id'] === $widget_id) {
                    $repeater_items = $child['settings']['form_fields'] ?? [];

                    foreach ($repeater_items as $item) {
                        $id = $item['_id'] ?? null;
                        $label = $item['field_label'] ?? null;
                        if ($id && $label) {
                            $results[$id] = $label;
                        }
                    }
                    return $results; // retorna assim que encontrar
                }
            }
        }
    }

    return $results;
}

function alphaform_map_labels_from_widget($post_id, $widget_id) {
    $raw = get_post_meta($post_id, '_elementor_data', true);
    if (!$raw) return [];

    $data = json_decode($raw, true);
    if (!is_array($data)) return [];

    foreach ($data as $el) {
        if (!empty($el['elements']) && is_array($el['elements'])) {
            foreach ($el['elements'] as $child) {
                if (($child['id'] ?? '') === $widget_id) {
                    $fields = $child['settings']['form_fields'] ?? [];
                    $labels = [];
                    foreach ($fields as $field) {
                        if (!empty($field['_id']) && !empty($field['field_label'])) {
                            $labels['field_' . $field['_id']] = $field['field_label'];
                        }
                    }
                    return $labels;
                }
            }
        }
    }

    return [];
}

function alphaform_formatar_dados_para_webhook($post_id, $widget_id, $form_data) {
    $labels = alphaform_map_labels_from_widget($post_id, $widget_id); // já existe
    $dados_legiveis = [];

    foreach ($form_data as $chave => $valor) {
        if (strpos($chave, 'field_') === 0) {
            $custom_id = str_replace('field_', '', $chave);
            $label = $labels[$chave] ?? $custom_id;

            $dados_legiveis[$label] = $valor;
        }
    }

    return $dados_legiveis;
}
