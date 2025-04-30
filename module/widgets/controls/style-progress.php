<?php
namespace AlphaFormPremium\Module\Controls;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;

if (!defined('ABSPATH')) exit;

function register_style_progress_controls(Widget_Base $widget) {
    $widget->start_controls_section(
        'style_progress_section',
        [
            'label' => __('Barra de Progresso', 'alpha-form-premium-main'),
            'tab' => Controls_Manager::TAB_STYLE,
        ]
    );

    $widget->add_group_control(
        Group_Control_Typography::get_type(),
        [
            'name' => 'progress_text_typography',
            'selector' => '{{WRAPPER}} .alpha-form-progress-text',
            'global' => [
                'default' => Global_Typography::TYPOGRAPHY_TEXT,
            ],
        ]
    );
    
    // Texto da porcentagem
    $widget->add_control(
        'progress_text_color',
        [
            'label' => __('Cor do Texto', 'alpha-form-premium-main'),
            'type' => Controls_Manager::COLOR,
            'global' => [
                'default' => Global_Colors::COLOR_PRIMARY,
            ],
            'selectors' => [
                '{{WRAPPER}} .alpha-form-progress-text' => 'color: {{VALUE}};',
            ],
        ]
    );

    // Cor da barra de fundo
    $widget->add_control(
        'progress_background_color',
        [
            'label' => __('Cor da Barra de Fundo', 'alpha-form-premium-main'),
            'type' => Controls_Manager::COLOR,
            'global' => [
                'default' => Global_Colors::COLOR_SECONDARY,
            ],
            'selectors' => [
                '{{WRAPPER}} .alpha-form-progress-bar' => 'background-color: {{VALUE}};',
            ],
        ]
    );

    
    $widget->add_responsive_control(
        'progress_height',
        [
            'label' => __('Altura da Barra', 'alpha-form-premium-main'),
            'type' => Controls_Manager::SLIDER,
            'range' => [
                'px' => [
                    'min' => 1,
                    'max' => 100,
                ],
            ],
            'default' => [
                'size' => 8,
                'unit' => 'px',
            ],
            'selectors' => [
                '{{WRAPPER}} .alpha-form-progress-bar, {{WRAPPER}} .alpha-form-progress-fill' => 'height: {{SIZE}}{{UNIT}} !important;',
            ],
        ]
    );
    
    $widget->add_control(
        'progress_background_color_total',
        [
            'label' => __('Cor do Fundo', 'alpha-form-premium-main'),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .alpha-form-progress' => 'background-color: {{VALUE}};',
            ],
        ]
    );
    $widget->add_control(
        'progress_background_color_fill',
        [
            'label' => __('Cor do progresso', 'alpha-form-premium-main'),
            'type' => Controls_Manager::COLOR,
            'default' => Global_Colors::COLOR_ACCENT, // Accent como valor padrão
            'selectors' => [
                '{{WRAPPER}} .alpha-form-progress-fill' => 'background-color: {{VALUE}};',
            ],
        ]
    );
    
    
    $widget->add_responsive_control(
        'progress_radius',
        [
            'label' => __('Borda Arredondada', 'alpha-form-premium-main'),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%'],
            'selectors' => [
                '{{WRAPPER}} .alpha-form-progress-bar, {{WRAPPER}} .alpha-form-progress-fill' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]
    );
    
    $widget->add_responsive_control(
        'progress_width',
        [
            'label' => __('Largura da Barra', 'alpha-form-premium-main'),
            'type' => Controls_Manager::SLIDER,
            'range' => [
                '%' => [
                    'min' => 10,
                    'max' => 100,
                ],
                'px' => [
                    'min' => 100,
                    'max' => 1000,
                ],
            ],
            'default' => [
                'size' => 20,
                'unit' => '%',
            ],
            'selectors' => [
                '{{WRAPPER}} .alpha-form-progress' => 'width: {{SIZE}}{{UNIT}};',
            ],
        ]
    );

    $widget->add_control(
        'prograss_border_color_x',
        [
            'label' => __('Cor da Borda', 'alpha-form-premium-main'),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
            '{{WRAPPER}} .alpha-form-progress' => 'border-color: {{VALUE}};',
            ],
        ]
    );
    
    $widget->add_control(
        'progress_position_section_title',
        [
            'label' => __('Posição da Barra (X / Y)', 'alpha-form-premium-main'),
            'type' => Controls_Manager::HEADING,
            'separator' => 'before',
        ]
    );
    
    $widget->add_group_control(
        Group_Control_Border::get_type(),
        [
            'name' => 'prograss_border',
            'selector' => '{{WRAPPER}} .alpha-form-progress',
        ]
    );
    
    $widget->add_responsive_control(
        'progress_bar_radius',
        [
            'label' => __('Borda Arredondada', 'alpha-form-premium-main'),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%'],
            'selectors' => [
                '{{WRAPPER}} .alpha-form-progress, {{WRAPPER}} .alpha-form-progress-fill' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]
    );
    
    $widget->add_responsive_control(
        'prograss__padding',
        [
            'label' => __('Padding', 'alpha-form-premium-main'),
            'type' => Controls_Manager::DIMENSIONS,
            'selectors' => [
                '{{WRAPPER}} .alpha-form-progress' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]
    );
    
    // Eixo X (horizontal)
    $widget->add_responsive_control(
        'progress_pos_x',
        [
            'label' => __('Horizontal (X)', 'alpha-form-premium-main'),
            'type' => Controls_Manager::SLIDER,
            'size_units' => ['%'],
            'range' => [
                '%' => [
                    'min' => 0,
                    'max' => 100,
                    'step' => 1,
                ],
            ],
            'default' => [
                'unit' => '%',
                'size' => 10,
            ],
            'selectors' => [
                '{{WRAPPER}} .alpha-form-progress' => 'right: {{SIZE}}{{UNIT}};',
            ],
        ]
    );
    
    // Eixo Y (vertical)
    $widget->add_responsive_control(
        'progress_pos_y',
        [
            'label' => __('Vertical (Y)', 'alpha-form-premium-main'),
            'type' => Controls_Manager::SLIDER,
            'size_units' => ['%'],
            'range' => [
                '%' => [
                    'min' => 0,
                    'max' => 100,
                    'step' => 1,
                ],
            ],
            'default' => [
                'unit' => '%',
                'size' => 2,
            ],
            'selectors' => [
                '{{WRAPPER}} .alpha-form-progress' => 'bottom: {{SIZE}}{{UNIT}};',
            ],
        ]
    );
    


    $widget->end_controls_section();
}
