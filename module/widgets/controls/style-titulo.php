<?php

namespace AlphaFormPremium\Module\Controls;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;
use Elementor\Group_Control_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;

if (!defined('ABSPATH')) exit;

function register_form_style_titulo_controls(Widget_Base $widget)
{
    $widget->start_controls_section(
        'style_titulo_section',
        [
            'label' => __('Título', 'alpha-form-premium-main'),
            'tab' => Controls_Manager::TAB_STYLE,
        ]
    );

    $widget->add_group_control(
        Group_Control_Typography::get_type(),
        [
            'name' => 'titulo_typography',
            'selector' => '{{WRAPPER}} .alpha-form-titulo',
            'global' => [
                'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
            ],
        ]
    );

    $widget->add_control(
        'titulo_color',
        [
            'label' => __('Cor da Label', 'alpha-form-premium-main'),
            'type' => Controls_Manager::COLOR,
            'global' => [
                'default' => Global_Colors::COLOR_PRIMARY,
            ],
            'selectors' => [
                '{{WRAPPER}} .alpha-form-wrapper h3' => 'color: {{VALUE}};',
            ],
        ]
    );


    $widget->add_responsive_control(
        'alinhamento',
        [
            'label' => esc_html__('Alinhamento', 'alpha-form-premium-main'),
            'type' => Controls_Manager::CHOOSE,
            'options' => [
                'left' => [
                    'title' => esc_html__('Left', 'alpha-form-premium-main'),
                    'icon' => 'eicon-text-align-left',
                ],
                'center' => [
                    'title' => esc_html__('Center', 'alpha-form-premium-main'),
                    'icon' => 'eicon-text-align-center',
                ],
                'right' => [
                    'title' => esc_html__('Right', 'alpha-form-premium-main'),
                    'icon' => 'eicon-text-align-right',
                ],
            ],
            'default' => 'left',
            'selectors' => [
                '{{WRAPPER}} .alpha-form-wrapper h3' => 'text-align: {{VALUE}}!important',
            ],
        ]
    );

    $widget->end_controls_section();
}
