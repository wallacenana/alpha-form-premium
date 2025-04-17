<?php
if (!current_user_can('manage_options')) return;

add_action('admin_init', function () {
    if (current_user_can('manage_options')) {
        echo '<pre>';
        echo 'Chave: ' . esc_html(get_option('alpha_form_license_key')) . PHP_EOL;
        echo 'Status: ' . esc_html(get_option('alpha_form_license_status')) . PHP_EOL;
        echo 'Expires: ' . esc_html(get_option('alpha_form_license_expires')) . PHP_EOL;
        echo 'Domain: ' . esc_html(get_option('alpha_form_license_domain')) . PHP_EOL;
        echo 'Checked at: ' . esc_html(get_option('alpha_form_license_checked_at')) . PHP_EOL;
        echo '</pre>';
    }
});


?>

<div class="alpha-form-wrap">

    <h1>Licença do Alpha Form Premium</h1>
    <form id="alpha-form-license-form">
        <table class="form-table">
            <tr>
                <th scope="row"><label for="alpha_form_license_key">Chave de Licença</label></th>
                <td>
                    <div class="alpha-form-input-group">
                        <input type="password" id="alpha_form_license_key" name="alpha_form_license_key"
                            value="<?php echo esc_attr(get_option('alpha_form_license_key', '')); ?>"
                            maxlength="50" style="width: 350px;" disabled required>
                        <button type="button" id="toggle-edit-license" class="button" title="Editar licença">✏️</button>
                    </div>
                    <p class="description">Insira a sua chave de licença para ativar o plugin.</p>
                </td>
            </tr>
        </table>

        <p class="submit alpha-form-submit-wrapper">
            <button type="submit" class="button button-primary">Validar Licença</button>
        </p>
    </form>
    <div id="alpha_form_status_message" style="margin-top: 20px;"></div>

    <?php
    $status       = get_option('alpha_form_license_status', 'invalid');
    $expires      = get_option('alpha_form_license_expires', '');
    $checked_at   = get_option('alpha_form_license_checked_at', '');
    $days_left    = $expires ? (floor((strtotime($expires) - time()) / 86400)) : null;

    if ($checked_at): ?>
        <h2 style="margin-top: 40px;">Informações da Licença</h2>
        <ul style="line-height: 1.8;">
            <li><strong>Status:</strong>
                <?php echo $status === 'valid'
                    ? '<b style="color: green;">Ativada</b>'
                    : '<b style="color: red;">' . esc_html(strtoupper($status ?: 'N/A')) . '</b>'; ?>
            </li>
            <li><strong>Expira em:</strong>
                <?php echo $expires ? esc_html(gmdate('d/m/Y', strtotime($expires))) : 'Indefinido'; ?>
            </li>
            <li><strong>Dias restantes:</strong>
                <?php echo $days_left !== null ? esc_html(max(0, $days_left) . ' dias') : 'N/A'; ?>
            </li>
        </ul>
    <?php endif; ?>
</div>

<script>
    const alphaFormNonce = '<?php echo esc_js(wp_create_nonce("alpha_form_save_license")); ?>';
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('alpha-form-license-form');
        const input = document.getElementById('alpha_form_license_key');
        const statusDisplay = document.getElementById('alpha_form_status_message');
        const toggleButton = document.getElementById('toggle-edit-license');
        let isEditing = false;

        toggleButton.addEventListener('click', () => {
            if (!isEditing) {
                if (confirm("Tem certeza que deseja atualizar?")) {
                    input.removeAttribute('disabled');
                    input.setAttribute('type', 'text');
                    input.focus();
                    isEditing = true;
                    toggleButton.textContent = '🔒';
                    toggleButton.title = 'Bloquear edição';
                }
            } else {
                input.setAttribute('disabled', 'true');
                input.setAttribute('type', 'password');
                isEditing = false;
                toggleButton.textContent = '✏️';
                toggleButton.title = 'Editar licença';
            }
        });

        form.addEventListener('submit', async function(e) {
            e.preventDefault();

            const license = input.value.trim();
            const domain = window.location.origin;
            statusDisplay.innerHTML = 'Validando licença...';

            try {
                const res = await fetch(`https://alphaform.com.br/wp-json/alphaform/v2/validate?license=${license}&domain=${domain}`);
                const data = await res.json();

                if (data.success) {
                    statusDisplay.innerHTML = `<span style="color: green;">✅ ${data.message}</span>`;
                } else {
                    statusDisplay.innerHTML = `<span style="color: red;">❌ ${data.message}</span>`;
                }

                await fetch(ajaxurl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: new URLSearchParams({
                        action: 'alpha_form_save_license_data',
                        license: data.license || license,
                        status: data.status || 'invalid',
                        expires: data.expires || '',
                        domain: domain,
                        nonce: alphaFormNonce
                    })
                });

            } catch (error) {
                statusDisplay.innerHTML = `<span style="color: red;">Erro na requisição: ${error.message}</span>`;
            }
        });
    });
</script>