<?php

namespace AlphaFormPremium\Module\Controls;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;

if (!defined('ABSPATH')) exit;

function register_form_visualizacoes_controls(Widget_Base $widget)
{

    $widget->start_controls_section(
        'section_form_view',
        [
            'label' => __('Vizualizações', 'alpha-form-premium-main'),
            'tab' => Controls_Manager::TAB_CONTENT,
        ]
    );

    $widget->add_control(
        'controles',
        [
            'label' => __('Controles', 'alpha-form-premium-main'),
            'type' => Controls_Manager::SWITCHER,
            'label_on' => __('Sim', 'alpha-form-premium-main'),
            'label_off' => __('Não', 'alpha-form-premium-main'),
            'return_value' => 'yes',
            'default' => 'yes',
        ]
    );

    $widget->add_control(
        'porcentagem',
        [
            'label' => __('Porcentagem', 'alpha-form-premium-main'),
            'type' => Controls_Manager::SWITCHER,
            'label_on' => __('Sim', 'alpha-form-premium-main'),
            'label_off' => __('Não', 'alpha-form-premium-main'),
            'return_value' => 'yes',
            'default' => 'yes',
        ]
    );

    $widget->add_control(
        'text_auxiliar',
        [
            'label' => __('Texto Auxiliar', 'alpha-form-premium-main'),
            'type' => Controls_Manager::TEXT,
            'default' => 'Escolha uma opção',
            'placeholder' => __('Escolha uma opção', 'alpha-form-premium-main'),
            'label_block' => true,
        ]
    );

    $widget->end_controls_section();
}
