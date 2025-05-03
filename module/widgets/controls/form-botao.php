<?php

namespace AlphaFormPremium\Module\Controls;

use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Widget_Base;

if (!defined('ABSPATH')) exit;

function register_form_botao_controls(Widget_Base $widget)
{

    $widget->start_controls_section(
        'section_submit_button',
        [
            'label' => __('Botões', 'alpha-form-premium-main'),
            'tab' => Controls_Manager::TAB_CONTENT,
        ]
    );

    $widget->add_control(
        'button_text',
        [
            'label' => __('Texto do Botão', 'alpha-form-premium-main'),
            'label_block' => true,
            'type' => Controls_Manager::TEXT,
            'default' => 'Enviar',
        ]
    );

    $widget->add_control(
        'btn_value',
        [
            'label' => __('Titulo', 'alpha-form-premium-main'),
            'type' => Controls_Manager::TEXT,
            'label_block' => true,
            'default' => 'Formulário concluído',
        ]
    );
    $widget->add_control(
        'btn_descricao',
        [
            'label' => __('Descrição', 'alpha-form-premium-main'),
            'type' => Controls_Manager::TEXTAREA,
            'default' => 'Parabéns pelo seu cadastro',
        ]
    );

    $widget->add_control(
        'button_width_percent',
        [
            'label' => __('Largura do Botão (%)', 'alpha-form-premium-main'),
            'type' => Controls_Manager::SELECT,
            'options' => [
                ''  => 'Auto',
                '15'  => '15%',
                '20'  => '20%',
                '25'  => '25%',
                '30'  => '30%',
                '35'  => '35%',
                '40'  => '40%',
                '45'  => '45%',
                '50'  => '50%',
                '55'  => '55%',
                '60'  => '60%',
                '70'  => '70%',
                '75'  => '75%',
                '80'  => '80%',
                '85'  => '85%',
                '90'  => '90%',
                '95'  => '95%',
                '100' => '100%',
            ],
            'default' => 'Auto',
        ]
    );

    $widget->add_control(
        'button_icon',
        [
            'label' => __('Ícone do Botão', 'alpha-form-premium-main'),
            'type' => Controls_Manager::ICONS,
            'default' => [
                'value' => '',
                'library' => '',
            ],
        ]
    );

    $widget->add_control(
        'button_id',
        [
            'label' => __('ID do Botão', 'alpha-form-premium-main'),
            'type' => Controls_Manager::TEXT,
            'placeholder' => 'ex: enviar_btn_1',
            'description' => 'Certifique-se de que o ID seja único e não usado em outra parte da página. Este campo permite letras (A-z), números (0-9) e underline, sem espaços.',
        ]
    );


    // Ícone "Voltar"
    $widget->add_control(
        'icon_prev',
        [
            'label' => __('Ícone de Voltar', 'alpha-form-premium-main'),
            'type' => Controls_Manager::ICONS,
            'default' => [
                'value' => 'fas fa-chevron-left',
                'library' => 'fa-solid',
            ],
        ]
    );

    // Ícone "Avançar"
    $widget->add_control(
        'icon_next',
        [
            'label' => __('Ícone de Avançar', 'alpha-form-premium-main'),
            'type' => Controls_Manager::ICONS,
            'default' => [
                'value' => 'fas fa-chevron-right',
                'library' => 'fa-solid',
            ],
        ]
    );

    $widget->end_controls_section();
}
