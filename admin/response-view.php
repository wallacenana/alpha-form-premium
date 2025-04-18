<?php
// Caminho sugerido: /admin/pages/response-view.php
if (!current_user_can('manage_options')) {
    wp_die('Acesso negado.');
}

$response_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$response_id) {
    echo '<div class="notice notice-error"><p>ID da resposta não informado.</p></div>';
    return;
}

global $wpdb;
$table = $wpdb->prefix . 'alpha_form_responses';
$response = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE id = %d", $response_id));

if (!$response) {
    echo '<div class="notice notice-error"><p>Resposta não encontrada.</p></div>';
    return;
}

$data = json_decode($response->data, true);
$labels = alphaform_map_labels_from_widget($response->postId, $response->widget_id);

?>

<div class="wrap alpha-form-wrap">
    <h1 class="wp-heading-inline">Detalhes da Resposta</h1>
    <a href="<?php echo admin_url('admin.php?page=alpha-form-responses&widget_id=' . esc_attr($response->widget_id)); ?>" class="page-title-action">Voltar</a>
    <hr class="wp-header-end">

    <table class="widefat fixed striped">
        <tbody>
            <tr>
                <th>ID</th>
                <td><?php echo esc_html($response->id); ?></td>
            </tr>
            <tr>
                <th>Formulário</th>
                <td><?php echo esc_html($response->form_id); ?></td>
            </tr>
            <tr>
                <th>Widget ID</th>
                <td><?php echo esc_html($response->widget_id); ?></td>
            </tr>
            <tr>
                <th>Post ID</th>
                <td><?php echo esc_html($response->postId); ?></td>
            </tr>
            <tr>
                <th>Data de Envio</th>
                <td><?php echo esc_html($response->submitted_at); ?></td>
            </tr>
        </tbody>
    </table>

    <h2>Respostas</h2>
    <table class="widefat striped">
        <thead>
            <tr>
                <th>Campo</th>
                <th>Resposta</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($data as $field => $value) : ?>
                <tr>
                    <td>
                        <?php
                        // Usa o label legível se existir, senão mostra o ID do campo
                        echo esc_html($labels['field_' . $field] ?? $field);

                        ?>
                    </td>
                    <td>
                        <?php
                        if (is_array($value)) {
                            echo esc_html(implode(', ', $value));
                        } else {
                            echo esc_html($value);
                        }
                        ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>