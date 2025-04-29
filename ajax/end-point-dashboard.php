<?php

add_action('wp_ajax_alphaform_get_form_widget_count', 'alphaform_get_form_widget_count_handle');

function alphaform_get_form_widget_count_handle()
{
    check_ajax_referer('alpha_form_nonce', 'nonce');

    global $wpdb;
    $response = [];
    $table = $wpdb->prefix . 'alpha_form_responses';

    // Quantidade de formulários únicos (baseado em widget_id)
    $response['total_forms'] = (int) $wpdb->get_var(
        $wpdb->prepare(
            "
            SELECT COUNT(DISTINCT widget_id)
            FROM %i
            ",
            $table
        )
    );


    // Total de respostas
    $response['total_responses'] = (int) $wpdb->get_var(
        $wpdb->prepare(
            "
            SELECT COUNT(*)
            FROM %i
            ",
            $table
        )
    );


    // Última submissão
    $response['total_integrations'] = (int) $wpdb->get_var(
        $wpdb->prepare(
            "
            SELECT COUNT(*)
            FROM %i
            ",
            $wpdb->prefix . 'alpha_form_integrations'
        )
    );


    // Retorno final
    wp_send_json_success($response);
}


add_action('wp_ajax_alphaform_get_dashboard_stats', 'alphaform_get_dashboard_stats');

function alphaform_get_dashboard_stats()
{
    check_ajax_referer('alpha_form_nonce', 'nonce');

    global $wpdb;
    $table = $wpdb->prefix . 'alpha_form_responses';

    $widget_ids = isset($_GET['widget_ids']) ? array_map('sanitize_text_field', (array) wp_unslash($_GET['widget_ids'])) : [];

    if ($widget_ids)
        $placeholders = implode(',', array_fill(0, count($widget_ids), '%s'));
    else
        $placeholders = "";
    // Datas principais
    $startThisWeek = gmdate('Y-m-d', strtotime('last sunday'));
    $endThisWeek   = gmdate('Y-m-d', strtotime('next saturday'));

    $startLastWeek = gmdate('Y-m-d', strtotime('last sunday -7 days'));
    $endLastWeek   = gmdate('Y-m-d', strtotime('last saturday'));

    $startMonth = gmdate('Y-m-01');
    $startLastMonth = gmdate('Y-m-01', strtotime('first day of last month'));
    $endLastMonth   = gmdate('Y-m-t', strtotime('last month'));

    $inicio = sanitize_text_field(isset($_GET['inicio']) ? wp_unslash($_GET['inicio']) : gmdate('Y-m-d', strtotime('-15 days'))) . ' 00:00:00';
    $fim    = sanitize_text_field(isset($_GET['fim']) ? wp_unslash($_GET['fim']) : gmdate('Y-m-d')) . ' 23:59:59';


    // Submissões
    $today = alphaform_get_results(gmdate('Y-m-d') . ' 00:00:00', gmdate('Y-m-d') . ' 23:59:59', $widget_ids);
    $week       = alphaform_get_results($startThisWeek, $endThisWeek, $widget_ids);
    $last_week  = alphaform_get_results($startLastWeek, $endLastWeek, $widget_ids);
    $month      = alphaform_get_results($startMonth, '', $widget_ids);
    $last_month = alphaform_get_results($startLastMonth, $endLastMonth, $widget_ids);
    $visits     = alphaform_get_results($inicio, $fim, $widget_ids, 'COUNT(*)');
    $leads      = alphaform_get_results($inicio, $fim, $widget_ids, 'COUNT(*)');
    $totalgeral = alphaform_get_results("", "", $widget_ids, 'COUNT(*)');
    $page_views = alphaform_get_results($inicio, $fim, $widget_ids, 'COUNT(*)', ['page_view' => 1, 'start_form' => 0]);
    $start_forms = alphaform_get_results($inicio, $fim, $widget_ids, 'COUNT(*)', ['start_form' => 1]);
    $totalconcluido = alphaform_get_results($inicio, $fim, $widget_ids, 'COUNT(*)', ['concluido' => 1]);


    // Submissões por dia (últimos 15)
    $start = new DateTime($inicio);
    $end = new DateTime($fim);
    $interval = new DateInterval('P1D');
    $period = new DatePeriod($start, $interval, $end);

    $labels = [];
    $values_total = [];
    $values_concluido = [];
    $formularios_iniciados = [];

    foreach ($period as $date) {
        $dia = $date->format('Y-m-d');
        $labels[] = $dia;

        $prepar = '';
        $params = [$dia];

        $where_widgets = '';
        if (!empty($widget_ids)) {
            $widget_placeholders = implode(',', array_fill(0, count($widget_ids), '%s'));
            $where_widgets = "AND widget_id IN ($widget_placeholders)";
            $params = array_merge($params, $widget_ids);
        }

        // phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared
        $total = $wpdb->get_var(
            $wpdb->prepare(
                "
        SELECT COUNT(*) FROM %i
        WHERE DATE(submitted_at) = %s
        $where_widgets
        AND page_view = 1
        AND start_form = 0
        ",
                $table,
                ...$params
            )
        );

        $concluidos = $wpdb->get_var(
            $wpdb->prepare(
                "
                SELECT COUNT(*) FROM %i
                WHERE DATE(submitted_at) = %s
                $where_widgets
                AND concluido = 1
                ",
                $table,
                ...$params
            )
        );

        $forms_iniciados = $wpdb->get_var(
            $wpdb->prepare(
                "
                SELECT COUNT(*) FROM $table
                WHERE DATE(submitted_at) = %s
                AND page_view = 1
                AND start_form = 1
                $prepar
                ",
                ...$params
            )
        );

        // phpcs:enable WordPress.DB.PreparedSQL.InterpolatedNotPrepared
        $values_total[] = (int) $total;
        $values_concluido[] = (int) $concluidos;
        $formularios_iniciados[] = (int) $forms_iniciados;
    }


    $params = [$inicio, $fim];

    if (!empty($widget_ids)) {
        $widget_placeholders = implode(',', array_fill(0, count($widget_ids), '%s'));
        $where_widget = " AND widget_id IN ($widget_placeholders)";
        $params = array_merge($params, $widget_ids);
    } else {
        $where_widget = '';
    }

    // phpcs:disable WordPress.DB.PreparedSQLPlaceholders.ReplacementsWrongNumber
    $total_concluidos = $wpdb->get_var(
        $wpdb->prepare(
            "
            SELECT COUNT(*)
            FROM %i
            WHERE concluido = 1
            AND submitted_at BETWEEN %s AND %s
            ",
            $table,
            ...$params
        )
    );
    // phpcs:enable WordPress.DB.PreparedSQLPlaceholders.ReplacementsWrongNumber

    $widget_filter = '';
    $params = [$inicio, $fim];

    if (!empty($widget_ids)) {
        $widget_placeholders = implode(',', array_fill(0, count($widget_ids), '%s'));
        $widget_filter = " AND widget_id IN ($widget_placeholders)";
        $params = array_merge($params, $widget_ids);
    }

    // phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared
    // phpcs:disable WordPress.DB.PreparedSQLPlaceholders.ReplacementsWrongNumber
    $states = $wpdb->get_results(
        $wpdb->prepare(
            "
            SELECT 
                state,
                COUNT(*) as total
            FROM %i
            WHERE submitted_at BETWEEN %s AND %s
              AND state IS NOT NULL
              AND state != ''
              $widget_filter
            GROUP BY state
            ORDER BY total DESC
            LIMIT 10
            ",
            $table,
            ...$params
        )
    );
    // phpcs:enable WordPress.DB.PreparedSQL.InterpolatedNotPrepared
    // phpcs:enable WordPress.DB.PreparedSQLPlaceholders.ReplacementsWrongNumber
    $states_data = array_map(function ($row) {
        return [
            'state' => $row->state,
            'total' => (int) $row->total
        ];
    }, $states);


    // Função de variação
    function calc_variation($current, $previous)
    {
        if ($previous == 0) return 100;
        return round((($current - $previous) / $previous) * 100, 1);
    }

    $device_stats = [];
    foreach (['desktop', 'tablet', 'mobile'] as $device) {
        $device_stats[$device] = alphaform_get_results($inicio, $fim, $widget_ids, 'COUNT(*)', ['device_type' => $device]);
    }

    // Tempo de permanência
    $params = [$inicio, $fim];

    // Se tiver widget IDs
    if (!empty($widget_ids)) {
        $widget_placeholders = implode(',', array_fill(0, count($widget_ids), '%s'));
        $where_widgets = "AND widget_id IN ($widget_placeholders)";
        $params = array_merge($params, $widget_ids);
    } else {
        $where_widgets = '';
    }

    // phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared
    // phpcs:disable WordPress.DB.PreparedSQLPlaceholders.ReplacementsWrongNumber
    $duracao_stats = $wpdb->get_row(
        $wpdb->prepare(
            "
        SELECT 
            MAX(duration) as max_duration,
            MIN(duration) as min_duration,
            AVG(duration) as avg_duration
        FROM %i
        WHERE submitted_at BETWEEN %s AND %s
        $where_widgets
        ",
            $table,
            ...$params
        ),
        ARRAY_A
    );

    // phpcs:enable WordPress.DB.PreparedSQL.InterpolatedNotPrepared
    // phpcs:enable WordPress.DB.PreparedSQLPlaceholders.ReplacementsWrongNumber	

    $leads_periodo_atual = $leads; // Já tem isso no código

    // Normalizar datas
    $inicioSemHora = gmdate('Y-m-d', strtotime($inicio));
    $fimSemHora = gmdate('Y-m-d', strtotime($fim));

    // Calcular intervalo de dias
    $inicioDate = new DateTime($inicioSemHora);
    $fimDate = new DateTime($fimSemHora);
    $interval = $inicioDate->diff($fimDate);
    $quantidadeDias = (int) $interval->format('%a');

    // Pegar período anterior
    $inicio_periodo_anterior = gmdate('Y-m-d', strtotime("-{$quantidadeDias} days", strtotime($inicioSemHora))) . ' 00:00:00';
    $fim_periodo_anterior = gmdate('Y-m-d', strtotime("-1 day", strtotime($inicioSemHora))) . ' 23:59:59';


    // 4. Buscar leads do período anterior
    $leads_periodo_anterior = alphaform_get_results($inicio_periodo_anterior, $fim_periodo_anterior, $widget_ids, 'COUNT(*)', [
        'start_form' => 1
    ]);

    // 5. Calcular variação
    function calc_variation_percent($atual, $anterior)
    {
        if ($anterior == 0) {
            return $atual > 0 ? 100 : 0;
        }
        return round((($atual - $anterior) / $anterior) * 100, 1);
    }

    $leads_variation = calc_variation_percent($leads_periodo_atual, $leads_periodo_anterior);

    wp_send_json_success([
        'today' => $today,
        'week' => $week,
        'month' => $month,
        'visits' => $visits,
        'week_variation' => calc_variation($week, $last_week),
        'month_variation' => calc_variation($month, $last_month),
        'labels' => $labels,
        'submissions_per_day' => $values_total,
        'submissions_per_day_concluido' => $values_concluido,
        'formularios_iniciados' => $formularios_iniciados,
        'devices' => $device_stats,
        'duration' => [
            'max' => (int) $duracao_stats['max_duration'],
            'min' => (int) $duracao_stats['min_duration'],
            'avg' => round((float) $duracao_stats['avg_duration'], 1)
        ],
        'concluido' => $total_concluidos,
        'states' => $states_data,
        'page_views' => $page_views,
        'start_forms' => $start_forms,
        'totalconcluido' => $totalconcluido,
        'leads' => $leads,
        'totalgeral' => $totalgeral,
        'variation' => [
            'current' => $leads_periodo_atual,
            'previous' => $leads_periodo_anterior,
            'variation' => $leads_variation
        ],
    ]);
}

function alphaform_get_results($inicio = null, $fim = null, $widget_ids = [], $aggregate = 'COUNT(*)', $extra_conditions = [])
{
    global $wpdb;
    $table = $wpdb->prefix . 'alpha_form_responses';

    $where = '1=1';
    $params = [];

    // Período
    if ($inicio && $fim) {
        $where .= ' AND submitted_at BETWEEN %s AND %s';
        $params[] = $inicio;
        $params[] = $fim;
    }

    // Filtro por widget_id
    if (!empty($widget_ids)) {
        $placeholders = implode(',', array_fill(0, count($widget_ids), '%s'));
        $where .= " AND widget_id IN ($placeholders)";
        $params = array_merge($params, $widget_ids);
    }

    // Condições adicionais (ex: device_type, browser, etc.)
    foreach ($extra_conditions as $column => $value) {
        $where .= " AND `$column` = %s";
        $params[] = $value;
    }

    // phpcs:disable WordPress.DB.PreparedSQLPlaceholders.ReplacementsWrongNumber	
    // phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared		
    // phpcs:disable WordPress.DB.PreparedSQL.NotPrepared	
    $sql = $wpdb->prepare("SELECT COUNT(*) FROM %i WHERE $where", $table, ...$params);
    // phpcs:enable WordPress.DB.PreparedSQLPlaceholders.ReplacementsWrongNumber
    
    return $wpdb->get_var($sql);
    // phpcs:enable WordPress.DB.PreparedSQL.NotPrepared
}


add_action('wp_ajax_alphaform_get_forms_list', 'alphaform_get_forms_list');

function alphaform_get_forms_list()
{
    check_ajax_referer('alpha_form_nonce', 'nonce');

    global $wpdb;
    $table = $wpdb->prefix . 'alpha_form_responses';

    // Busca os formulários distintos com título
    $results = $wpdb->get_results("
        SELECT DISTINCT widget_id, form_id 
        FROM $table 
        WHERE widget_id IS NOT NULL AND widget_id != ''
        ORDER BY submitted_at DESC
        LIMIT 100
    ");

    $data = [];

    foreach ($results as $row) {
        $data[] = [
            'id' => $row->widget_id,
            'text' => $row->form_id
        ];
    }

    wp_send_json_success($data);
}
