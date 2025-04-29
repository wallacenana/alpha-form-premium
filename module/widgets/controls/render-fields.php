<?php

namespace AlphaFormPremium\Module\Controls;

if (!defined('ABSPATH')) exit;


function render_alpha_form_fields($settings, $widget_id)
{
    echo '<div class="alpha-form-wrapper">';

    $form_name = $settings['form_name'] ?? 'formulario-alpha';

    $datashortcode = $settings['redirect_url']['url'] ?? '';

    echo '<form class="alpha-form" data-widget-id="' . esc_attr($widget_id) . '" novalidate data-form-id="' . esc_attr($form_name) . '" data-redirect="' . esc_attr($datashortcode) . '">';

    if (!empty($settings['form_fields'])) {
        foreach ($settings['form_fields'] as $i => $field) {
            $type        = $field['field_type'] ?? 'text';
            $label       = $field['field_label'] ?? '';
            $placeholder = $field['field_placeholder'] ?? '';
            $id = $field['_id'] ?? 'field_' . $i;
            $shortcode = $field['custom_id'];
            $class       = $field['afp_class'] ?? '';
            $required    = ($field['required'] === 'yes') ? 'required' : '';
            $default     = $field['field_value'] ?? '';
            $pattern     = $field['field_pattern'] ?? '';
            $show_required = $settings['show_required_mark'] === 'yes';
            $btn_text    = $field['next_button_text'] ?? '';
            $step_class  = 'alpha-form-step' . ($i === 0 ? ' active' : '');
            $special_masks = ['cpf', 'cnpj', 'cep', 'currency', 'credit_card'];

            $mask = in_array($type, $special_masks) ? ' data-mask="' . esc_attr($type) . '"' : '';


            $requiredMark = $show_required && esc_html($required) ? '<span style="color:red">*</span>' : '';

            echo '<div class="alpha-form-field ' . esc_attr($step_class) . '">';
            echo wp_kses_post('<h3 class="alpha-form-titulo">' . esc_html($label) . $requiredMark . '</h3>');
            if (!empty($field['field_descricao'])) {
                echo '<div class="alpha-form-description">' . wp_kses_post($field['field_descricao']) . '</div>';
            }

            switch ($type) {
                case 'intro':
                    echo '<textarea id="' . esc_attr($id) . '" name="' . esc_attr($id) . '" data-shortcode="' .
                        esc_attr($shortcode) . '" class="alpha-form-input ' . esc_attr($class) . '" placeholder="' .
                        esc_attr($placeholder) . '" ' . esc_attr($required) . ' autofocus rows="5">';
                    echo esc_html($default);
                    echo '</textarea>';
                    break;

                case 'select':
                    echo '<div class="alpha-form-input select">';
                    echo '<select id="' . esc_attr($id) . '" name="' . esc_attr($id) . '" class="' . esc_attr($class) . '" ' . esc_attr($required) . '>';
                    echo '<option value="" disabled selected hidden>Selecione uma opção</option>';
                    $options = explode("\n", $field['field_options'] ?? '');
                    foreach ($options as $opt) {
                        $opt = trim($opt);
                        if (!$opt) continue;
                        if (str_contains($opt, '|')) {
                            [$label_option, $value_option] = array_map('trim', explode('|', $opt, 2));
                        } else {
                            $label_option = $value_option = $opt;
                        }
                        echo '<option value="' . esc_attr($value_option) . '">' . esc_html($label_option) . '</option>';
                    }
                    echo '</select>
                        </div>';
                    if ($settings['text_auxiliar'])
                        echo '<div class="alpha-text-auxiliar">' . esc_html($settings['text_auxiliar']) . '</div>';
                    break;

                case 'intro':
                    break;

                case 'acceptance':
                    $text = $field['acceptance_text'] ?? 'Li e aceito a política de privacidade.';
                    echo '<div class="alpha-form-input acceptance' . esc_attr($class) . '">';
                    echo '<label class="acceptance"><input type="checkbox" name="' . esc_attr($id) . '" ' . esc_attr($required) . '> ' . esc_html($text) . '</label>';
                    echo '</div>';
                    break;

                case 'hidden':
                    echo '<input type="hidden" id="' . esc_attr($id) . '" name="' . esc_attr($id) . '"  data-shortcode="' . esc_attr($shortcode) . '" value="' . esc_attr($default) . '">';
                    break;

                case 'radio':
                    $options = explode("\n", $field['field_options'] ?? '');
                    $style   = $settings['radio_icon_style'] ?? 'abc';
                    $show_hint = $field['key-hint'] === 'yes';
                    echo '<div class="alpha-form-input radio" data-style="' . esc_attr($style) . '">';
                    foreach ($options as $index => $opt) {
                        $opt = trim($opt);
                        if (!$opt) continue;
                        if (str_contains($opt, '|')) {
                            [$label_option, $value_option] = array_map('trim', explode('|', $opt, 2));
                        } else {
                            $label_option = $value_option = $opt;
                        }
                        $input_id = esc_attr($id . '_' . $index);
                        $letter = chr(65 + $index); // A, B, C...
                        echo '<input type="radio" class="toggle" id="' . esc_attr($input_id) . '" name="' . esc_attr($id) . '" value="' . esc_attr($value_option) . '" ' . esc_attr($required) . '>';
                        $label_attrs = '';
                        if ($show_hint) {
                            $label_attrs .= ' data-letter=' . $letter . '';
                            $label_attrs .= ' data-icon=✓';
                        }
                        echo '<label for="' . esc_attr($input_id) . '"' . esc_attr($label_attrs) . '>' . esc_html($label_option) . '</label>';
                    }
                    echo '</div>';
                    if ($settings['text_auxiliar'])
                        echo '<div class="alpha-text-auxiliar">' . esc_html($settings['text_auxiliar']) . '</div>';
                    break;

                case 'checkbox':
                    $options = explode("\n", $field['field_options'] ?? '');
                    $style   = $settings['checkbox_icon_style'] ?? 'abc';
                    $show_hint = $field['key-hint'] === 'yes';
                    echo '<div class="alpha-form-input checkbox" data-style="' . esc_attr($style) . '">';
                    foreach ($options as $index => $opt) {
                        $opt = trim($opt);
                        if (!$opt) continue;
                        if (str_contains($opt, '|')) {
                            [$label_option, $value_option] = array_map('trim', explode('|', $opt, 2));
                        } else {
                            $label_option = $value_option = $opt;
                        }
                        $input_id = esc_attr($id . '_' . $index);
                        $letter = chr(65 + $index); // A, B, C...
                        echo '<input type="checkbox" class="toggle" id="' . esc_attr($input_id) . '" name="' . esc_attr($id) . '" value="' . esc_attr($value_option) . '" ' . esc_attr($required) . '>';
                        $label_attrs = '';
                        if ($show_hint) {
                            $label_attrs .= ' data-letter=' . $letter . '';
                            $label_attrs .= ' data-icon=✓';
                        }
                        echo '<label for="' . esc_attr($input_id) . '"' . esc_attr($label_attrs) . '>' . esc_html($label_option) . '</label>';
                    }
                    echo '</div>';
                    if ($settings['text_auxiliar'])
                        echo '<div class="alpha-text-auxiliar">' . esc_html($settings['text_auxiliar']) . '</div>';
                    break;

                default:
                    echo '<input type="' . esc_attr($type) . '" id="' . esc_attr($id) . '" name="' . esc_attr($id) . '"  data-shortcode="' . esc_attr($shortcode) . '" class="alpha-form-input' . esc_attr($class) . '" placeholder="' . esc_attr($placeholder) . '" value="' . esc_attr($default) . '" pattern="' . esc_attr($pattern) . '" ' . esc_attr($required) . esc_attr($mask) . ' autofocus>';
                    break;
            }

            if (!in_array($type, ['select', 'radio', 'acceptance']) && $btn_text) {
                echo '<button type="button" class="alpha-form-next-button">' . esc_html($btn_text) . '</button>';
            }

            echo '</div>';
        }
    }

    // Botão final
    $btn_text  = $settings['button_text'] ?? 'Enviar';
    $btn_size  = $settings['button_size'] ?? 'medium';
    $btn_width = $settings['button_width_percent'] ?? '100';
    $btn_id    = $settings['button_id'] ?? '';
    $btn_icon  = $settings['button_icon']['value'] ?? '';
    $btnvalue  = $settings['btn_value'] ?? '';
    $style = 'width: ' . esc_attr($btn_width) . '%;';
    $class = 'alpha-form-submit alpha-btn-size-' . esc_attr($btn_size);

    echo '<div class="alpha-form-field alpha-form-step">';
    echo '<h3 class="alpha-form-titulo">' . esc_html($btnvalue) . '</h3>';
    echo '<p class="alpha-form-description">' . esc_html($settings['btn_descricao']) . '</p>';
    echo '<button type="submit" id="' . esc_attr($btn_id) . '" class="' . esc_attr($class) . '" style="' . esc_attr($style) . '">';
    if (!empty($btn_icon)) {
        echo '<i class="' . esc_attr($btn_icon) . '" style="margin-right: 5px;"></i>';
    }
    echo esc_html($btn_text) . '</button>';
    echo '</div>';

    // Barra de progresso e controles
    $show_controls = $settings['controles'] === 'yes';
    $show_percentage = $settings['porcentagem'] === 'yes';

    echo '<div class="alpha-form-progress">';
    if ($show_percentage) {
        echo '<div class="alpha-form-progress-container">
                <div class="alpha-form-progress-wrapper">
                    <div class="alpha-form-progress-text">0%</div>
                    <div class="alpha-form-progress-bar">
                        <div class="alpha-form-progress-fill"></div>
                    </div>
                </div>
              </div>';
    }

    if ($show_controls) {
        echo '<div class="alpha-form-controls">';

        if (!empty($settings['icon_prev']['value'])) {
            \Elementor\Icons_Manager::render_icon($settings['icon_prev'], ['aria-hidden' => 'true']);
        } else {
            echo '<i class="fas fa-chevron-left"></i>';
        }

        echo '<button type="button" class="alpha-form-progress-button alpha-form-prev-button-x" aria-label="Voltar">';
        \Elementor\Icons_Manager::render_icon($settings['icon_prev'], ['aria-hidden' => 'true']);
        echo '</button>';

        echo '<button type="button" class="alpha-form-progress-button alpha-form-next-button-x" aria-label="Próximo">';
        \Elementor\Icons_Manager::render_icon($settings['icon_next'], ['aria-hidden' => 'true']);
        echo '</button>';

        echo '</div>';
    }


    echo '</div>'; // .alpha-form-progress
    echo '</form>';
    echo '</div>'; // .alpha-form-wrapper
    echo '<div id="alphaform-overlay" style="display:none;">
            <div class="alphaform-loader-box">
                <img src="' . esc_url(ALPHA_FORM_PLUGIN_URL . 'assets/img/alphaform-loader-bcb992ad.gif') . '" alt="Carregando..." width="30">
                </div>
            </div>';

    if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
        echo '<style>.alpha-form-field { opacity: 1 !important; visibility: visible !important; margin-bottom: 20px!important; position: relative !important; transform: none !important; height: auto!important; margin-bottom: 50px !important }</style>';
    }
}
