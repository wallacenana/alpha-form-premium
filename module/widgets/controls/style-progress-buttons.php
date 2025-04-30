<?php
namespace AlphaFormPremium\Module\Controls;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;
use Elementor\Group_Control_Border;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;

if (!defined('ABSPATH')) exit;

function register_style_progress_buttons_controls(Widget_Base $widget) {
    $widget->start_controls_section(
        'style_progress_buttons',
        [
            'label' => __('Botões de Progresso', 'alpha-form-premium-main'),
            'tab' => Controls_Manager::TAB_STYLE,
        ]
    );
    
    $widget->add_control(
        'progress_button_icon_size',
        [
            'label' => __('Tamanho do Ícone', 'alpha-form-premium-main'),
            'type' => Controls_Manager::SLIDER,
            'range' => [
                'px' => ['min' => 10, 'max' => 60],
            ],
            'default' => ['size' => 10, 'unit' => 'px'],
            'selectors' => [
                '{{WRAPPER}} .alpha-form-progress-button i, {{WRAPPER}} .alpha-form-progress-button svg' => 'width: {{SIZE}}{{UNIT}};',
            ],
        ]
    );
    
    // Tabs de estilo: Normal e Hover
    $widget->start_controls_tabs('tabs_button_progress_styles');

    // Aba Normal
    $widget->start_controls_tab(
        'tab_button_progress_normal',
        [
            'label' => __('Normal', 'alpha-form-premium-main'),
        ]
    );

    $widget->add_control(
        'button_progress_text_color',
        [
            'label' => __('Cor do Icone', 'alpha-form-premium-main'),
            'type' => Controls_Manager::COLOR,
            'global' => [
                'default' => Global_Colors::COLOR_SECONDARY,
            ],
            'selectors' => [
                '{{WRAPPER}} .alpha-form-progress-button i, {{WRAPPER}} .alpha-form-progress-button svg' => 'fill: {{VALUE}};',
            ],
        ]
    );

    $widget->add_control(
        'button_progress_background',
        [
            'label' => __('Cor de Fundo', 'alpha-form-premium-main'),
            'type' => Controls_Manager::COLOR,
            'global' => [
                'default' => Global_Colors::COLOR_ACCENT,
            ],
            'selectors' => [
                ' {{WRAPPER}} .alpha-form-progress-button' => 'background-color: {{VALUE}};',
            ],
        ]
    );

    $widget->end_controls_tab();

    // Aba Hover
    $widget->start_controls_tab(
        'tab_button_progress_hover',
        [
            'label' => __('Hover', 'alpha-form-premium-main'),
        ]
    );

    $widget->add_control(
        'button_progress_text_color_hover',
        [
            'label' => __('Cor do icone', 'alpha-form-premium-main'),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .alpha-form-progress-button:hover i, {{WRAPPER}} .alpha-form-progress-button:hover svg' => 'fill: {{VALUE}}!important;',
            ],
        ]
    );

    $widget->add_control(
        'button_progress_background_hover',
        [
            'label' => __('Cor de Fundo', 'alpha-form-premium-main'),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .alpha-form-submit:hover, {{WRAPPER}} .alpha-form-progress-button:hover' => 'background-color: {{VALUE}};',
            ],
        ]
    );

    $widget->end_controls_tab();

    $widget->end_controls_tabs(); // Fim das tabs Normal/Hover
    
    $widget->add_group_control(
        Group_Control_Border::get_type(),
        [
            'name' => 'progress_button_border',
            'selector' => '{{WRAPPER}} .alpha-form-progress-button',
            'fields_options' => [
                'border' => [
                    'default' => 'none',
                ],
            ],
        ]
    );

    $widget->add_responsive_control(
        'progress_button_radius',
        [
            'label' => __('Borda Arredondada', 'alpha-form-premium-main'),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%'],
            'selectors' => [
                '{{WRAPPER}} .alpha-form-progress-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]
    );
    
    $widget->add_control(
        'prograss_button_border_color_x',
        [
            'label' => __('Cor da Borda', 'alpha-form-premium-main'),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
            '{{WRAPPER}} .alpha-form-progress-button' => 'border-color: {{VALUE}};',
            ],
        ]
    );
    
    
    $widget->add_responsive_control(
        'prograss_button_padding',
        [
            'label' => __('Padding', 'alpha-form-premium-main'),
            'type' => Controls_Manager::DIMENSIONS,
            'selectors' => [
                '{{WRAPPER}} .alpha-form-progress-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]
    );
    
    $widget->add_responsive_control(
        'progress_button_margin_x',
        [
            'label' => __('Espaço da margem', 'alpha-form-premium-main'),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%', 'em'],
            'selectors' => [
                ' {{WRAPPER}} .alpha-form-progress-button' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]
    );
    $widget->end_controls_section();
}
