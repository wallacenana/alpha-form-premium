<?php
namespace AlphaFormPremium\Module\Controls;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;
use Elementor\Group_Control_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Group_Control_Border;

if (!defined('ABSPATH')) exit;

function register_form_style_label_controls(Widget_Base $widget) {
    $widget->start_controls_section(
        'style_label_section',
        [
            'label' => __('Checks Label', 'alpha-form-premium'),
            'tab' => Controls_Manager::TAB_STYLE,
        ]
    );
    $widget->add_responsive_control(
			'direcao',
			[
				'label' => esc_html__( 'Direção', 'alpha-form-premium' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'column' => [
						'title' => esc_html__( 'Hosizontal', 'alpha-form-premium' ),
						'icon' => 'eicon-editor-list-ul',
					],
					'row' => [
						'title' => esc_html__( 'Vertical', 'alpha-form-premium' ),
						'icon' => 'eicon-ellipsis-h',
					],
				],
				'default' => 'column',
				'selectors' => [
					'{{WRAPPER}} .alpha-form-input.radio' => 'flex-direction: {{VALUE}}',
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
            'label' => __('Normal', 'alpha-form-premium'),
        ]
    );

    $widget->add_control(
        'label_text_color',
        [
            'label' => __('Cor do Texto', 'alpha-form-premium'),
            'type' => Controls_Manager::COLOR,
            'global' => [
                'default' => Global_Colors::COLOR_SECONDARY,
            ],
            'selectors' => [
                '{{WRAPPER}} .alpha-form-wrapper label' => 'color: {{VALUE}};',
            ],
        ]
    );

    $widget->add_control(
        'label_background',
        [
            'label' => __('Cor de Fundo', 'alpha-form-premium'),
            'type' => Controls_Manager::COLOR,
            'global' => [
                'default' => Global_Colors::COLOR_ACCENT,
            ],
            'selectors' => [
                '{{WRAPPER}} .alpha-form-wrapper label' => 'background-color: {{VALUE}};',
            ],
        ]
    );

    $widget->end_controls_tab();

    // Aba Hover
    $widget->start_controls_tab(
        'tab_label_hover',
        [
            'label' => __('Hover', 'alpha-form-premium'),
        ]
    );

    $widget->add_control(
        'label_text_color_hover',
        [
            'label' => __('Cor do Texto', 'alpha-form-premium'),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .alpha-form-wrapper label:hover:hover' => 'color: {{VALUE}};',
            ],
        ]
    );

    $widget->add_control(
        'label_background_hover',
        [
            'label' => __('Cor de Fundo', 'alpha-form-premium'),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .alpha-form-wrapper label:hover:hover' => 'background-color: {{VALUE}};',
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
            'selector' => '{{WRAPPER}} .alpha-form-wrapper label',
        ]
    );
    

    $widget->add_responsive_control(
        'label_radius',
        [
            'label' => __('Borda Arredondada', 'alpha-form-premium'),
            'type' => Controls_Manager::DIMENSIONS,
            'selectors' => [
                '{{WRAPPER}} .alpha-form-wrapper label' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]
    );

    $widget->add_control(
        'gap',
        [
            'label' => __('Espaçamento', 'alpha-form-premium'),
            'type' => Controls_Manager::SLIDER,
            'range' => [
                'px' => ['min' => 0, 'max' => 200],
            ],
            'default' => ['size' => 10, 'unit' => 'px'],
            'selectors' => [
                '{{WRAPPER}} .alpha-form-input.radio' => 'gap: {{SIZE}}{{UNIT}};',
            ],
        ]
    );
    
    $widget->add_responsive_control(
        'radio_label_padding',
        [
            'label' => __('Espaçamento interno', 'alpha-form-premium'),
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
                '{{WRAPPER}} .alpha-form-input.radio label' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]
    );

    
    $widget->end_controls_section();
}
