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
