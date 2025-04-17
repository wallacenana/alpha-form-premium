<?php

namespace AlphaFormPremium\Module\Controls;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;
use Elementor\Group_Control_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;

if (!defined('ABSPATH')) exit;

function register_style_input_controls(Widget_Base $widget)
{
    $widget->start_controls_section(
        'style_input_section',
        [
            'label' => __('Campos de Entrada', 'alpha-form-premium'),
            'tab' => Controls_Manager::TAB_STYLE,
        ]
    );

    $widget->add_group_control(
        Group_Control_Typography::get_type(),
        [
            'name' => 'input_typography',
            'selector' => '{{WRAPPER}} .alpha-form-input',
            'global' => [
                'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
            ],
        ]
    );

    $widget->add_control(
        'input_text_color',
        [
            'label' => __('Cor do Texto', 'alpha-form-premium'),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .alpha-form-input:not(.radio):not(.checkbox)' => 'color: {{VALUE}};',
            ],
        ]
    );

    $widget->add_control(
        'input_background',
        [
            'label' => __('Cor de Fundo', 'alpha-form-premium'),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .alpha-form-input:not(.radio):not(.checkbox)' => 'background-color: {{VALUE}};',
            ],
        ]
    );

    $widget->add_group_control(
        \Elementor\Group_Control_Border::get_type(),
        [
            'name' => 'input_border',
            'selector' => '{{WRAPPER}} .alpha-form-input:not(.radio):not(.checkbox)',
        ]
    );

    $widget->add_responsive_control(
        'input_padding',
        [
            'label' => __('Espaçamento interno', 'alpha-form-premium'),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%', 'em'],
            'default' => [
                'top' => 15,
                'right' => 25,
                'bottom' => 15,
                'left' => 25,
                'unit' => 'px',
            ],
            'selectors' => [
                '{{WRAPPER}} .alpha-form-input' =>
                'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]
    );

    $widget->add_responsive_control(
        'input_radius',
        [
            'label' => __('Arredondamento', 'alpha-form-premium'),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%'],
            'selectors' => [
                '{{WRAPPER}} .alpha-form-input:not(.radio):not(.checkbox)' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]
    );
    $widget->add_responsive_control(
        'alinhamento_input',
        [
            'label' => esc_html__('Alinhamento', 'alpha-form-premium'),
            'type' => Controls_Manager::CHOOSE,
            'options' => [
                'left' => [
                    'title' => esc_html__('Left', 'alpha-form-premium'),
                    'icon' => 'eicon-text-align-left',
                ],
                'center' => [
                    'title' => esc_html__('Center', 'alpha-form-premium'),
                    'icon' => 'eicon-text-align-center',
                ],
                'right' => [
                    'title' => esc_html__('Right', 'alpha-form-premium'),
                    'icon' => 'eicon-text-align-right',
                ],
            ],
            'default' => 'left',
            'selectors' => [
                '{{WRAPPER}} .alpha-form-input:not(.radio):not(.checkbox)' => 'text-align: {{VALUE}}!important',
            ],
        ]
    );

    $widget->end_controls_section();
}
