<?php

namespace AlphaFormPremium\Module\Controls;

use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Widget_Base;

if (!defined('ABSPATH')) exit;

function register_form_fields_controls(Widget_Base $widget)
{

    $widget->start_controls_section(
        'section_form_fields',
        [
            'label' => __('Campos do Formulário', 'alpha-form-premium'),
            'tab' => Controls_Manager::TAB_CONTENT,
        ]
    );
    $widget->add_control(
        'form_name',
        [
            'label' => __('Nome do Formulário', 'alpha-form-premium'),
            'type' => Controls_Manager::TEXT,
            'placeholder' => 'Formulário Alpha',
            'default' => 'Formulário Alpha',
            'input_attrs' => [
                'maxlength' => 40,
            ],
            'description' => 'Nome usado para identificar o formulário. Máximo de 50 caracteres.',
        ]
    );

    $repeater = new Repeater();

    $repeater->add_control(
        'field_type',
        [
            'label' => __('Tipo do Campo', 'alpha-form-premium'),
            'type' => Controls_Manager::SELECT,
            'options' => [
                'text'      => 'Texto',
                'email'     => 'Email',
                'textarea'  => 'Área de Texto',
                'tel'       => 'Telefone',
                'url'       => 'URL',
                'number'    => 'Número',
                'password'  => 'Senha',
                'radio'     => 'Escolha Única',
                'checkbox'  => 'Múltiplas Escolhas',
                'select'    => 'Select',
                'date'      => 'Data',
                'time'      => 'Hora',
                'hidden'    => 'Oculto',
                'acceptance' => 'Aceitação',
                'intro'     => 'Texto simples',
                'cpf'       => 'CPF',
                'cnpj'      => 'CNPJ',
                'cep'       => 'CEP',
                'currency'  => 'Moeda',
            ],
            'default' => 'text',
        ]
    );

    $repeater->start_controls_tabs('form_fields_tabs');

    $repeater->start_controls_tab('form_fields_conteudo_tab', [
        'label' => esc_html__('Conteúdo', 'alpha-form-premium'),
    ]);


    $repeater->add_control(
        'acceptance_text',
        [
            'label' => __('Texto da Aceitação', 'alpha-form-premium'),
            'type' => Controls_Manager::TEXTAREA,
            'default' => 'Li e aceito a política de privacidade.',
            'condition' => [
                'field_type' => 'acceptance',
            ],
        ]
    );
    $repeater->add_control(
        'field_label',
        [
            'label' => __('Rótulo', 'alpha-form-premium'),
            'type' => Controls_Manager::TEXT,
            'default' => 'Item',
        ]
    );
    $repeater->add_control(
        'field_descricao',
        [
            'label' => __('Descrição', 'alpha-form-premium'),
            'type' => Controls_Manager::WYSIWYG,
            'default' => '',
            'condition' => [
                'field_type!' => ['hidden'],
            ],
        ]
    );


    $repeater->add_control(
        'field_placeholder',
        [
            'label' => __('Placeholder', 'alpha-form-premium'),
            'type' => Controls_Manager::TEXT,
            'default' => '',
            'condition' => [
                'field_type!' => ['select', 'radio', 'checkbox', 'date', 'time', 'intro', 'hidden'],
            ],
        ]
    );


    $repeater->add_control(
        'field_options',
        [
            'label' => __('Opções (uma por linha)', 'alpha-form-premium'),
            'type' => Controls_Manager::TEXTAREA,
            'rows' => 5,
            'condition' => [
                'field_type' => ['select', 'radio', 'checkbox'],
            ],
            'description' => __('Insira cada opção em uma linha separada. Para diferenciar entre rótulo e valor, separe-os com um caractere de barra vertical ("|"). Por exemplo: First Name|f_name', 'alpha-form-premium'),
        ]
    );


    $repeater->add_control(
        'next_button_text',
        [
            'label' => __('Texto do botão', 'alpha-form-premium'),
            'type' => Controls_Manager::TEXT,
            'default' => 'Ok',
            'placeholder' => 'ex: Continuar',
            'condition' => [
                'field_type' => ['text', 'email', 'textarea', 'tel', 'url', 'number', 'password', 'date', 'time', 'intro', 'checkbox', 'cpf', 'cnpj', 'cep', 'currency'],
            ],
        ]
    );

    $repeater->add_control(
        'key-hint',
        [
            'label' => __('Mostrar marcadores', 'alpha-form-premium'),
            'type' => Controls_Manager::SWITCHER,
            'label_on' => __('Sim', 'alpha-form-premium'),
            'label_off' => __('Não', 'alpha-form-premium'),
            'return_value' => 'yes',
            'default' => 'yes',
            'condition' => [
                'field_type' => ['radio', 'checkbox'],
            ],
        ]
    );

    $repeater->add_control(
        'required',
        [
            'label' => __('Obrigatório', 'alpha-form-premium'),
            'type' => Controls_Manager::SWITCHER,
            'label_on' => __('Sim', 'alpha-form-premium'),
            'label_off' => __('Não', 'alpha-form-premium'),
            'return_value' => 'yes',
            'default' => '',
            'condition' => [
                'field_type' => [
                    'text',
                    'email',
                    'tel',
                    'textarea',
                    'select',
                    'radio',
                    'checkbox',
                    'date',
                    'cpf',
                    'cnpj',
                    'cep',
                    'currency'
                ],
            ],
        ]
    );

    $repeater->end_controls_tab();

    $repeater->start_controls_tab(
        'form_fields_advanced_tab',
        [
            'label' => esc_html__('Avançado', 'alpha-form-premium'),
            'condition' => [
                'field_type!' => 'html',
            ],
        ]
    );
    $repeater->add_control(
        'field_value',
        [
            'label' => __('Valor Padrão', 'alpha-form-premium'),
            'type' => Controls_Manager::TEXT,
            'default' => '',
            'condition' => [
                'field_type' => ['text', 'email', 'textarea', 'tel', 'url', 'number', 'password', 'hidden', 'text', 'cpf', 'cnpj', 'cep', 'currency'],
            ],
        ]
    );

    $repeater->add_control(
        'field_pattern',
        [
            'label' => __('Padrão (Pattern)', 'alpha-form-premium'),
            'type' => Controls_Manager::TEXT,
            'description' => __('Regex ou padrão de validação HTML5. Ex: [0-9]{3}-[0-9]{2}', 'alpha-form-premium'),
            'condition' => [
                'field_type' => ['text', 'email', 'password'],
            ],
        ]
    );

    $repeater->add_control(
        'custom_id',
        [
            'label' => esc_html__('ID', 'alpha-form-premium'),
            'type' => Controls_Manager::TEXT,
            'description' => sprintf(
                esc_html__('Certifique-se de que o ID seja único e não usado em outra parte da página. Este campo permite letras (A-z), números (0-9) e underline, sem espaços.', 'alpha-form-premium'),
                '<code>',
                '</code>'
            ),
            'render_type' => 'none',
            'required' => true,
            'dynamic' => [
                'active' => true,
            ],
            'ai' => [
                'active' => false,
            ],
        ]
    );

    $repeater->end_controls_tab();
    $repeater->end_controls_tabs();

    $widget->add_control(
        'input_size_hr',
        [
            'type' => Controls_Manager::DIVIDER,
        ]
    );

    $widget->add_control(
        'show_required_mark',
        [
            'label' => __('Marcar obrigatórios', 'alpha-form-premium'),
            'type' => Controls_Manager::SWITCHER,
            'label_on' => __('Sim', 'alpha-form-premium'),
            'label_off' => __('Não', 'alpha-form-premium'),
            'return_value' => 'yes',
            'default' => 'yes',
        ]
    );

    $widget->add_control(
        'form_fields',
        [
            'label' => __('Campos', 'alpha-form-premium'),
            'type' => Controls_Manager::REPEATER,
            'fields' => $repeater->get_controls(),
            'default' => [
                [
                    'custom_id' => 'field_introducao',
                    'field_type' => 'intro',
                    'field_label' => 'Inicio do seu formulário',
                    'field_descricao' => 'Este é o seu texto de introdução',
                    'next_button_text' => 'Iniciar formulário',
                ],
                [
                    'custom_id' => 'field_nome',
                    'field_type' => 'text',
                    'field_label' => 'Qual o seu nome?',
                    'field_placeholder' => 'Digite aqui',
                ],
                [
                    'custom_id' => 'field_email',
                    'field_type' => 'email',
                    'field_label' => 'Qual o seu email?',
                    'field_placeholder' => 'Digite aqui',
                    'required' => 'yes',
                ],
            ],
            'title_field' => '{{ field_label }}',
        ]
    );
    $widget->end_controls_section();
}
