<?php

namespace AlphaFormPremium\Module\Controls;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;

require_once ALPHA_FORM_PLUGIN_PATH . 'includes/line-helped.php';
require_once ALPHA_FORM_PLUGIN_PATH . 'includes/integrations-helper.php';
require_once ALPHA_FORM_PLUGIN_PATH . 'includes/helpers/field-helper.php';
require_once ALPHA_FORM_PLUGIN_PATH . 'includes/helpers.php';
require_once ALPHA_FORM_PLUGIN_PATH . 'vendor/autoload.php';

if (!defined('ABSPATH')) exit;

function register_form_depois_do_envio_controls(Widget_Base $widget)
{
    $active_integrations = alpha_form_get_available_integrations();

    $widget->start_controls_section(
        'section_actions_after_submit',
        [
            'label' => __('Ações após envio', 'alpha-form-premium'),
            'tab' => Controls_Manager::TAB_CONTENT,
        ]
    );

    $options = [
        'store'    => 'Coletar Submissão',
        'redirect' => 'Redirecionar',
    ];

    if (afp_is_license_valid()) {
        $options['webhook'] = 'Webhook';
        $options['email'] = 'Enviar email';

        // Adiciona dinamicamente integrações ativas
        if (is_array($active_integrations)) {
            foreach ($active_integrations as $key => $settings) {
                $label = ucfirst($key);
                $options["integration_{$key}"] = "Enviar para: {$label}";
            }
        }
    }

    $widget->add_control(
        'actions',
        [
            'label' => __('Escolher Ações', 'alpha-form-premium'),
            'type' => Controls_Manager::SELECT2,
            'multiple' => true,
            'label_block' => true,
            'options' => $options,
            'default' => ['store'],
        ]
    );

    $widget->end_controls_section();

    // Sessão: Coletar Submissão
    $widget->start_controls_section(
        'section_action_store',
        [
            'label' => __('Ação: Coletar Submissão', 'alpha-form-premium'),
            'tab' => Controls_Manager::TAB_CONTENT,
            'condition' => [
                'actions' => 'store',
            ],
        ]
    );

    $widget->add_control(
        'store_message',
        [
            'type' => Controls_Manager::RAW_HTML,
            'raw' => '<p>Os dados serão armazenados na área de submissões.</p>',
        ]
    );

    $widget->end_controls_section();

    // Sessão: Webhook
    $widget->start_controls_section(
        'section_action_webhook',
        [
            'label' => __('Ação: Webhook', 'alpha-form-premium'),
            'tab' => Controls_Manager::TAB_CONTENT,
            'condition' => [
                'actions' => 'webhook',
            ],
        ]
    );

    $widget->add_control(
        'webhook_url',
        [
            'label' => __('URL do Webhook', 'alpha-form-premium'),
            'type' => Controls_Manager::TEXT,
            'placeholder' => 'https://api.exemplo.com/endpoint',
            'label_block' => true,
            'condition' => [
                'actions' => 'webhook',
            ],
            'description' => 'Disponível apenas na versão PRO do plugin.',
        ]
    );

    $widget->end_controls_section();

    // Sessão: Enviar E-mail
    $widget->start_controls_section(
        'section_action_email',
        [
            'label' => __('Ação: Enviar E-mail', 'alpha-form-premium'),
            'tab' => Controls_Manager::TAB_CONTENT,
            'condition' => [
                'actions' => 'email',
            ],
        ]
    );

    $widget->add_control('email_to', [
        'label' => __('Enviar para (To)', 'alpha-form-premium'),
        'type' => Controls_Manager::TEXT,
        'placeholder' => 'ex: contato@seudominio.com',
        'default' => '',
        'condition' => ['actions' => 'email'],
    ]);

    $widget->add_control('email_subject', [
        'label' => __('Assunto do E-mail', 'alpha-form-premium'),
        'type' => Controls_Manager::TEXT,
        'placeholder' => 'ex: Nova submissão do site',
        'default' => 'Nova submissão do formulário',
        'condition' => ['actions' => 'email'],
    ]);

    $widget->add_control('email_from', [
        'label' => __('De (From Email)', 'alpha-form-premium'),
        'type' => Controls_Manager::TEXT,
        'placeholder' => 'ex: sistema@seudominio.com',
        'default' => '',
        'condition' => ['actions' => 'email'],
    ]);

    $widget->add_control('email_from_name', [
        'label' => __('Nome do Remetente (From Name)', 'alpha-form-premium'),
        'type' => Controls_Manager::TEXT,
        'placeholder' => 'ex: Site AlphaForm',
        'default' => 'AlphaForm',
        'condition' => ['actions' => 'email'],
    ]);

    $widget->add_control('email_reply_to', [
        'label' => __('Responder para (Reply-To)', 'alpha-form-premium'),
        'type' => Controls_Manager::TEXT,
        'placeholder' => 'ex: contato@seudominio.com',
        'default' => '',
        'condition' => ['actions' => 'email'],
    ]);

    $widget->add_control('email_message', [
        'label' => __('Mensagem', 'alpha-form-premium'),
        'type' => Controls_Manager::TEXTAREA,
        'rows' => 6,
        'default' => '[all_fields]',
        'description' => 'Você pode usar shortcodes como [field id="nome"] ou [all_fields] para exibir todos os campos.',
        'condition' => ['actions' => 'email'],
    ]);

    $widget->end_controls_section();

    // Sessão: Redirecionar
    $widget->start_controls_section(
        'section_action_redirect',
        [
            'label' => __('Ação: Redirecionar', 'alpha-form-premium'),
            'tab' => Controls_Manager::TAB_CONTENT,
            'condition' => [
                'actions' => 'redirect',
            ],
        ]
    );

    $widget->add_control(
        'redirect_url',
        [
            'label' => __('URL de Redirecionamento', 'alpha-form-premium'),
            'type' => Controls_Manager::URL,
            'placeholder' => 'https://',
            'condition' => [
                'actions' => 'redirect',
            ],
        ]
    );

    $widget->end_controls_section();

    $widget->start_controls_section(
        'section_activecampaign_map_fields',
        [
            'label' => __('Ação: ActiveCampaign', 'alpha-form-premium'),
            'tab' => Controls_Manager::TAB_CONTENT,
            'condition' => [
                'actions' => 'integration_activecampaign',
            ],
        ]
    );
    // Campos ActiveCampaign

    $api_key = get_option('alpha_form_activecampaign_api_key');
    $api_url = rtrim(get_option('alpha_form_activecampaign_api_url'), '/') . '/';

    if (!$api_key || !$api_url) return;

    $client = new \GuzzleHttp\Client();

    try {
        $getlistas = $client->request('GET', $api_url . 'api/3/lists', [
            'headers' => [
                'Api-Token' => $api_key,
                'accept' => 'application/json',
            ],
        ]);

        $listas = $getlistas->getBody();
        $dataListas = json_decode($listas, true);
        $dataListasfields = isset($dataListas['lists']) ? $dataListas['lists'] : [];
    } catch (\Exception $e) {
        $dataListasfields = [];
    }

    // Transforma em array associativo para o Elementor
    $list_options = [];

    foreach ($dataListasfields as $lista) {
        if (isset($lista['id']) && isset($lista['name'])) {
            $list_options[$lista['id']] = $lista['name'];
        }
    }

    try {
        $response = $client->request('GET', $api_url . 'api/3/fields', [
            'headers' => [
                'Api-Token' => $api_key,
                'accept' => 'application/json',
            ],
        ]);
        $body = $response->getBody();
        $data = json_decode($body, true);
        $remote_fields = isset($data['fields']) ? $data['fields'] : [];
    } catch (\Exception $e) {
        $remote_fields = [];
    }

    $default_fields = [
        ['id' => 'email', 'title' => 'Email*'],
        ['id' => 'first_name', 'title' => 'Primeiro Nome'],
        ['id' => 'last_name', 'title' => 'Sobrenome'],
        ['id' => 'phone', 'title' => 'Telefone'],
        ['id' => 'organization', 'title' => 'Organização'],
    ];

    $all_fields = array_merge($default_fields, $remote_fields);

    //exibição das listas
    $widget->add_control(
        'listasExistentes',
        [
            'label' => 'Selecione a lista',
            'type' => Controls_Manager::SELECT,
            'options' => $list_options,
            'default' => '',
            'condition' => [
                'actions' => 'integration_activecampaign',
            ],
            'description' => 'Clique em "Publicar" para efetivar as alterações e usar as opções abaixo.',
        ]
    );
    $widget->add_control(
        'ajax_button',
        [
            'label' => __('Clique para receber dados', 'alpha-form-premium'),
            'type' => \Elementor\Controls_Manager::BUTTON,
            'button_type' => 'warning',
            'text' => __('Receber', 'alpha-form-premium'),
            'event' => 'alphaform:editor:send_click',
        ]
    );

    foreach ($all_fields as $field) {
        // Para campos vindos como array associativo (ActiveCampaign API)
        $field_id = $field['id'];
        $field_label = $field['title'] ?? $field['name'] ?? ('Campo ' . $field_id);

        $widget->add_control(
            'map_field_' . $field_id,
            [
                'label' => $field_label,
                'type' => \Elementor\Controls_Manager::SELECT,
                'options' => [], // Será preenchido via JS
                'default' => '',
                'condition' => [
                    'actions' => 'integration_activecampaign',
                ],
            ]
        );
    }

    $widget->end_controls_section();

    $widget->start_controls_section(
        'section_mailchimp_map_fields',
        [
            'label' => __('Ação: Mailchimp', 'alpha-form-premium'),
            'tab' => Controls_Manager::TAB_CONTENT,
            'condition' => [
                'actions' => 'integration_mailchimp',
            ],
        ]
    );

    $widget->add_control(
        'ajax_button_mc',
        [
            'label' => __('Clique para receber dados', 'alpha-form-premium'),
            'type' => \Elementor\Controls_Manager::BUTTON,
            'button_type' => 'warning',
            'text' => __('Receber', 'alpha-form-premium'),
            'event' => 'alphaform:editor:send_click',
        ]
    );

    // ✅ Dados da API Mailchimp
    $api_key = get_option('alpha_form_mailchimp_api_key');

    if (!$api_key) {
        $widget->add_control(
            'mailchimp_alert',
            [
                'type' => \Elementor\Controls_Manager::RAW_HTML,
                'raw' => '<strong>⚠️ Configure sua API Key e Audience ID do Mailchimp nas configurações do plugin.</strong>',
                'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning',
            ]
        );
        $widget->end_controls_section();
        return;
    }

    $merge_fields = [
        ['tag' => 'FNAME', 'name' => 'Primeiro Nome'],
        ['tag' => 'LNAME', 'name' => 'Último Nome'],
        ['tag' => 'PHONE', 'name' => 'Telefone'],
        ['tag' => 'EMAIL', 'name' => 'Email*'],
    ];

    // 👇 Exibe dropdown de campo local para cada campo do Mailchimp
    foreach ($merge_fields as $field) {
        $field_tag = strtolower($field['tag']); // fname, lname...
        $widget->add_control(
            'map_field_' . $field_tag . '_mc',
            [
                'label' => $field['name'],
                'type' => \Elementor\Controls_Manager::SELECT,
                'options' => [], // será preenchido via JS
                'default' => '',
                'condition' => [
                    'actions' => 'integration_mailchimp',
                ],
            ]
        );
    }

    $widget->end_controls_section();
}
