<?php
// Caminho: admin/pages/responses.php

if (!defined('ABSPATH')) exit;

// Configuração de paginação
$per_page = 20;
$current_page = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
$offset = ($current_page - 1) * $per_page;

// Filtro por widget_id
$widget_id = isset($_GET['widget_id']) ? sanitize_text_field($_GET['widget_id']) : '';

// Tabela personalizada
global $wpdb;
$table = $wpdb->prefix . 'alpha_form_responses';

$where = '';
$params = [];

if ($widget_id) {
    $where = 'WHERE widget_id = %s';
    $params[] = $widget_id;
}

// Total de registros
$total = $widget_id
    ? $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table WHERE widget_id = %s", $widget_id))
    : $wpdb->get_var("SELECT COUNT(*) FROM $table");

// Resultados com paginação
$sql = "SELECT id, form_id, session_id, postId, widget_id, submitted_at FROM $table $where ORDER BY submitted_at DESC LIMIT %d OFFSET %d";
$params[] = $per_page;
$params[] = $offset;
$results = $wpdb->get_results($wpdb->prepare($sql, ...$params));

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
                        <td><?php echo esc_html(date('d/m/Y H:i', strtotime($row->submitted_at))); ?></td>
                        <td><a href="<?php echo admin_url('admin.php?page=alpha-form-view-response&id=' . $row->id) ?>" class="button button-small">Ver Detalhes</a></td>

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
        echo paginate_links([
            'base' => $base_url . '&paged=%#%',
            'format' => '',
            'current' => $current_page,
            'total' => $total_pages,
            'prev_text' => '<i class="dashicons dashicons-arrow-left-alt2"></i>',
            'next_text' => '<i class="dashicons dashicons-arrow-right-alt2"></i>'
        ]);
        echo '</div></div>';
    endif;
    ?>
</div>