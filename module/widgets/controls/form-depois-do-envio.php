<?php

namespace AlphaFormPremium\Module\Controls;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;

require_once ALPHA_FORM_PLUGIN_PATH . 'includes/line-helped.php';
require_once ALPHA_FORM_PLUGIN_PATH . 'includes/integrations-helper.php';
require_once ALPHA_FORM_PLUGIN_PATH . 'includes/field-helper.php';
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
        'email'    => 'Enviar email',
        'redirect' => 'Redirecionar',
    ];

    if (afp_is_license_valid()) {
        $options['webhook'] = 'Webhook';

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
            'label' => __('[Ação] Coletar Submissão', 'alpha-form-premium'),
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

    // 1. Ação do Widget - controles no Elementor
    $widget->start_controls_section(
        'section_webhook_settings',
        [
            'label' => __('[Ação] Webhook', 'alpha-form-premium'),
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
            'label_block' => true,
            'placeholder' => __('https://sua-api.com/webhook', 'alpha-form-premium'),
            'default' => '',
        ]
    );

    $widget->end_controls_section();

    // Sessão: Enviar E-mail
    $widget->start_controls_section(
        'section_action_email',
        [
            'label' => __('[Ação] Enviar E-mail', 'alpha-form-premium'),
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
            'label' => __('[Ação] Redirecionar', 'alpha-form-premium'),
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
            'label' => __('[Ação] ActiveCampaign', 'alpha-form-premium'),
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
        ]
    );
    $widget->add_control(
        'active_alert',
        [
            'type' => Controls_Manager::RAW_HTML,
            'raw' => '<strong>⚠️ Clique em "publicar" antes de clicar no botão e usar as opções abaixo.</strong>',
            'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning',
        ]
    );

    $widget->add_control(
        'ajax_button',
        [
            'label' => __('Clique para receber dados', 'alpha-form-premium'),
            'type' => Controls_Manager::BUTTON,
            'button_type' => 'success',
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
                'type' => Controls_Manager::SELECT,
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
            'label' => __('[Ação] Mailchimp', 'alpha-form-premium'),
            'tab' => Controls_Manager::TAB_CONTENT,
            'condition' => [
                'actions' => 'integration_mailchimp',
            ],
        ]
    );

    if (!$api_key) {
        $widget->add_control(
            'mailchimp_alert',
            [
                'type' => Controls_Manager::RAW_HTML,
                'raw' => '<strong>⚠️ Configure sua API Key e Audience ID do Mailchimp nas configurações do plugin.</strong>',
                'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning',
            ]
        );
        $widget->end_controls_section();
        return;
    }

    $widget->add_control(
        'mailchimp_alert2',
        [
            'type' => Controls_Manager::RAW_HTML,
            'raw' => '<strong>⚠️ Clique em "publicar" antes de clicar no botão e usar as opções abaixo.</strong>',
            'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning',
        ]
    );

    $widget->add_control(
        'ajax_button_mc',
        [
            'label' => __('Clique para receber dados', 'alpha-form-premium'),
            'type' => Controls_Manager::BUTTON,
            'button_type' => 'success',
            'text' => __('Receber', 'alpha-form-premium'),
            'event' => 'alphaform:editor:send_click',
        ]
    );

    $api_key = get_option('alpha_form_mailchimp_api_key');

    if (!$api_key) return;

    // Extrai o prefixo automaticamente da API key
    $server_prefix = explode('-', $api_key)[1] ?? '';

    if (!$server_prefix) return;

    $mailchimp  = new \MailchimpMarketing\ApiClient();
    $mailchimp->setConfig([
        'apiKey' => $api_key,
        'server' => $server_prefix,
    ]);

    // Busca listas (audiências)
    try {
        $responsex = $mailchimp->lists->getAllLists();
        $response_array = json_decode(json_encode($responsex), true);
        $listas = $response_array['lists'] ?? [];
    } catch (\Exception $e) {
        $listas = [];
    }

    $list_options = [];

    foreach ($listas as $lista) {
        if (isset($lista['id']) && isset($lista['name'])) {
            $list_options[$lista['id']] = $lista['name'];
        }
    }

    $widget->add_control(
        'mailchimp_list_id',
        [
            'label' => __('Selecione a audiência (lista)', 'alpha-form-premium'),
            'type' => Controls_Manager::SELECT,
            'options' => $list_options,
            'default' => '',
            'condition' => [
                'actions' => 'integration_mailchimp',
            ],
            'description' => 'Clique em "Publicar" para atualizar os campos da audiência selecionada.',
        ]
    );

    // Campos padrão + merge_fields
    $default_fields = [
        ['id' => 'email_address', 'title' => 'Email*'],
        ['id' => 'FNAME', 'title' => 'Primeiro Nome'],
        ['id' => 'LNAME', 'title' => 'Último Nome'],
    ];

    $merge_fields = [];
    try {
        if (!empty($list_options)) {
            // Pega o primeiro da lista como exemplo
            $first_list_id = array_key_first($list_options);
            $mergeResponse = $mailchimp->lists->getListMergeFields($first_list_id);
            $mergeFieldsArray = json_decode(json_encode($mergeResponse), true);
            foreach ($mergeFieldsArray['merge_fields'] ?? [] as $field) {
                $merge_fields[] = [
                    'id' => $field['tag'],
                    'title' => $field['name'],
                ];
            }
        }
    } catch (\Exception $e) {
        $merge_fields = [];
    }

    $all_fields = array_merge($default_fields, $merge_fields);

    // Mapeia os campos do formulário para os campos do Mailchimp
    foreach ($all_fields as $field) {
        $field_id = $field['id'];
        $field_label = $field['title'] ?? $field['id'];

        $widget->add_control(
            'map_field_' . $field_id . '_mc',
            [
                'label' => $field_label,
                'type' => Controls_Manager::SELECT,
                'options' => [], // Populado via JS
                'default' => '',
                'condition' => [
                    'actions' => 'integration_mailchimp',
                ],
            ]
        );
    }

    $widget->end_controls_section();
}
