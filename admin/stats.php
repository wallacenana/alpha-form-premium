<?php
if (!defined('ABSPATH')) exit;

function alpha_form_plugin_image($path, $alt = '')
{
    // phpcs:disable PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage
    printf(
        '<img src="%s" alt="%s" loading="lazy" decoding="async" />',
        esc_url(ALPHA_FORM_PLUGIN_URL . ltrim($path, '/')),
        esc_attr($alt)
    );
    // phpcs:enable PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage

}

?>

<div class="alpha-form-wrap">
    <div class="alpha-justify-space-between">
        <div class="alpha_form_esquerda">
            <div class="alpha-justify-space-between alpha-align-center">
                <span class="titulo">
                    <h1>Estatísticas do Alpha Form</h1>
                    <p class="alpha_form_descricao">Veja o que está acontecendo no seu negócio agora </p>
                </span>
                <div class="alpja-x">
                    <select id="alpha-form-filter" multiple>
                    </select>
                </div>
            </div>
            <div class="alpha-card d-flex ">
                <div class="alpha-card-content bg-branco p22">
                    <div class="d-flex alpha-flex-wrap">
                        <div class="d-flex">
                            <?php alpha_form_plugin_image('assets/img/leads.svg', 'total'); ?>
                            <p class="text-body-tertiary">Entradas Recentes</p>
                        </div>
                        <p class="text-primary fs-6"><span id="stat-today">0</span> <span class="fs-8 text-body lh-lg">Leads no periodo</span></p>
                    </div>
                </div>
                <div class="alpha-card-content bg-branco border-1 p22">
                    <div class="d-flex alpha-flex-wrap">
                        <div class="d-flex">
                            <?php alpha_form_plugin_image('assets/img/calendar.svg', 'concluido'); ?>
                            <p class="text-body-tertiary">Total</p>
                        </div>
                        <p class="text-info fs-6"><span id="stat-total">0</span> <span class="fs-8 text-body lh-lg">Cadastros no total</span></p>
                    </div>
                </div>
                <div class="alpha-card-content">
                    <div class="d-flex alpha-flex-wrap">
                        <h3>Métricas de analise</h3>
                        <div class="lista">
                            <div class="d-flex justify-content-between">
                                <span class="fw-normal fs-9 mx-1">
                                    <span class="weight-600"> 1. </span>
                                    Cadastros no mês
                                </span>
                                <span class="weight-600">(<span id="stat-month">0</span>)</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="fw-normal fs-9 mx-1">
                                    <span class="weight-600"> 2. </span>
                                    Cadastros na semana
                                </span>
                                <span class="weight-600">(<span id="stat-week">0</span>)
                                </span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="fw-normal fs-9 mx-1">
                                    <span class="weight-600"> 3. </span>
                                    Cadastros hoje
                                </span>
                                <span class="weight-600">(<span id="stat-today">0</span>)
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="section-devices">
                <div class="alpha-justify-space-between alpha-align-center py20">
                    <span class="titulo">
                        <h2>Dispositivos</h2>
                        <p class="alpha_form_descricao">Dispositivos que passaram pelos seus formulários</p>
                    </span>
                </div>
                <div class="section-content d-flex">
                    <div class="w-66 w-sm-100">
                        <div class="devices d-flex">
                            <div class="content-devices">
                                <svg class="svg-inline--fa fa-square fs-11 me-2 text-success" data-fa-transform="up-2" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="square" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" data-fa-i2svg="" style="transform-origin: 0.4375em 0.375em;">
                                    <g transform="translate(224 256)">
                                        <g transform="translate(0, -64)  scale(1, 1)  rotate(0 0 0)">
                                            <path fill="currentColor" d="M0 96C0 60.7 28.7 32 64 32H384c35.3 0 64 28.7 64 64V416c0 35.3-28.7 64-64 64H64c-35.3 0-64-28.7-64-64V96z" transform="translate(-224 -256)"></path>
                                        </g>
                                    </g>
                                </svg> Mobile
                                <h3 id="stat-mobile" class="device-valor"></h3>
                            </div>
                            <div class="content-devices">
                                <svg class="svg-inline--fa fa-square fs-11 me-2 text-primary" data-fa-transform="up-2" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="square" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" data-fa-i2svg="" style="transform-origin: 0.4375em 0.375em;">
                                    <g transform="translate(224 256)">
                                        <g transform="translate(0, -64)  scale(1, 1)  rotate(0 0 0)">
                                            <path fill="currentColor" d="M0 96C0 60.7 28.7 32 64 32H384c35.3 0 64 28.7 64 64V416c0 35.3-28.7 64-64 64H64c-35.3 0-64-28.7-64-64V96z" transform="translate(-224 -256)"></path>
                                        </g>
                                    </g>
                                </svg> Desktop
                                <h3 id="stat-desktop" class="device-valor"></h3>
                            </div>
                            <div class="content-devices">
                                <svg class="svg-inline--fa fa-square fs-11 me-2 text-warning" data-fa-transform="up-2" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="square" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" data-fa-i2svg="" style="transform-origin: 0.4375em 0.375em;">
                                    <g transform="translate(224 256)">
                                        <g transform="translate(0, -64)  scale(1, 1)  rotate(0 0 0)">
                                            <path fill="currentColor" d="M0 96C0 60.7 28.7 32 64 32H384c35.3 0 64 28.7 64 64V416c0 35.3-28.7 64-64 64H64c-35.3 0-64-28.7-64-64V96z" transform="translate(-224 -256)"></path>
                                        </g>
                                    </g>
                                </svg> Tablet
                                <h3 id="stat-tablet" class="device-valor"></h3>
                            </div>
                        </div>
                    </div>
                    <div class="alpha-chart-container w-33 w-sm-100">
                        <canvas id="alphaFormDevices"></canvas>
                    </div>
                </div>
            </div>
            <div class="section-duration">
                <div class="alpha-justify-space-between alpha-align-center py20">
                    <span class="titulo">
                        <h2>Tempo de tela</h2>
                        <p class="alpha_form_descricao">Média de tempo de conexão nas páginas no periodo e formulário selecionados</p>
                    </span>
                </div>
                <div class="section-content d-flex">
                    <div class="w-66 w-sm-100">
                        <div class="devices d-flex">
                            <div class="content-devices">
                                <svg class="svg-inline--fa fa-square fs-11 me-2 text-info" data-fa-transform="up-2" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="square" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" data-fa-i2svg="" style="transform-origin: 0.4375em 0.375em;">
                                    <g transform="translate(224 256)">
                                        <g transform="translate(0, -64)  scale(1, 1)  rotate(0 0 0)">
                                            <path fill="currentColor" d="M0 96C0 60.7 28.7 32 64 32H384c35.3 0 64 28.7 64 64V416c0 35.3-28.7 64-64 64H64c-35.3 0-64-28.7-64-64V96z" transform="translate(-224 -256)"></path>
                                        </g>
                                    </g>
                                </svg> Tempo minimo
                                <h3 id="tempo-min" class="device-valor"></h3>
                            </div>
                            <div class="content-devices">
                                <svg class="svg-inline--fa fa-square fs-11 me-2 text-info-light" data-fa-transform="up-2" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="square" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" data-fa-i2svg="" style="transform-origin: 0.4375em 0.375em;">
                                    <g transform="translate(224 256)">
                                        <g transform="translate(0, -64)  scale(1, 1)  rotate(0 0 0)">
                                            <path fill="currentColor" d="M0 96C0 60.7 28.7 32 64 32H384c35.3 0 64 28.7 64 64V416c0 35.3-28.7 64-64 64H64c-35.3 0-64-28.7-64-64V96z" transform="translate(-224 -256)"></path>
                                        </g>
                                    </g>
                                </svg> Tempo máximo
                                <h3 id="tempo-max" class="device-valor"></h3>
                            </div>
                            <div class="content-devices">
                                <svg class="svg-inline--fa fa-square fs-11 me-2 text-warning-light" data-fa-transform="up-2" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="square" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" data-fa-i2svg="" style="transform-origin: 0.4375em 0.375em;">
                                    <g transform="translate(224 256)">
                                        <g transform="translate(0, -64)  scale(1, 1)  rotate(0 0 0)">
                                            <path fill="currentColor" d="M0 96C0 60.7 28.7 32 64 32H384c35.3 0 64 28.7 64 64V416c0 35.3-28.7 64-64 64H64c-35.3 0-64-28.7-64-64V96z" transform="translate(-224 -256)"></path>
                                        </g>
                                    </g>
                                </svg> Média total
                                <h3 id="tempo-avg" class="device-valor"></h3>
                            </div>
                        </div>
                    </div>
                    <div class="alpha-chart-container w-33 w-sm-100">
                        <canvas id="alphaFormDuration"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="alpha_form_direita">
            <div class="section-views bg-branco p-20 br-10">
                <div class="alpha-justify-space-between alpha-align-center py20">
                    <span class="titulo">
                        <h2>Métricas de conclusão</h2>
                        <p class="alpha_form_descricao">Veja o que está acontecendo no seu negócio agora </p>
                    </span>
                    <div class="py20">
                        <select id="alphaFormDateRange">
                            <option value="7">Últimos 7 dias</option>
                            <option value="15" selected>Últimos 15 dias</option>
                            <option value="30">Últimos 30 dias</option>
                            <option value="90">Últimos 3 meses</option>
                            <option value="180">Últimos 6 meses</option>
                            <option value="custom">Personalizado</option>
                        </select>
                    </div>
                </div>
                <div id="customDateFields" style="display: none;">
                    <input type="date" id="dateStart">
                    <input type="date" id="dateEnd">
                    <button id="applyDateRange">Aplicar</button>
                </div>

                <div class="section-content mb-5">
                    <div class="views d-flex">
                        <div class="content-devices">
                            <svg class="svg-inline--fa fa-square fs-11 me-2 text-primary" data-fa-transform="up-2" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="square" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" data-fa-i2svg="" style="transform-origin: 0.4375em 0.375em;">
                                <g transform="translate(224 256)">
                                    <g transform="translate(0, -64)  scale(1, 1)  rotate(0 0 0)">
                                        <path fill="currentColor" d="M0 96C0 60.7 28.7 32 64 32H384c35.3 0 64 28.7 64 64V416c0 35.3-28.7 64-64 64H64c-35.3 0-64-28.7-64-64V96z" transform="translate(-224 -256)"></path>
                                    </g>
                                </g>
                            </svg> Page View
                            <h3 id="stat-page_view" class="device-valor"></h3>
                        </div>
                        <div class="content-devices">
                            <svg class="svg-inline--fa fa-square fs-11 me-2 text-warning" data-fa-transform="up-2" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="square" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" data-fa-i2svg="" style="transform-origin: 0.4375em 0.375em;">
                                <g transform="translate(224 256)">
                                    <g transform="translate(0, -64)  scale(1, 1)  rotate(0 0 0)">
                                        <path fill="currentColor" d="M0 96C0 60.7 28.7 32 64 32H384c35.3 0 64 28.7 64 64V416c0 35.3-28.7 64-64 64H64c-35.3 0-64-28.7-64-64V96z" transform="translate(-224 -256)"></path>
                                    </g>
                                </g>
                            </svg> Formulários iniciados
                            <h3 id="stat-form_iniciados" class="device-valor"></h3>
                        </div>
                        <div class="content-devices">
                            <svg class="svg-inline--fa fa-square fs-11 me-2 text-success" data-fa-transform="up-2" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="square" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" data-fa-i2svg="" style="transform-origin: 0.4375em 0.375em;">
                                <g transform="translate(224 256)">
                                    <g transform="translate(0, -64)  scale(1, 1)  rotate(0 0 0)">
                                        <path fill="currentColor" d="M0 96C0 60.7 28.7 32 64 32H384c35.3 0 64 28.7 64 64V416c0 35.3-28.7 64-64 64H64c-35.3 0-64-28.7-64-64V96z" transform="translate(-224 -256)"></path>
                                    </g>
                                </g>
                            </svg> Concluidos
                            <h3 id="stat-form_concluidos" class="device-valor"></h3>
                        </div>
                        <div class="content-devices">
                            <svg class="svg-inline--fa fa-square fs-11 me-2 text-info" data-fa-transform="up-2" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="square" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" data-fa-i2svg="" style="transform-origin: 0.4375em 0.375em;">
                                <g transform="translate(224 256)">
                                    <g transform="translate(0, -64)  scale(1, 1)  rotate(0 0 0)">
                                        <path fill="currentColor" d="M0 96C0 60.7 28.7 32 64 32H384c35.3 0 64 28.7 64 64V416c0 35.3-28.7 64-64 64H64c-35.3 0-64-28.7-64-64V96z" transform="translate(-224 -256)"></path>
                                    </g>
                                </g>
                            </svg> Taxa de conversão
                            <h3 id="stat-conversao" class="device-valor"></h3>
                        </div>
                    </div>
                </div>

                <div class="alpha-chart-container">
                    <canvas id="alphaFormChart"></canvas>
                </div>
            </div>
            <div class="session-location bg-branco p-20 br-10">
                <span class="titulo">
                    <h2>Geolocalização</h2>
                    <p class="alpha_form_descricao">Localizações que mais preencheram os formulários</p>
                </span>
                <div class="alpha-chart-container ">
                    <canvas id="barStates"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>