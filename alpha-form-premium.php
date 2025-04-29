<?php defined('ABSPATH') || die('No direct access allowed');

/**
 * Plugin Name: Alpha Form Premium - Addon for Elementor
 * Plugin URI: https://alphaform.com.br
 * Description: Formulário estilo Premium integrado ao Elementor
 * Version: 1.0
 * Author: Wallace Tavares
 * Author URI: https://wallacetavares.com
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: alpha-form-premium
 */

if (!defined('ABSPATH')) exit;

define('ALPHA_FORM_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('ALPHA_FORM_PLUGIN_URL', plugin_dir_url(__FILE__));

// Inclui o conteúdo dinâmico do frontend, se necessário
require_once ALPHA_FORM_PLUGIN_PATH . 'ajax/ajax-save-response.php';
require_once ALPHA_FORM_PLUGIN_PATH . 'includes/helpers.php';
require_once ALPHA_FORM_PLUGIN_PATH . 'includes/line-helped.php';
require_once ALPHA_FORM_PLUGIN_PATH . 'ajax/ajax-hooks.php';
require_once ALPHA_FORM_PLUGIN_PATH . 'ajax/end-point-dashboard.php';
require_once ALPHA_FORM_PLUGIN_PATH . 'module/actions/submit-handler.php';

// =====================================================
// INICIALIZA FORMULÁRIO DO ELEMENTOR
// =====================================================

add_action('plugins_loaded', function () {
    if (!defined('ELEMENTOR_VERSION')) return;
    require_once ALPHA_FORM_PLUGIN_PATH . 'module/form.php';
    if (class_exists('AlphaFormPremium\\Module\\Form')) {
        (new \AlphaFormPremium\Module\Form())->init();
    }
});

// =====================================================
// ADMIN MENU
// =====================================================
add_action('admin_menu', 'alpha_form_add_user_dashboard_menu');

function alpha_form_add_user_dashboard_menu()
{
    add_menu_page(
        'Alpha Form - Meus Dados',          // Título da página
        'Alpha Form',                       // Título do menu
        'read',                             // Capacidade mínima
        'alpha-form-dashboard',             // Slug da página
        'alpha_form_render_dashboard_page', // Função de callback
        'dashicons-feedback',               // Ícone
        56                                  // Posição
    );

    add_submenu_page(
        'alpha-form-dashboard',
        'Formulários',
        'Formulários',
        'read',
        'alpha-form-forms',
        'alpha_form_render_forms_page'
    );

    add_submenu_page(
        'alpha-form-dashboard',
        'Estatísticas',
        'Estatísticas',
        'read',
        'alpha-form-stats',
        'alpha_form_render_stats_page'
    );

    add_submenu_page(
        'alpha-form-dashboard',
        'Integrações',
        'Integrações',
        'read',
        'alpha-form-integrations',
        'alpha_form_render_integrations_page'
    );

    add_submenu_page(
        'alpha-form-dashboard', // Não aparece no menu
        'Visualizar Resposta',
        '',
        'manage_options',
        'alpha-form-view-response',
        function () {
            require_once ALPHA_FORM_PLUGIN_PATH . 'admin/response-view.php';
        }
    );


    add_submenu_page(
        'alpha-form-dashboard',
        'Respostas',
        '',
        'read',
        'alpha-form-responses',
        'alpha_form_render_responses_page'
    );
}

// Páginas de callback (por enquanto, apenas placeholders)
function alpha_form_render_dashboard_page()
{
    include_once ALPHA_FORM_PLUGIN_PATH . 'admin/dashboard.php';
}
function alpha_form_render_forms_page()
{
    include_once ALPHA_FORM_PLUGIN_PATH . 'admin/forms.php';
}
function alpha_form_render_responses_page()
{
    include_once ALPHA_FORM_PLUGIN_PATH . 'admin/responses.php';
}
function alpha_form_render_integrations_page()
{
    include_once ALPHA_FORM_PLUGIN_PATH . 'admin/integrations.php';
}
function alpha_form_render_stats_page()
{
    include_once ALPHA_FORM_PLUGIN_PATH . 'admin/stats.php';
}

// =====================================================
// MENU ICON STYLE
// =====================================================
add_action('admin_head', function () {
    echo '<style>#adminmenu .toplevel_page_alpha-form-settings img { width: 20px; height: 20px; object-fit: contain; }</style>';
});


// =====================================================
// ENQUEUE ASSETS FRONT E ADMIN
// =====================================================
function alpha_form_admin_assets($hook)
{
    if (strpos($hook, 'alpha-form-') === false) return;

    wp_enqueue_style(
        'alpha-form-admin-style',
        ALPHA_FORM_PLUGIN_URL . 'assets/css/alpha-form-style.css',
        [],
        filemtime(ALPHA_FORM_PLUGIN_PATH . 'assets/css/alpha-form-style.css')
    );

    wp_enqueue_script(
        'alpha-form-dashboard-script',
        ALPHA_FORM_PLUGIN_URL . 'assets/js/dashboard.js',
        ['jquery'],
        filemtime(ALPHA_FORM_PLUGIN_PATH . 'assets/js/dashboard.js'),
        true
    );

    wp_enqueue_script(
        'chartjs',
        'https://cdn.jsdelivr.net/npm/chart.js',
        [],
        '4.4.0', // 👈 define manualmente a versão da CDN
        true
    );

    wp_localize_script('alpha-form-dashboard-script', 'alpha_form_nonce', [
        'ajaxurl' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('alpha_form_nonce'),
        'plugin_url' => ALPHA_FORM_PLUGIN_URL,
        'assetsUrl' => ALPHA_FORM_PLUGIN_URL . 'assets/img/',
    ]);

    wp_enqueue_script(
        'alpha-form-admin-script',
        ALPHA_FORM_PLUGIN_URL . 'assets/js/alpha-form.js',
        [],
        filemtime(ALPHA_FORM_PLUGIN_PATH . 'assets/js/alpha-form.js'),
        true
    );
}

function alpha_form_enqueue_select2()
{
    wp_enqueue_style(
        'select2-css',
        'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css',
        [],
        '4.1.0-rc.0' // 👈 coloca a versão
    );

    wp_enqueue_script(
        'select2-js',
        'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js',
        ['jquery'],
        '4.1.0-rc.0',
        true // 👈 e define para rodapé
    );
}

add_action('admin_enqueue_scripts', 'alpha_form_enqueue_select2');
add_action('wp_enqueue_scripts', 'alpha_form_enqueue_select2'); // caso precise no frontend

add_action('admin_enqueue_scripts', 'alpha_form_admin_assets');

function alpha_form_enqueue_front_assets()
{
    wp_enqueue_style('alpha-form-style', ALPHA_FORM_PLUGIN_URL . 'assets/css/alpha-form-style.css', [], filemtime(ALPHA_FORM_PLUGIN_PATH . 'assets/css/alpha-form-style.css'));

    wp_enqueue_script('alpha-form-js', ALPHA_FORM_PLUGIN_URL . 'assets/js/alpha-form.js', ['jquery'], filemtime(ALPHA_FORM_PLUGIN_PATH . 'assets/js/alpha-form.js'), true);

    wp_localize_script('alpha-form-js', 'alphaFormVars', [
        'ajaxurl' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('alpha_form_nonce')
    ]);
}
add_action('wp_enqueue_scripts', 'alpha_form_enqueue_front_assets');

add_action('elementor/preview/enqueue_styles', function () {
    wp_enqueue_style('alpha-form-style-preview', ALPHA_FORM_PLUGIN_URL . 'assets/css/alpha-form-style.css', [], filemtime(ALPHA_FORM_PLUGIN_PATH . 'assets/css/alpha-form-style.css'));
});

add_action('elementor/editor/after_enqueue_scripts', function () {
    wp_enqueue_script(
        'alphaform-editor-js',
        plugin_dir_url(__FILE__) . 'assets/js/editor.js',
        ['jquery'],
        '1.0',
        true
    );

    global $post;

    wp_localize_script('alphaform-editor-js', 'alphaFormVars', [
        'ajaxurl' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('alpha_form_nonce'),
        'post_id' => $post->ID ?? 0,
    ]);
});



// =====================================================
// ACTION LINK NA LISTA DE PLUGINS
// =====================================================
add_filter('plugin_action_links_' . plugin_basename(__FILE__), function ($links) {
    $settings_link = '<a href="' . admin_url('admin.php?page=alpha-form-settings') . '">Configurações</a>';
    array_unshift($links, $settings_link);
    return $links;
});


add_action('admin_menu', function () {
    add_submenu_page(
        null,
        'Configurar Integração',
        'Configurar Integração',
        'manage_options',
        'alpha-form-integration',
        function () {
            if (
                !isset($_GET['_alpha_form_nonce']) ||
                !wp_verify_nonce(sanitize_text_field(wp_unslash($_GET['_alpha_form_nonce'])), 'alpha_form_settings_action')
            ) {
                wp_die('Acesso negado (nonce inválido).');
            }


            $service = isset($_GET['service']) ? sanitize_text_field(wp_unslash($_GET['service'])) : '';
            $file = plugin_dir_path(__FILE__) . "integrations/{$service}.php";

            if (file_exists($file)) {
                include $file;
            } else {
                echo '<div class="wrap"><h1>Integração não encontrada</h1></div>';
            }
        }
    );
});


add_action('admin_menu', function () {
    add_submenu_page(
        'alpha-form-settings',
        'Integrações',
        'Integrações',
        'manage_options',
        'alpha-form-integrations',
        function () {
            include plugin_dir_path(__FILE__) . 'integrations/index.php';
        }
    );
});


// Salvamento da licença no admin
add_action('wp_ajax_alpha_form_save_license_data', function () {
    if (!current_user_can('manage_options')) {
        wp_send_json_error(['message' => 'Acesso negado.']);
    }

    check_ajax_referer('alpha_form_save_license', 'nonce');

    $license = isset($_POST['license']) ? sanitize_text_field(wp_unslash($_POST['license'])) : '';
    $status  = isset($_POST['status']) ? sanitize_text_field(wp_unslash($_POST['status'])) : 'invalid';
    $expires = isset($_POST['expires']) ? sanitize_text_field(wp_unslash($_POST['expires'])) : '';
    $domain  = isset($_POST['domain']) ? sanitize_text_field(wp_unslash($_POST['domain'])) : '';


    update_option('alpha_form_license_key', $license);
    update_option('alpha_form_license_status', $status);
    update_option('alpha_form_license_expires', $expires);
    update_option('alpha_form_license_domain', $domain);
    update_option('alpha_form_license_checked_at', current_time('mysql'));

    wp_send_json_success('Licença salva com sucesso');
});


register_activation_hook(__FILE__, 'alpha_form_premium_activate');

function alpha_form_premium_activate()
{
    require_once plugin_dir_path(__FILE__) . 'includes/db-install.php';
    alpha_form_create_response_table();
}
