<?php
defined('ABSPATH') || exit;
require_once plugin_dir_path(__DIR__) . 'includes/line-helped.php';
require_once plugin_dir_path(__DIR__, 2) . 'includes/integrations-helper.php';

if (!afp_is_license_valid()) {
    echo '<div class="alpha-form-license-screen">
        <div class="alpha-form-license-card">
            <h1 class="alpha-form-license-title">Ative sua licença Premium</h1>
            <p class="alpha-form-license-subtitle">
                Seus formulários ganham <strong>mais poder, controle e profissionalismo</strong> com o <strong>Alpha Form Premium</strong>.
                Ative sua licença para desbloquear todas as integrações e recursos avançados.
            </p>
            <ul class="alpha-form-license-benefits">
                <li>🔗 Integração com CRMs e e-mails (Mailchimp, ActiveCampaign, HubSpot...)</li>
                <li>📊 Envio de respostas para Google Sheets</li>
                <li>📩 Disparos automáticos de e-mail</li>
                <li>🚀 Automatização via webhooks personalizados</li>
                <li>🔐 Painel completo e suporte dedicado</li>
            </ul>
            <a href="https://alphaform.com.br/investimento" target="_blank" class="alpha-form-license-button">
                💎 Comprar o Alpha Form Premium
            </a>
            <p class="alpha-form-license-note">
                Já possui uma chave? Vá em <strong>Alpha Form → Licença</strong> e ative sua licença.
            </p>
        </div>
    </div>';
    return;
}

// 🧠 Configuração das integrações
$integrations = [
    'mailchimp' => [
        'label' => 'Mailchimp',
        'doc'   => 'https://kb.mailchimp.com/integrations/api-integrations/about-api-keys',
        'fields' => [
            'api_key' => ['label' => 'API Key', 'type' => 'secret'],
        ],
        'validate' => function ($data) {
            require_once plugin_dir_path(__DIR__, 2) . 'vendor/autoload.php';
            $api_key = $data['api_key'];
            $server = substr($api_key, strpos($api_key, '-') + 1);

            $client = new \MailchimpMarketing\ApiClient();
            $client->setConfig([
                'apiKey' => $api_key,
                'server' => $server,
            ]);
            $response = $client->ping->get();

            if (isset($response->health_status) && $response->health_status === "Everything's Chimpy!") {
                alpha_form_save_integration('mailchimp', ['api_key' => $api_key]);
                return ['success' => true, 'message' => '✅ API Key validada com sucesso!'];
            }

            return ['success' => false, 'message' => '❌ Resposta inesperada da API do Mailchimp.'];
        }
    ],
    'activecampaign' => [
        'label' => 'ActiveCampaign',
        'doc'   => 'https://help.activecampaign.com/hc/pt-br/articles/207317590-Onde-posso-encontrar-minha-chave-API-e-URL-API-',
        'fields' => [
            'api_url' => ['label' => 'URL da API', 'type' => 'text'],
            'api_key' => ['label' => 'API Key', 'type' => 'secret'],
        ],
        'validate' => function ($data) {
            $api_url = trailingslashit(esc_url_raw($data['api_url'])) . 'api/3/users';
            $api_key = $data['api_key'];

            $response = wp_remote_get($api_url, [
                'headers' => [
                    'Api-Token' => $api_key,
                ],
                'timeout' => 10,
            ]);

            if (is_wp_error($response)) {
                return ['success' => false, 'message' => '❌ Erro na requisição: ' . $response->get_error_message()];
            }

            $code = wp_remote_retrieve_response_code($response);
            if ($code === 200) {
                alpha_form_save_integration('activecampaign', [
                    'api_url' => esc_url_raw($data['api_url']),
                    'api_key' => $api_key
                ]);
                return ['success' => true, 'message' => '✅ API Key do ActiveCampaign validada com sucesso!'];
            }

            return ['success' => false, 'message' => '❌ Erro: Código de resposta ' . $code];
        }
    ],
    'hubspot' => [
        'label' => 'HubSpot',
        'doc'   => 'https://developers.hubspot.com/docs/api/private-apps',
        'fields' => [
            'access_token' => ['label' => 'Access Token', 'type' => 'secret'],
        ],
        'validate' => function ($data) {
            $token = trim($data['access_token']);
            $response = wp_remote_get('https://api.hubapi.com/integrations/v1/me', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                ],
                'timeout' => 10,
            ]);

            if (is_wp_error($response)) {
                return ['success' => false, 'message' => '❌ Erro na requisição: ' . $response->get_error_message()];
            }

            $code = wp_remote_retrieve_response_code($response);
            if ($code === 200) {
                alpha_form_save_integration('hubspot', [
                    'access_token' => $token,
                ]);
                return ['success' => true, 'message' => '✅ Token do HubSpot validado com sucesso!'];
            }

            return ['success' => false, 'message' => '❌ Erro: Código de resposta ' . $code];
        }
    ],

];

// 🔄 Processamento
$feedback = null;
if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'POST' && current_user_can('manage_options')) {
    foreach ($integrations as $slug => $integration) {
        if (isset($_POST["validate_$slug"])) {
            check_admin_referer("alpha_form_validate_$slug", "alpha_form_{$slug}_nonce");
            $data = [];

            foreach ($integration['fields'] as $field_key => $field) {
                $value = isset($_POST["alpha_form_{$slug}_{$field_key}"])
                    ? sanitize_text_field(wp_unslash($_POST["alpha_form_{$slug}_{$field_key}"]))
                    : '';
                $data[$field_key] = $value;
            }

            try {
                $result = $integration['validate']($data);
                $feedback = $result['message'];
            } catch (Throwable $e) {
                $feedback = '❌ Erro: ' . $e->getMessage();
            }
        }
    }
}
?>


<div class="wrap alpha-form-wrap">
    <h1>Integrações</h1>

    <?php if ($feedback): ?>
        <div class="notice <?php echo strpos($feedback, '✅') !== false ? 'notice-success' : 'notice-error'; ?> is-dismissible">
            <p><?php echo esc_html($feedback); ?></p>
        </div>
    <?php endif; ?>

    <?php foreach ($integrations as $slug => $integration): ?>
        <form method="post" class="alpha-form-integration-group">
            <h2>
                <?php echo esc_html($integration['label']); ?>
                <?php if (!empty($integration['doc'])): ?>
                    <a href="<?php echo esc_url($integration['doc']); ?>" target="_blank" rel="noopener noreferrer"
                        style="font-size: 14px; font-weight: normal; margin-left: 10px;">
                        Ver como configurar
                    </a>
                <?php endif; ?>
            </h2>

            <table class="form-table">
                <?php foreach ($integration['fields'] as $field_key => $field): ?>
                    <?php
                    $label = $field['label'];
                    $type = $field['type'] ?? 'text';
                    $field_id = "alpha_form_{$slug}_{$field_key}";
                    $value = get_option($field_id, '');
                    $is_hidden = !empty($value) && $type === 'secret';
                    ?>
                    <tr>
                        <th scope="row">
                            <label for="<?php echo esc_attr($field_id); ?>"><?php echo esc_html($label); ?></label>
                        </th>
                        <td>
                            <div class="alpha-form-input-wrapper" style="display: flex; gap: 8px; align-items: center;">
                                <input
                                    type="<?php echo $is_hidden ? 'password' : 'text'; ?>"
                                    id="<?php echo esc_attr($field_id); ?>"
                                    name="<?php echo esc_attr($field_id); ?>"
                                    class="regular-text"
                                    value="<?php echo esc_attr($value); ?>"
                                    <?php echo $is_hidden ? 'disabled' : ''; ?>>
                                <?php if ($is_hidden): ?>
                                    <button type="button" class="button alpha-form-edit-field" data-target="<?php echo esc_attr($field_id); ?>">✏️</button>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>

            <p>
                <button type="submit" name="validate_<?php echo esc_attr($slug); ?>" class="button button-primary">Validar</button>
            </p>

            <?php wp_nonce_field("alpha_form_validate_{$slug}", "alpha_form_{$slug}_nonce"); ?>
        </form>
    <?php endforeach; ?>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.alpha-form-edit-field').forEach(btn => {
            btn.addEventListener('click', function() {
                const input = document.getElementById(this.dataset.target);
                if (input) {
                    input.removeAttribute('disabled');
                    input.setAttribute('type', 'text');
                    input.focus();
                }
                this.remove();
            });
        });
    });
</script>