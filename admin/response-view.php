<?php
// Caminho sugerido: /admin/pages/response-view.php
if (!current_user_can('manage_options')) {
    wp_die('Acesso negado.');
}

if (!isset($_GET['_wpnonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_GET['_wpnonce'])), 'alpha_form_view_response')) {
    wp_die('Acesso negado (nonce inválido).');
}



$response_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$response_id) {
    echo '<div class="notice notice-error"><p>ID da resposta não informado.</p></div>';
    return;
}

global $wpdb;
$table = $wpdb->prefix . 'alpha_form_responses';
$response = $wpdb->get_row($wpdb->prepare("SELECT * FROM %i WHERE id = %d", $table, $response_id));

if (!$response) {
    echo '<div class="notice notice-error"><p>Resposta não encontrada.</p></div>';
    return;
}

$data = json_decode($response->data, true);
$labels = alphaform_map_labels_from_widget($response->postId, $response->widget_id);

?>

<div class="wrap alpha-form-wrap">
    <h1 class="wp-heading-inline">Detalhes da Resposta</h1>
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
            <tr>
                <th>Status</th>
                <td>
                    <?php
                    if ($response->concluido == 1) {
                        echo '<span class="text-success">Concluído</span>';
                    } elseif ($response->start_form == 1 && $response->concluido == 0) {
                        echo '<span class="text-info">Não concluído</span>';
                    } elseif ($response->page_view == 1 && $response->start_form == 0) {
                        echo '<span class="text-warning">Não Iniciado</span>';
                    } else {
                        echo '<span class="text-warning">Não Iniciado</span>';
                    }
                    ?>
                </td>
            </tr>
            <tr>
                <th>Cidade</th>
                <td><?php echo esc_html($response->city ?? 'Desconhecida'); ?></td>
            </tr>
            <tr>
                <th>Estado</th>
                <td><?php echo esc_html($response->state ?? 'Desconhecido'); ?></td>
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