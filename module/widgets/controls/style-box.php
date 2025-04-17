<?php

namespace AlphaFormPremium\Module\Controls;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;
use Elementor\Group_Control_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;

if (!defined('ABSPATH')) exit;

function register_style_box_controls(Widget_Base $widget)
{
    $widget->start_controls_section(
        'style_box_section',
        [
            'label' => __('Caixa geral', 'alpha-form-premium'),
            'tab' => Controls_Manager::TAB_STYLE,
        ]
    );

    $widget->add_control(
        'box_background',
        [
            'label' => __('Cor de Fundo', 'alpha-form-premium'),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                '.alpha-form-wrapper' => 'background-color: {{VALUE}};',
            ],
        ]
    );

    $widget->add_group_control(
        \Elementor\Group_Control_Border::get_type(),
        [
            'name' => 'box_border',
            'selector' => '.alpha-form-wrapper',
        ]
    );

    $widget->add_responsive_control(
        'box_padding',
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
                '.alpha-form-wrapper' =>
                'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]
    );

    $widget->add_responsive_control(
        'box_radius',
        [
            'label' => __('Arredondamento', 'alpha-form-premium'),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%'],
            'selectors' => [
                '.alpha-form-wrapper' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]
    );

    $widget->add_responsive_control(
        'box_gap',
        [
            'label' => __('Espaço entre itens', 'alpha-form-premium'),
            'type' => Controls_Manager::SLIDER,
            'size_units' => ['px', '%', 'em'],
            'range' => [
                'px' => ['min' => 0, 'max' => 100],
                '%'  => ['min' => 0, 'max' => 100],
                'em' => ['min' => 0, 'max' => 10],
            ],
            'default' => [
                'size' => 15,
                'unit' => 'px',
            ],
            'selectors' => [
                '{{WRAPPER}} form' => 'gap: {{SIZE}}{{UNIT}};',
            ],
        ]
    );

    $widget->end_controls_section();
}
