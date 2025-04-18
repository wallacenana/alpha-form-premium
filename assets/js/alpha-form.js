document.addEventListener('DOMContentLoaded', function () {
    const wrapper = document.querySelector('.alpha-form-wrapper');
    if (!wrapper) return;

    const fields = wrapper.querySelectorAll('.alpha-form-field');
    const allNextButtons = wrapper.querySelectorAll('.alpha-form-next-button, .alpha-form-next-button-x');
    const prevBtn = wrapper.querySelector('.alpha-form-prev-button-x');
    const scrollEnabled = wrapper.closest('.alpha-form')?.dataset.scroll === 'yes';

    let currentIndex = 0;

    function showField(index, direction = 'forward') {
        if (index < 0 || index >= fields.length) return;

        // Detecta se o próximo campo é hidden
        const currentField = fields[index];
        const isHidden = !!currentField.querySelector('input[type="hidden"]');

        if (isHidden) {
            // Pula para frente ou para trás, dependendo da direção
            const nextIndex = direction === 'forward' ? index + 1 : index - 1;
            showField(nextIndex, direction);
            currentIndex = nextIndex;
            return;
        }

        // Exibe o campo atual
        fields.forEach((f, i) => {
            f.classList.toggle('active', i === index);
        });

        updateProgressBar(index);
        currentIndex = index;

        // Foco automático
        const focusable = currentField.querySelector('input:not([type="hidden"]), textarea, select');
        if (focusable) {
            setTimeout(() => focusable.focus(), 100);
        }
    }



    function showError(input, message) {
        const error = document.createElement('p');
        error.className = 'alpha-error';
        error.textContent = message;
        input.after(error);
    }

    function validateField(field) {
        const input = field.querySelector('input:not([type=hidden]), textarea, select');
        const type = input?.type;
        const isRequired = input?.hasAttribute('required');

        // Remove erro anterior
        field.querySelector('.alpha-error')?.remove();

        if (type === 'radio') {
            const groupName = input.name;
            const selected = field.querySelector(`input[name="${groupName}"]:checked`);
            if (isRequired && !selected) {
                showError(input, 'Escolha uma opção.');
                return false;
            }
            return true;
        }

        const value = input?.value?.trim();
        if (isRequired && !value) {
            showError(input, 'Este campo é obrigatório.');
            return false;
        }

        if (type === 'email' && value && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value)) {
            showError(input, 'Informe um e-mail válido.');
            return false;
        }

        if (type === 'tel' && value && !/^\(?\d{2}\)?\s?\d{4,5}-?\d{4}$/.test(value)) {
            showError(input, 'Telefone inválido. Ex: (11) 91234-5678');
            return false;
        }

        if (type === 'url' && value && !/^https?:\/\/[^\s]+$/.test(value)) {
            showError(input, 'URL inválida.');
            return false;
        }

        return true;
    }

    // Avanço automático por clique
    allNextButtons.forEach((btn) => {
        btn.addEventListener('click', () => {
            const currentField = fields[currentIndex];
            const isValid = validateField(currentField);
            if (!isValid) return;

            saveFieldData(currentField);

            if (currentIndex < fields.length - 1) {
                showField(currentIndex + 1, 'forward');
            }
        });
    });

    // Voltar
    if (prevBtn) {
        prevBtn.addEventListener('click', () => {
            if (currentIndex > 0) {
                showField(currentIndex - 1, 'backward');
            }
        });
    }

    // Enter para avançar
    fields.forEach((field, index) => {
        const input = field.querySelector('input:not([type=hidden]), textarea');
        if (!input) return;

        input.addEventListener('keydown', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();

                const isValid = validateField(field);
                if (!isValid) return;

                saveFieldData(field); // ✅ Salva os dados antes de avançar

                if (index < fields.length - 1) {
                    currentIndex = index + 1;
                    showField(currentIndex);
                }
            }
        });
    });


    // Radio auto avanço
    wrapper.querySelectorAll('input[type="radio"]').forEach((radio) => {
        radio.addEventListener('change', () => {
            const field = radio.closest('.alpha-form-field');
            const index = Array.from(fields).indexOf(field);

            saveFieldData(field);

            if (index >= 0 && index < fields.length - 1) {
                currentIndex = index + 1;
                showField(currentIndex);
            }
        });
    });

    function updateProgressBar(index) {
        const visible = document.querySelector('.alpha-form-progress-container');
        if (!visible)
            return
        const allSteps = document.querySelectorAll('.alpha-form-step');

        // Filtra os campos visíveis (exclui hidden e a introdução)
        const visibleSteps = Array.from(allSteps).filter((step, i) => {
            const input = step.querySelector('input, textarea, select');
            const isHidden = input?.type === 'hidden';
            const isIntro = i === 0;
            const isSubmit = step.querySelector('button[type="submit"]');
            return !isHidden && !isIntro && !isSubmit;
        });

        const progressBar = document.querySelector('.alpha-form-progress-fill');
        const progressText = document.querySelector('.alpha-form-progress-text');

        const currentStep = allSteps[index];
        const visibleIndex = visibleSteps.indexOf(currentStep);

        // Total real = campos visíveis + 1 (para o submit)
        const total = visibleSteps.length + 1;

        // Se for o campo final (submit), já mostra 100%
        if (currentStep && currentStep.querySelector('button[type="submit"]')) {
            progressBar.style.width = '100%';
            progressText.textContent = '100%';
            return;
        }

        // Se o campo não está na lista visível (ex: introdução ou hidden), zera
        if (visibleIndex === -1) {
            progressBar.style.width = '0%';
            progressText.textContent = '0%';
            return;
        }

        // Calcula o progresso normalmente
        const percent = Math.round((visibleIndex + 1) / total * 100);

        progressBar.style.width = percent + '%';
        progressText.textContent = percent + '%';
    }

    let sessionId;

    function generateSessionId() {
        return 'afp_' + Math.random().toString(36).substr(2, 9) + Date.now();
    }

    sessionId = generateSessionId();
    localStorage.setItem('alpha_form_session_id', sessionId);

    function saveFieldData(fieldElement) {
        const input = fieldElement.querySelector('input, textarea, select');
        if (!input) return;

        let value;

        if (input.type === 'checkbox') {
            const checkboxes = fieldElement.querySelectorAll(`input[name="${input.name}"]:checked`);
            value = Array.from(checkboxes).map(cb => cb.value);
        } else if (input.type === 'radio') {
            const selected = fieldElement.querySelector(`input[name="${input.name}"]:checked`);
            value = selected ? selected.value : '';
        } else {
            value = input.value?.trim();
        }


        const form = fieldElement.closest('form');
        const formId = form?.dataset.formId || 'alpha_form_undefined';
        const widgetId = form.dataset.widgetId;
        const postId = parseInt(document.querySelector('[data-elementor-id]')?.dataset.elementorId || 0);

        const data = {
            action: 'alpha_form_save_response',
            form_id: formId,
            session_id: sessionId,
            nonce: alphaFormVars.nonce,
            widgetId: widgetId,
            postId: postId,
            response: JSON.stringify({
                [input.name]: value
            })
        };

        fetch(alphaFormVars.ajaxurl, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams(data)
        })
            .then(res => res.json())
            .then(response => {
                if (!response.success) {
                    console.warn('❌ Erro ao salvar resposta:', response.data?.message || 'Erro desconhecido');
                    if (response.data?.sql_error) {
                        console.log('📛 SQL Error:', response.data.sql_error);
                    }
                    if (response.data?.debug_data) {
                        console.log('📦 Dados enviados:', response.data.debug_data);
                    }
                }
            })
            .catch(err => {
                console.error('🚨 Erro na requisição:', err);
            });
    }

    function mostrarLoader() {
        document.getElementById('alphaform-overlay').style.display = 'flex';
    }
    function esconderLoader() {
        document.getElementById('alphaform-overlay').style.display = 'none';
    }


    document.querySelectorAll('.alpha-form').forEach(form => {
        form.addEventListener('submit', async function (e) {
            e.preventDefault();
            mostrarLoader();

            const postId = parseInt(document.querySelector('[data-elementor-id]')?.dataset.elementorId || 0);
            const widgetId = form.dataset.widgetId;
            const raw = {};

            const inputs = form.querySelectorAll('input, textarea, select');
            const data = {};

            inputs.forEach(input => {
                if (!input.name) return;

                if (input.type === 'checkbox') {
                    if (!data[input.name]) data[input.name] = [];
                    if (input.checked) data[input.name].push(input.value);
                } else if (input.type === 'radio') {
                    if (input.checked) data[input.name] = input.value;
                } else {
                    data[input.name] = input.value;
                }
            });

            let redirectUrl = form.dataset.redirect;

            if (redirectUrl && redirectUrl.includes('[field_')) {
                redirectUrl = redirectUrl.replace(/\[field_([^\]]+)\]/g, (match, key) => {
                    const input = document.querySelector(`[data-shortcode="field_${key}"]`);
                    const value = input?.value || '';
                    return encodeURIComponent(value);
                });
            }


            // inicio do envio com js

            inputs.forEach(input => {
                if (!input.name) return;

                if (input.type === 'checkbox') {
                    if (!data[input.name]) data[input.name] = [];
                    if (input.checked) data[input.name].push(input.value);
                } else if (input.type === 'radio') {
                    if (input.checked) data[input.name] = input.value;
                } else {
                    data[input.name] = input.value;
                }

                raw[input.name] = input.value;
            });

            console.log('[AlphaForm] Dados preenchidos:', data);

            // 🔁 AJAX para buscar ações e mapeamentos
            const res = await fetch(alphaFormVars.ajaxurl, {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams({
                    action: 'alphaform_get_widget_actions',
                    post_id: postId,
                    widget_id: widgetId,
                    nonce: alphaFormVars.nonce
                })
            });

            const json = await res.json();
            if (!json.success) {
                console.error('[AlphaForm] Erro ao buscar ações:', json);
                return;
            }

            const actions = json.data.actions || [];
            const map = json.data.map || {};
            const listaId = json.data.listaId || {};
            const listaIdMC = json.data.listaIdMC || {};

            console.log('[AlphaForm] Integrações ativadas:', actions);
            console.log('[AlphaForm] Mapeamentos configurados:', map);

            // 🔁 Pega os dados REAIS dos inputs com base no map
            const dadosMapeados = {};

            Object.entries(map).forEach(([chave, idCampo]) => {
                if (!idCampo) return;

                // Escapa IDs inválidos (ex: que começam com número)
                const inputEl = document.querySelector(`[id="${idCampo}"]`);

                if (inputEl && inputEl.value !== '') {
                    dadosMapeados[chave] = inputEl.value;
                    console.log(`[AlphaForm] ${chave}: ${inputEl.value}`);
                } else {
                    console.warn(`[AlphaForm] Campo com ID "${idCampo}" não encontrado ou está vazio (${chave})`);
                }
            });


            // Envio dos dados mapeados
            const envio = await fetch(alphaFormVars.ajaxurl, {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams({
                    action: 'alphaform_send_integrations',
                    nonce: alphaFormVars.nonce,
                    ...dadosMapeados,
                    post_id: postId,
                    widget_id: widgetId,
                    listaId: listaId,
                    listaIdMC: listaIdMC,
                    actions: JSON.stringify(actions) // aqui envia as integrações selecionadas
                })
            });

            const resultado = await envio.json();

            if (resultado.success && redirectUrl)
                window.location.href = redirectUrl;
            else if (!resultado.success) {
                console.warn('[AlphaForm] resultado:', resultado.data);
                const erro = typeof json.data === 'string' ? json.data : 'Erro no envio.';
                alert('[AlphaForm] Falha no envio: ' + erro);
                esconderLoader();
                return
            }

            esconderLoader();

        });
    });



    showField(currentIndex);

});