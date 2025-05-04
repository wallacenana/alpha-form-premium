const wrapper = document.querySelector('.alpha-form-wrapper');
if (wrapper) {
    // Prepara variáveis
    let currentIndex = 0;
    let sessionId;
    let latitude = '';
    let longitude = '';
    let geoTimeout;

    const fields = wrapper.querySelectorAll('.alpha-form-field');
    const allNextButtons = wrapper.querySelectorAll('.alpha-form-next-button, .alpha-form-next-button-x');
    const prevBtn = wrapper.querySelector('.alpha-form-prev-button-x');
    const pageViewSavedKey = 'alpha_form_page_view_saved_' + sessionId;

    function generateSessionId() {
        return 'afp_' + Math.random().toString(36).substr(2, 9) + Date.now();
    }

    function mostrarLoader() {
        document.getElementById('alphaform-overlay').style.display = 'flex';
    }

    function esconderLoader() {
        document.getElementById('alphaform-overlay').style.display = 'none';
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

        // Validação adicional para campos mascarados
        const maskType = input?.dataset.mask;
        if (isRequired && maskType && value) {
            let digits = value.replace(/\D/g, '');

            if (maskType === 'cpf' && digits.length !== 11) {
                showError(input, 'CPF inválido.');
                return false;
            }

            if (maskType === 'cnpj' && digits.length !== 14) {
                showError(input, 'CNPJ inválido.');
                return false;
            }

            if (maskType === 'cep' && digits.length !== 8) {
                showError(input, 'CEP inválido.');
                return false;
            }

            if (maskType === 'credit_card' && digits.length !== 16) {
                showError(input, 'Número de cartão inválido.');
                return false;
            }

            if (maskType === 'currency' && !/^\d+,\d{2}$/.test(value)) {
                showError(input, 'Valor inválido.');
                return false;
            }
        }

        return true;
    }

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

    function salvarPageView() {
        const dummyField = document.querySelector('.alpha-form');
        if (dummyField) {
            dummyField.dataset.geoLat = latitude || '';
            dummyField.dataset.geoLng = longitude || '';

            saveFieldData(dummyField);
            localStorage.setItem(pageViewSavedKey, '1');
        }
    }

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

        const extraData = {
            duration: Math.round(performance.now() / 1000), // tempo em segundos desde o carregamento da página
            lang: navigator.language || '',
            platform: navigator.platform || '',
            device_type: /Mobi|Android/i.test(navigator.userAgent) ? 'mobile' : 'desktop',
            timezone: Intl.DateTimeFormat().resolvedOptions().timeZone || '',
            user_agent: navigator.userAgent || '',
            ip_address: '', // será captado pelo backend se não estiver usando serviço externo
            browser: (() => {
                const ua = navigator.userAgent;
                if (ua.includes("Chrome")) return "Chrome";
                if (ua.includes("Firefox")) return "Firefox";
                if (ua.includes("Safari") && !ua.includes("Chrome")) return "Safari";
                if (ua.includes("Edge")) return "Edge";
                return "Outro";
            })()
        };

        const data = {
            action: 'alpha_form_save_response',
            form_id: formId,
            session_id: sessionId,
            nonce: alphaFormVars.nonce,
            widgetId: widgetId,
            postId: postId,
            latitude: latitude || "",
            longitude: longitude || "",
            response: JSON.stringify({
                [input.name]: value
            }),
            ...extraData
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

    function updateProgressBar(index) {
        const progressBar = document.querySelector('.alpha-form-progress-fill');
        const progressText = document.querySelector('.alpha-form-progress-text');

        if (!progressBar || !progressText) return;

        const currentField = fields[index];
        const isLastStep = currentField.classList.contains('alpha-form-final');

        if (isLastStep) {
            progressBar.style.width = '100%';
            progressText.textContent = '100%';
            return;
        }

        const validSteps = Array.from(fields).filter(f => {
            const isHidden = !!f.querySelector('input[type="hidden"]');
            return !isHidden && getComputedStyle(f).display !== 'none' && !f.classList.contains('final');
        });

        const visibleIndex = validSteps.indexOf(currentField);

        // Se estiver no primeiro campo, a porcentagem é 0
        if (visibleIndex === 0) {
            progressBar.style.width = '0%';
            progressText.textContent = '0%';
            return;
        }

        const total = validSteps.length;
        const percentage = Math.round((visibleIndex / total) * 100);

        progressBar.style.width = percentage + '%';
        progressText.textContent = percentage + '%';
    }


    function applyMasks() {
        document.querySelectorAll('[data-mask]').forEach(function (input) {
            const maskType = input.dataset.mask;

            input.addEventListener('input', function (e) {
                let value = input.value.replace(/\D/g, ''); // só números

                if (maskType === 'cpf') {
                    value = value.substring(0, 11);

                    if (value.length <= 3) {
                        value = value.replace(/(\d{1,3})/, '$1');
                    } else if (value.length <= 6) {
                        value = value.replace(/(\d{3})(\d{1,3})/, '$1.$2');
                    } else if (value.length <= 9) {
                        value = value.replace(/(\d{3})(\d{3})(\d{1,3})/, '$1.$2.$3');
                    } else {
                        value = value.replace(/(\d{3})(\d{3})(\d{3})(\d{1,2})/, '$1.$2.$3-$4');
                    }
                }

                if (maskType === 'cnpj') {
                    value = value.substring(0, 14);

                    if (value.length <= 2) {
                        value = value.replace(/(\d{1,2})/, '$1');
                    } else if (value.length <= 5) {
                        value = value.replace(/(\d{2})(\d{1,3})/, '$1.$2');
                    } else if (value.length <= 8) {
                        value = value.replace(/(\d{2})(\d{3})(\d{1,3})/, '$1.$2.$3');
                    } else if (value.length <= 12) {
                        value = value.replace(/(\d{2})(\d{3})(\d{3})(\d{1,4})/, '$1.$2.$3/$4');
                    } else {
                        value = value.replace(/(\d{2})(\d{3})(\d{3})(\d{4})(\d{1,2})/, '$1.$2.$3/$4-$5');
                    }
                }

                if (maskType === 'cep') {
                    value = value.substring(0, 8);

                    if (value.length <= 5) {
                        value = value.replace(/(\d{1,5})/, '$1');
                    } else {
                        value = value.replace(/(\d{5})(\d{1,3})/, '$1-$2');
                    }
                }

                if (maskType === 'phone') {
                    value = value.substring(0, 11);

                    if (value.length <= 2) {
                        value = value.replace(/(\d{1,2})/, '($1');
                    } else if (value.length <= 6) {
                        value = value.replace(/(\d{2})(\d{1,4})/, '($1) $2');
                    } else if (value.length <= 10) {
                        value = value.replace(/(\d{2})(\d{4})(\d{1,4})/, '($1) $2-$3');
                    } else {
                        value = value.replace(/(\d{2})(\d{5})(\d{1,4})/, '($1) $2-$3');
                    }
                }

                if (maskType === 'currency') {
                    value = value.replace(/\D/g, ''); // remove tudo que não é número

                    if (value.length > 0) {
                        value = (parseFloat(value) / 100).toLocaleString('pt-BR', {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        });
                    } else {
                        value = '';
                    }
                }


                input.value = value;
            });
        });
    }

    async function handleSubmit(form) {
        const currentField = fields[currentIndex];
        const isValid = validateField(currentField);
        if (!isValid) return;

        mostrarLoader();

        const postId = parseInt(document.querySelector('[data-elementor-id]')?.dataset.elementorId || 0);
        const widgetId = form.dataset.widgetId;
        const raw = {};

        const inputs = form.querySelectorAll('input, textarea, select');
        const data = {};

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

        let redirectUrl = form.dataset.redirect;

        if (redirectUrl && redirectUrl.includes('[field_')) {
            redirectUrl = redirectUrl.replace(/\[field_([^\]]+)\]/g, (match, key) => {
                const input = document.querySelector(`[data-shortcode="field_${key}"]`);
                const value = input?.value || '';
                return encodeURIComponent(value);
            });
        }

        const rens = await fetch(alphaFormVars.ajaxurl, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({
                action: 'alphaform_get_widget_actions',
                post_id: postId,
                widget_id: widgetId,
                nonce: alphaFormVars.nonce
            })
        });

        const json = await rens.json();
        if (!json.success) {
            console.error('[AlphaForm] Erro ao buscar ações:', json);
            return;
        }

        const actions = json.data.actions || [];
        const map = json.data.map || {};
        const listaId = json.data.listaId || {};
        const listaIdMC = json.data.listaIdMC || {};
        const webhook_url = json.data.webhook_url || '';

        const dadosMapeados = {};

        Object.entries(map).forEach(([chave, idCampo]) => {
            if (!idCampo) return;

            const inputEl = document.querySelector(`[id="${idCampo}"]`);

            if (inputEl && inputEl.value !== '') {
                dadosMapeados[chave] = inputEl.value;
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
                webhook_url: webhook_url,
                session_id: sessionId,
                is_final_submission: 1,
                actions: JSON.stringify(actions),
                ...data
            })
        });

        const resultado = await envio.json();

        if (resultado.success && redirectUrl)
            window.location.href = redirectUrl;
        else if (!resultado.success) {
            const erro = typeof json.data === 'string' ? json.data : 'Erro no envio.';
            alert('[AlphaForm] Falha no envio: ' + erro);
            esconderLoader();
            return
        }

        esconderLoader();

    }

    document.addEventListener('DOMContentLoaded', function () {
        if (!wrapper) return;

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

                    saveFieldData(field);

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
        wrapper.querySelectorAll('select').forEach((select) => {
            select.addEventListener('change', () => {
                const field = select.closest('.alpha-form-field');
                const index = Array.from(fields).indexOf(field);

                saveFieldData(field);

                if (index >= 0 && index < fields.length - 1) {
                    currentIndex = index + 1;
                    showField(currentIndex);
                }
            });
        });

        const sessionKey = 'alpha_form_session_id';
        const sessionExpireKey = 'alpha_form_session_expire';
        const now = Date.now();

        // Verifica se já existe uma sessão válida
        sessionId = localStorage.getItem(sessionKey);
        let sessionExpire = localStorage.getItem(sessionExpireKey);

        if (!sessionId || !sessionExpire || now > parseInt(sessionExpire, 10)) {
            // Se não existir ou expirou, gera nova sessão
            sessionId = generateSessionId();
            localStorage.setItem(sessionKey, sessionId);
            localStorage.setItem(sessionExpireKey, now + 2 * 60 * 1000);
        }

        const enableGeo = form?.dataset.enableGeolocation === 'true';

        if (enableGeo && !localStorage.getItem(pageViewSavedKey)) {
            if (!navigator.geolocation) {
                salvarPageView();
            } else {
                geoTimeout = setTimeout(() => {
                    salvarPageView();
                }, 3000); // 3 segundos

                navigator.geolocation.getCurrentPosition(
                    function (position) {
                        clearTimeout(geoTimeout);

                        latitude = position.coords.latitude;
                        longitude = position.coords.longitude;

                        // Salva também no localStorage para ser usado depois
                        localStorage.setItem('alpha_form_user_latitude', latitude);
                        localStorage.setItem('alpha_form_user_longitude', longitude);

                        salvarPageView();
                    },
                    function (error) {
                        clearTimeout(geoTimeout);
                        salvarPageView();
                    }
                );

            }
        }

        applyMasks();
        showField(currentIndex);
    });


    window.addEventListener('load', function () {
        const sessionId = localStorage.getItem('alpha_form_session_id');
        const latitude = localStorage.getItem('alpha_form_user_latitude');
        const longitude = localStorage.getItem('alpha_form_user_longitude');

        if (!sessionId) {
            console.warn('[AlphaFormGeo] sem session');
            return;
        }
        if (latitude === null) {
        }
        if (longitude === null) {
        }

        const geoSavedKey = 'alpha_form_geo_saved_' + sessionId;
        if (localStorage.getItem(geoSavedKey)) {
        }

        const data = {
            action: 'alpha_form_save_geo',
            nonce: alphaFormVars.nonce,
            session_id: sessionId,
            latitude: latitude,
            longitude: longitude,
            latitude: latitude,
            longitude: longitude
        };

        fetch(alphaFormVars.ajaxurl, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams(data)
        })
            .then(res => res.json())
            .then(response => {
                if (response.success) {
                    localStorage.setItem(geoSavedKey, '1');
                } else {
                }
            }).catch(error => {
                console.error('Alpha Form: erro na requisição fetch.', error);
            });


        const form = document.querySelector('form[data-auto-submit=""]');
        const steps = form ? Array.from(form.querySelectorAll('.alpha-form-step')) : [];

        if (form && steps.length) {
            const lastStep = steps[steps.length - 1];
            const nextButton = lastStep.querySelector('.alpha-form-next-button');
            const input = lastStep.querySelector('input, select, textarea');

            if (nextButton) {
                nextButton.setAttribute('type', 'submit');
                nextButton.classList.add('alpha-form-submit');
            } else if (input) {
                const type = input.type;

                if (type === 'radio' || type === 'checkbox') {
                    lastStep.querySelectorAll(`input[type="${type}"]`).forEach(el => {
                        el.addEventListener('change', () => handleSubmit(form));
                    });
                } else if (input.tagName.toLowerCase() === 'select') {
                    input.addEventListener('change', () => handleSubmit(form));
                } else {
                    input.addEventListener('keydown', e => {
                        if (e.key === 'Enter') handleSubmit(form);
                    });
                }
            }
        }

        document.querySelectorAll('.alpha-form').forEach(form => {
            form.addEventListener('submit', async function (e) {
                console.log(form)
                e.preventDefault();
                await handleSubmit(form)
            });
        });
    });
}
else
    console.log("Alpha Form Premium - Admin carregado com sucesso")
