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
require_once ALPHA_FORM_PLUGIN_PATH . 'includes/ajax-save-response.php';
require_once ALPHA_FORM_PLUGIN_PATH . 'includes/helpers.php';
require_once ALPHA_FORM_PLUGIN_PATH . 'includes/line-helped.php';
require_once ALPHA_FORM_PLUGIN_PATH . 'ajax/ajax-hooks.php';
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
add_action('admin_menu', function () {
    add_menu_page(
        'Alpha Form Premium', 'Alpha Form', 'manage_options', 'alpha-form-settings', 'alpha_form_premium_settings_page',
        ALPHA_FORM_PLUGIN_URL . 'assets/img/icon.png', 56
    );

    add_submenu_page(
        null, 'Configurar Integração', 'Configurar Integração', 'manage_options', 'alpha-form-integration', function () {
            $service = isset($_GET['service']) ? sanitize_text_field($_GET['service']) : '';
            $file = ALPHA_FORM_PLUGIN_PATH . "integrations/{$service}.php";
            if (file_exists($file)) include $file;
            else echo '<div class="wrap"><h1>Integração não encontrada</h1></div>';
        }
    );

    add_submenu_page(
        'alpha-form-settings', 'Integrações', 'Integrações', 'manage_options', 'alpha-form-integrations', function () {
            include ALPHA_FORM_PLUGIN_PATH . 'integrations/index.php';
        }
    );
});

function alpha_form_premium_settings_page() {
    $settings_view_path = ALPHA_FORM_PLUGIN_PATH . 'admin/settings-view.php';
    if (file_exists($settings_view_path)) include $settings_view_path;
    else echo '<div class="wrap"><h1>Alpha Form Premium</h1><p>Configurações do plugin não encontradas.</p></div>';
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
function alpha_form_admin_assets($hook) {
    if (strpos($hook, 'alpha-form-') === false) return;

    wp_enqueue_style('alpha-form-admin-style', ALPHA_FORM_PLUGIN_URL . 'assets/css/alpha-form-style.css', [], filemtime(ALPHA_FORM_PLUGIN_PATH . 'assets/css/alpha-form-style.css'));
    wp_enqueue_script('alpha-form-admin-script', ALPHA_FORM_PLUGIN_URL . 'assets/js/alpha-form.js', [], filemtime(ALPHA_FORM_PLUGIN_PATH . 'assets/js/alpha-form.js'), true);
}
add_action('admin_enqueue_scripts', 'alpha_form_admin_assets');

function alpha_form_enqueue_front_assets() {
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
            $service = isset($_GET['service']) ? sanitize_text_field($_GET['service']) : '';
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

    $license = sanitize_text_field($_POST['license'] ?? '');
    $status = sanitize_text_field($_POST['status'] ?? 'invalid');
    $expires = sanitize_text_field($_POST['expires'] ?? '');
    $domain = sanitize_text_field($_POST['domain'] ?? '');

    update_option('alpha_form_license_key', $license);
    update_option('alpha_form_license_status', $status);
    update_option('alpha_form_license_expires', $expires);
    update_option('alpha_form_license_domain', $domain);
    update_option('alpha_form_license_checked_at', current_time('mysql'));

    wp_send_json_success('Licença salva com sucesso');
});


register_activation_hook(__FILE__, 'alpha_form_premium_activate');

function alpha_form_premium_activate() {
    require_once plugin_dir_path(__FILE__) . 'includes/db-install.php';
    alpha_form_create_response_table();
}


