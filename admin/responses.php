<?php
// Caminho sugerido: /admin/pages/response-view.php
if (!current_user_can('manage_options')) {
    wp_die('Acesso negado.');
}

if (!isset($_GET['_wpnonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_GET['_wpnonce'])), 'alpha_form_responses_list')) {
    wp_die('Acesso negado (nonce inválido).');
}

// Configuração de paginação
$per_page = 20;
$current_page = isset($_GET['paged']) ? max(1, intval(sanitize_text_field(wp_unslash($_GET['paged'])))) : 1;
$offset = ($current_page - 1) * $per_page;

// Filtro por widget_id
$widget_id = isset($_GET['widget_id']) ? sanitize_text_field(wp_unslash($_GET['widget_id'])) : '';

// Tabela personalizada
global $wpdb;

$table = $wpdb->prefix . 'alpha_form_responses';
$params = [];

$cache_key_total = 'alpha_form_total_' . ($widget_id ? $widget_id : 'all');

$total = wp_cache_get($cache_key_total, 'alpha_form');

// phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQLPlaceholders.ReplacementsWrongNumber	

if (false === $total) {
    if ($widget_id) {
        $total = $wpdb->get_var(
            $wpdb->prepare(
                "
                SELECT COUNT(*)
                FROM $table
                WHERE widget_id = %s
                ",
                $widget_id
            )
        );
    } else {
        $total = $wpdb->get_var(
            $wpdb->prepare(
                "
                SELECT COUNT(*)
                FROM $table
                "
            )
        );
    }

    if (!is_null($total)) {
        wp_cache_set($cache_key_total, $total, 'alpha_form', 600);
    }
}

// Paginação
$params[] = $per_page;
$params[] = $offset;

$cache_key_results = 'alpha_form_results_' . ($widget_id ? $widget_id : 'all') . "_page_$current_page";

$results = wp_cache_get($cache_key_results, 'alpha_form');

if (false === $results) {
    if ($widget_id) {
        $results = $wpdb->get_results(
            $wpdb->prepare(
                "
                SELECT id, form_id, session_id, postId, widget_id, submitted_at
                FROM $table
                WHERE widget_id = %s
                ORDER BY submitted_at DESC
                LIMIT %d OFFSET %d
                ",
                $widget_id,
                ...$params
            )
        );
    } else {
        $results = $wpdb->get_results(
            $wpdb->prepare(
                "
                SELECT id, form_id, session_id, postId, widget_id, submitted_at
                FROM $table
                ORDER BY submitted_at DESC
                LIMIT %d OFFSET %d
                ",
                ...$params
            )
        );
    }

    if (!empty($results)) {
        wp_cache_set($cache_key_results, $results, 'alpha_form', 300);
    }
}

// phpcs:enable WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQLPlaceholders.ReplacementsWrongNumber	

// Renderização
?>
<div class="wrap alpha-form-wrap">
    <h1 class="wp-heading-inline">Respostas dos Formulários</h1>
    <?php if ($widget_id): ?>
        <p>Exibindo respostas do widget <code><?php echo esc_html($widget_id); ?></code></p>
    <?php endif; ?>

    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Formulário</th>
                <th>Post ID</th>
                <th>Widget ID</th>
                <th>Data de Envio</th>
                <th>Ação</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($results): ?>
                <?php foreach ($results as $row): ?>
                    <tr>
                        <td><?php echo esc_html($row->id); ?></td>
                        <td><?php echo esc_html($row->form_id); ?></td>
                        <td><?php echo esc_html($row->postId); ?></td>
                        <td><?php echo esc_html($row->widget_id); ?></td>
                        <td><?php echo esc_html(gmdate('d/m/Y H:i', strtotime($row->submitted_at))); ?></td>
                        <td>
                            <a href="<?php echo esc_url(
                                            wp_nonce_url(
                                                admin_url('admin.php?page=alpha-form-view-response&id=' . intval($row->id)),
                                                'alpha_form_view_response',
                                                '_wpnonce'
                                            )
                                        ); ?>" class="button button-small">Ver Detalhes</a>
                        </td>

                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6">Nenhuma resposta encontrada.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <?php
    $total_pages = ceil($total / $per_page);
    if ($total_pages > 1):
        $base_url = admin_url('admin.php?page=alpha-form-responses');
        if ($widget_id) $base_url .= '&widget_id=' . urlencode($widget_id);
        echo '<div class="tablenav"><div class="tablenav-pages">';
        echo wp_kses_post(paginate_links([
            'base' => $base_url . '&paged=%#%',
            'format' => '',
            'current' => $current_page,
            'total' => $total_pages,
            'prev_text' => '<i class="dashicons dashicons-arrow-left-alt2"></i>',
            'next_text' => '<i class="dashicons dashicons-arrow-right-alt2"></i>',
        ]));

        echo '</div></div>';
    endif;
    ?>
</div>