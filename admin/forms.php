<?php
// Caminho: /admin/pages/forms.php
if (!defined('ABSPATH')) exit;

global $wpdb;
$table = $wpdb->prefix . 'alpha_form_responses';

// Consulta agrupando por widget_id
$results = $wpdb->get_results("SELECT widget_id, MAX(form_id) as form_id, COUNT(*) as total, MAX(postId) as postId FROM {$table} GROUP BY widget_id", ARRAY_A);

?>
<div class="wrap alpha-form-wrap">
    <h1 class="wp-heading-inline">Formulários</h1>
    <p class="description">Lista de todos os formulários enviados pelo Alpha Form, agrupados por Widget ID.</p>

    <table class="widefat fixed striped">
        <thead>
            <tr>
                <th width="10%">ID</th>
                <th>Nome do Formulário</th>
                <th>Página</th>
                <th width="15%">Respostas</th>
                <th width="15%">Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($results)) : ?>
                <?php foreach ($results as $row) :
                    $post_id = intval($row['postId']);
                    $post_title = get_the_title($post_id);
                    $post_url = get_option('siteurl') . '/wp-admin/post.php?post=' . $post_id . '&action=elementor';
                    $widget_id = esc_html($row['widget_id']);
                ?>
                    <tr>
                        <td><code><?php echo $widget_id; ?></code></td>
                        <td><?php echo esc_html($row['form_id']) ?? 'Desconhecido'; ?></td>
                        <td>
                            <?php if ($post_title && $post_url) : ?>
                                <a href="<?php echo esc_url($post_url); ?>" target="_blank">Ver página</a>
                            <?php else : ?>
                                <em>Não encontrado</em>
                            <?php endif; ?>
                        </td>
                        <td><strong><?php echo intval($row['total']); ?></strong></td>
                        <td>
                            <a href="admin.php?page=alpha-form-responses&widget_id=<?php echo $widget_id; ?>" class="button">Ver Respostas</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else : ?>
                <tr>
                    <td colspan="5">Nenhum formulário encontrado.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>