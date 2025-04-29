<?php

namespace AlphaFormPremium\Module\Controls;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;

if (!defined('ABSPATH')) exit;

function register_style_botao_controls(Widget_Base $widget)
{
    $widget->start_controls_section(
        'style_button_section',
        [
            'label' => __('Botões', 'alpha-form-premium'),
            'tab' => Controls_Manager::TAB_STYLE,
        ]
    );

    $widget->add_group_control(
        Group_Control_Typography::get_type(),
        [
            'name' => 'button_typography',
            'global' => [
                'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
            ],
            'selector' => '{{WRAPPER}} .alpha-form-submit, {{WRAPPER}} .alpha-form-next-button',
        ]
    );

    // Tabs de estilo: Normal e Hover
    $widget->start_controls_tabs('tabs_button_styles');

    // Aba Normal
    $widget->start_controls_tab(
        'tab_button_normal',
        [
            'label' => __('Normal', 'alpha-form-premium'),
        ]
    );

    $widget->add_control(
        'button_text_color',
        [
            'label' => __('Cor do Texto', 'alpha-form-premium'),
            'type' => Controls_Manager::COLOR,
            'global' => [
                'default' => Global_Colors::COLOR_SECONDARY,
            ],
            'selectors' => [
                '{{WRAPPER}} .alpha-form-submit, {{WRAPPER}} .alpha-form-next-button' => 'color: {{VALUE}};',
            ],
        ]
    );

    $widget->add_control(
        'button_background',
        [
            'label' => __('Cor de Fundo', 'alpha-form-premium'),
            'type' => Controls_Manager::COLOR,
            'global' => [
                'default' => Global_Colors::COLOR_ACCENT,
            ],
            'selectors' => [
                '{{WRAPPER}} .alpha-form-submit, {{WRAPPER}} .alpha-form-next-button' => 'background-color: {{VALUE}};',
            ],
        ]
    );

    $widget->end_controls_tab();

    // Aba Hover
    $widget->start_controls_tab(
        'tab_button_hover',
        [
            'label' => __('Hover', 'alpha-form-premium'),
        ]
    );

    $widget->add_control(
        'button_text_color_hover',
        [
            'label' => __('Cor do Texto', 'alpha-form-premium'),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .alpha-form-submit:hover, {{WRAPPER}} .alpha-form-next-button:hover' => 'color: {{VALUE}};',
            ],
        ]
    );

    $widget->add_control(
        'button_background_hover',
        [
            'label' => __('Cor de Fundo', 'alpha-form-premium'),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .alpha-form-submit:hover, {{WRAPPER}} .alpha-form-next-button:hover' => 'background-color: {{VALUE}};',
            ],
        ]
    );

    $widget->end_controls_tab();

    $widget->end_controls_tabs(); // Fim das tabs Normal/Hover

    // Controles gerais
    $widget->add_control(
        'button_border_style',
        [
            'label' => __('Borda do Botão', 'alpha-form-premium'),
            'type' => Controls_Manager::SELECT,
            'default' => 'none',
            'options' => [
                'none' => 'Nenhuma',
                'solid' => 'Sólida',
                'dashed' => 'Tracejada',
                'dotted' => 'Pontilhada',
            ],
            'selectors' => [
                '{{WRAPPER}} .alpha-form-submit, {{WRAPPER}} .alpha-form-next-button' => 'border-style: {{VALUE}};',
            ],
        ]
    );

    // Controles gerais
    $widget->add_group_control(
        Group_Control_Border::get_type(),
        [
            'name' => 'button_border',
            'selector' => '{{WRAPPER}} .alpha-form-wrapper label',
        ]
    );
    $widget->add_responsive_control(
        'button_radius',
        [
            'label' => __('Borda Arredondada', 'alpha-form-premium'),
            'type' => Controls_Manager::DIMENSIONS,
            'default' => [
                'top' => 4,
                'right' => 4,
                'bottom' => 4,
                'left' => 4,
                'unit' => 'px',
            ],
            'selectors' => [
                '{{WRAPPER}} .alpha-form-submit, {{WRAPPER}} .alpha-form-next-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]
    );

    $widget->add_responsive_control(
        'button_padding_x',
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
                '{{WRAPPER}} .alpha-form-next-button, {{WRAPPER}} .alpha-form-submit' =>
                'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]
    );


    $widget->add_responsive_control(
        'button_margin_x',
        [
            'label' => __('Espaço da margem', 'alpha-form-premium'),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%', 'em'],
            'default' => [
                'top' => 0,
                'right' => 0,
                'bottom' => 0,
                'left' => 0,
                'unit' => 'px',
            ],
            'selectors' => [
                '{{WRAPPER}} .alpha-form-next-button, {{WRAPPER}} .alpha-form-submit' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]
    );

    $widget->add_responsive_control(
        'alinhamento_btn',
        [
            'label' => esc_html__('Alinhamento', 'alpha-form-premium'),
            'type' => Controls_Manager::CHOOSE,
            'options' => [
                '' => [
                    'title' => esc_html__('Left', 'alpha-form-premium'),
                    'icon' => 'eicon-text-align-left',
                ],
                'margin: 0 auto' => [
                    'title' => esc_html__('Center', 'alpha-form-premium'),
                    'icon' => 'eicon-text-align-center',
                ],
                'margin-left: auto' => [
                    'title' => esc_html__('Right', 'alpha-form-premium'),
                    'icon' => 'eicon-text-align-right',
                ],
            ],
            'default' => 'left',
            'selectors' => [
                '{{WRAPPER}} .alpha-form-submit, {{WRAPPER}} .alpha-form-next-button' => '{{VALUE}}!important',
            ],
        ]
    );

    $widget->end_controls_section();
}
