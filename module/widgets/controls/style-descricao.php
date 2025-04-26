<?php

namespace AlphaFormPremium\Module\Controls;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;
use Elementor\Group_Control_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;

if (!defined('ABSPATH')) exit;

function register_style_descricao_controls(Widget_Base $widget)
{
    $widget->start_controls_section(
        'style_description_section',
        [
            'label' => __('Descrição', 'alpha-form-premium'),
            'tab' => Controls_Manager::TAB_STYLE,
        ]
    );

    $widget->add_group_control(
        Group_Control_Typography::get_type(),
        [
            'name' => 'description_typography',
            'selector' => '{{WRAPPER}} .alpha-form-description',
            'global' => [
                'default' => Global_Typography::TYPOGRAPHY_TEXT,
            ],
        ]
    );

    $widget->add_control(
        'description_color',
        [
            'label' => __('Cor da Descrição', 'alpha-form-premium'),
            'type' => Controls_Manager::COLOR,
            'global' => [
                'default' => Global_Colors::COLOR_SECONDARY,
            ],
            'selectors' => [
                '{{WRAPPER}} .alpha-form-description' => 'color: {{VALUE}};',
            ],
        ]
    );

    $widget->add_responsive_control(
        'alinhamento_descricao',
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
                '{{WRAPPER}} .alpha-form-description' => 'text-align: {{VALUE}}!important',
            ],
        ]
    );
    $widget->end_controls_section();
}
