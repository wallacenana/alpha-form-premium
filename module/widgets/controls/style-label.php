<?php

namespace AlphaFormPremium\Module\Controls;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;
use Elementor\Group_Control_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Group_Control_Border;

if (!defined('ABSPATH')) exit;

function register_form_style_label_controls(Widget_Base $widget)
{
    $widget->start_controls_section(
        'style_label_section',
        [
            'label' => __('Checks Label', 'alpha-form-premium-main'),
            'tab' => Controls_Manager::TAB_STYLE,
        ]
    );
    $widget->add_responsive_control(
        'direcao',
        [
            'label' => esc_html__('Direção', 'alpha-form-premium-main'),
            'type' => Controls_Manager::CHOOSE,
            'options' => [
                'column' => [
                    'title' => esc_html__('Hosizontal', 'alpha-form-premium-main'),
                    'icon' => 'eicon-editor-list-ul',
                ],
                'row' => [
                    'title' => esc_html__('Vertical', 'alpha-form-premium-main'),
                    'icon' => 'eicon-ellipsis-h',
                ],
            ],
            'default' => 'column',
            'selectors' => [
                '{{WRAPPER}} .alpha-form-input.radio, {{WRAPPER}} .alpha-form-input.checkbox' => 'flex-direction: {{VALUE}}',
            ],
        ]
    );
    $widget->add_group_control(
        Group_Control_Typography::get_type(),
        [
            'name' => 'label_typography',
            'selector' => '{{WRAPPER}} .alpha-form-wrapper label',
            'global' => [
                'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
            ],
        ]
    );

    // Tabs de estilo: Normal e Hover
    $widget->start_controls_tabs('tabs_label_styles');

    // Aba Normal
    $widget->start_controls_tab(
        'tab_label_normal',
        [
            'label' => __('Normal', 'alpha-form-premium-main'),
        ]
    );

    $widget->add_control(
        'label_text_color',
        [
            'label' => __('Cor do Texto', 'alpha-form-premium-main'),
            'type' => Controls_Manager::COLOR,
            'global' => [
                'default' => Global_Colors::COLOR_SECONDARY,
            ],
            'selectors' => [
                '{{WRAPPER}} .alpha-form-wrapper label, {{WRAPPER}} .alpha-form-input.select select' => 'color: {{VALUE}};'
            ],
        ]
    );

    $widget->add_control(
        'label_background',
        [
            'label' => __('Cor de Fundo', 'alpha-form-premium-main'),
            'type' => Controls_Manager::COLOR,
            'global' => [
                'default' => Global_Colors::COLOR_ACCENT,
            ],
            'selectors' => [
                '{{WRAPPER}} .alpha-form-input.radio label, {{WRAPPER}} .alpha-form-input.checkbox label, {{WRAPPER}} .alpha-form-input.select select' => 'background-color: {{VALUE}};',
            ],
        ]
    );
    $widget->add_responsive_control(
        'label_width',
        [
            'label' => __('Largura do Label', 'alpha-form-premium-main'),
            'type' => Controls_Manager::SLIDER,
            'size_units' => ['%', 'px', 'em', 'rem'],
            'default' => [
                'unit' => '%',
                'size' => 100,
            ],
            'range' => [
                '%' => [
                    'min' => 10,
                    'max' => 100,
                ],
                'px' => [
                    'min' => 50,
                    'max' => 1000,
                ],
                'em' => [
                    'min' => 1,
                    'max' => 20,
                ],
                'rem' => [
                    'min' => 1,
                    'max' => 20,
                ],
            ],
            'selectors' => [
                '{{WRAPPER}} .alpha-form-input.checkbox, {{WRAPPER}} .alpha-form-input.checkbox label,
                {{WRAPPER}} .alpha-form-input.radio, {{WRAPPER}} .alpha-form-input.radio label, 
                {{WRAPPER}} .alpha-form-input.select select' => 'width: {{SIZE}}{{UNIT}};',
            ],
        ]
    );


    $widget->end_controls_tab();

    // Aba Hover
    $widget->start_controls_tab(
        'tab_label_hover',
        [
            'label' => __('Hover', 'alpha-form-premium-main'),
        ]
    );

    $widget->add_control(
        'label_text_color_hover',
        [
            'label' => __('Cor do Texto', 'alpha-form-premium-main'),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .alpha-form-wrapper label:not(.acceptance):hover' => 'color: {{VALUE}};',
            ],
        ]
    );

    $widget->add_control(
        'label_background_hover',
        [
            'label' => __('Cor de Fundo', 'alpha-form-premium-main'),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .alpha-form-input.radio label:hover, {{WRAPPER}} .alpha-form-input.checkbox label:hover' => 'background-color: {{VALUE}};',
            ],
        ]
    );

    $widget->end_controls_tab();

    $widget->end_controls_tabs(); // Fim das tabs Normal/Hover

    // Controles gerais
    $widget->add_group_control(
        Group_Control_Border::get_type(),
        [
            'name' => 'label_border',
            'selector' => '{{WRAPPER}} .alpha-form-input.radio label, {{WRAPPER}} .alpha-form-input.checkbox label, {{WRAPPER}} .alpha-form-input.select select, {{WRAPPER}} .alpha-form-input option',
        ]
    );


    $widget->add_responsive_control(
        'label_radius',
        [
            'label' => __('Borda Arredondada', 'alpha-form-premium-main'),
            'type' => Controls_Manager::DIMENSIONS,
            'selectors' => [
                '{{WRAPPER}} .alpha-form-wrapper label, {{WRAPPER}} .alpha-form-input.select select, {{WRAPPER}} .alpha-form-input option' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]
    );

    $widget->add_control(
        'gap',
        [
            'label' => __('Espaçamento', 'alpha-form-premium-main'),
            'type' => Controls_Manager::SLIDER,
            'range' => [
                'px' => ['min' => 0, 'max' => 200],
            ],
            'default' => ['size' => 10, 'unit' => 'px'],
            'selectors' => [
                '{{WRAPPER}} .alpha-form-input.radio, {{WRAPPER}} .alpha-form-input.checkbox' => 'gap: {{SIZE}}{{UNIT}};',
            ],
        ]
    );

    $widget->add_responsive_control(
        'radio_label_padding',
        [
            'label' => __('Espaçamento interno', 'alpha-form-premium-main'),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%', 'em'],
            'default' => [
                'top' => 15,
                'right' => 30,
                'bottom' => 15,
                'left' => 30,
                'unit' => 'px',
            ],
            'selectors' => [
                '{{WRAPPER}} .alpha-form-input.radio label,{{WRAPPER}} .alpha-form-input.checkbox label, {{WRAPPER}} .alpha-form-input.select select, {{WRAPPER}} .alpha-form-input option' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]
    );


    $widget->end_controls_section();

    $widget->start_controls_section(
        'section_radio_style',
        [
            'label' => __('Key hint', 'alpha-form-premium-main'),
            'tab' => Controls_Manager::TAB_STYLE,
        ]
    );

    // Cor da borda
    $widget->add_control(
        'radio_border_color',
        [
            'label' => __('Cor da Borda', 'alpha-form-premium-main'),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .alpha-form-input.radio[data-style="abc"] label::before, {{WRAPPER}} .alpha-form-input.checkbox[data-style="abc"] label::before' => 'border-color: {{VALUE}};',
            ],
        ]
    );
    $widget->add_control(
        'radio_color',
        [
            'label' => __('Cor do texto', 'alpha-form-premium-main'),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .alpha-form-input.radio[data-style="abc"] label::before, {{WRAPPER}} .alpha-form-input.checkbox[data-style="abc"] label::before' => 'color: {{VALUE}};',
            ],
        ]
    );

    // Cor de fundo
    $widget->add_control(
        'radio_background_color',
        [
            'label' => __('Cor de Fundo', 'alpha-form-premium-main'),
            'type' => Controls_Manager::COLOR,
            'default' => 'transparent',
            'selectors' => [
                '{{WRAPPER}} .alpha-form-input.radio[data-style="abc"] label::before, {{WRAPPER}} .alpha-form-input.checkbox[data-style="abc"] label::before' => 'background-color: {{VALUE}};',
            ],
        ]
    );

    // Tamanho do hint
    $widget->add_responsive_control(
        'radio_hint_size',
        [
            'label' => __('Tamanho do Indicador', 'alpha-form-premium-main'),
            'type' => Controls_Manager::SLIDER,
            'range' => [
                'px' => [
                    'min' => 5,
                    'max' => 50,
                ],
            ],
            'selectors' => [
                '{{WRAPPER}} .alpha-form-input.radio[data-style="abc"] label::before, {{WRAPPER}} .alpha-form-input.checkbox[data-style="abc"] label::before' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
            ],
        ]
    );


    // Arredondamento (Border Radius)
    $widget->add_responsive_control(
        'radio_hint_border_radius',
        [
            'label' => __('Borda Arredondada', 'alpha-form-premium-main'),
            'type' => Controls_Manager::SLIDER,
            'range' => [
                'px' => [
                    'min' => 0,
                    'max' => 50,
                ],
            ],
            'selectors' => [
                '{{WRAPPER}} .alpha-form-input.radio[data-style="abc"] label::before, {{WRAPPER}} .alpha-form-input.checkbox[data-style="abc"] label::before' => 'border-radius: {{SIZE}}{{UNIT}};',
            ],
        ]
    );

    $widget->add_responsive_control(
        'radio_hint_position_left',
        [
            'label' => __('Posição (Esquerda)', 'alpha-form-premium-main'),
            'type' => \Elementor\Controls_Manager::SLIDER,
            'size_units' => ['px', '%', 'em', 'rem'],
            'range' => [
                'px' => [
                    'min' => -50,
                    'max' => 1000,
                ],
                '%' => [
                    'min' => -50,
                    'max' => 100,
                ],
                'em' => [
                    'min' => -5,
                    'max' => 50,
                ],
                'rem' => [
                    'min' => -5,
                    'max' => 50,
                ],
            ],
            'default' => [
                'unit' => 'px',
                'size' => -10,
            ],
            'selectors' => [
                '{{WRAPPER}} .alpha-form-input.radio[data-style="abc"] label::before, {{WRAPPER}} .alpha-form-input.checkbox[data-style="abc"] label::before' => 'left: {{SIZE}}{{UNIT}};',
            ],
        ]
    );


    $widget->end_controls_section();

    $widget->start_controls_section(
        'section_text_auxiliar_style',
        [
            'label' => __('Texto Auxiliar', 'alpha-form-premium-main'),
            'tab' => Controls_Manager::TAB_STYLE,
        ]
    );

    // Cor do texto auxiliar
    $widget->add_control(
        'text_auxiliar_color',
        [
            'label' => __('Cor do Texto', 'alpha-form-premium-main'),
            'type' => Controls_Manager::COLOR,
            'global' => [
                'default' => Global_Colors::COLOR_TEXT,
            ],
            'selectors' => [
                '{{WRAPPER}} .alpha-text-auxiliar' => 'color: {{VALUE}};',
            ],
        ]
    );

    // Tipografia do texto auxiliar
    $widget->add_group_control(
        Group_Control_Typography::get_type(),
        [
            'name' => 'text_auxiliar_typography',
            'global' => [
                'default' => Global_Typography::TYPOGRAPHY_TEXT,
            ],
            'selector' => '{{WRAPPER}} .alpha-text-auxiliar',
        ]
    );

    $widget->end_controls_section();
}
