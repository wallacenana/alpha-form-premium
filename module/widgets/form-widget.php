<?php

namespace AlphaFormPremium\Module\Widgets;

use Elementor\Widget_Base;

require_once __DIR__ . '/controls/form-fields.php';
require_once __DIR__ . '/controls/form-botao.php';
require_once __DIR__ . '/controls/form-visualizacoes.php';
require_once __DIR__ . '/controls/form-depois-do-envio.php';
require_once __DIR__ . '/controls/style-titulo.php';
require_once __DIR__ . '/controls/style-input.php';
require_once __DIR__ . '/controls/style-button.php';
require_once __DIR__ . '/controls/style-progress.php';
require_once __DIR__ . '/controls/style-descricao.php';
require_once __DIR__ . '/controls/style-label.php';
require_once __DIR__ . '/controls/style-progress-buttons.php';
require_once __DIR__ . '/controls/style-box.php';
require_once __DIR__ . '/controls/render-fields.php';

use function AlphaFormPremium\Module\Controls\register_form_fields_controls;
use function AlphaFormPremium\Module\Controls\register_form_botao_controls;
use function AlphaFormPremium\Module\Controls\register_form_visualizacoes_controls;
use function AlphaFormPremium\Module\Controls\register_form_depois_do_envio_controls;
use function AlphaFormPremium\Module\Controls\register_form_style_titulo_controls;
use function AlphaFormPremium\Module\Controls\register_style_botao_controls;
use function AlphaFormPremium\Module\Controls\register_style_input_controls;
use function AlphaFormPremium\Module\Controls\register_style_progress_controls;
use function AlphaFormPremium\Module\Controls\register_style_descricao_controls;
use function AlphaFormPremium\Module\Controls\register_form_style_label_controls;
use function AlphaFormPremium\Module\Controls\register_style_box_controls;
use function AlphaFormPremium\Module\Controls\register_style_progress_buttons_controls;
use function AlphaFormPremium\Module\Controls\render_alpha_form_fields;


if (!defined('ABSPATH')) exit;

class Form_Widget extends Widget_Base
{

    public function get_name()
    {
        return 'alpha_form';
    }

    public function get_title()
    {
        return esc_html__('Alpha Form', 'alpha-form-premium');
    }

    public function get_icon()
    {
        return 'eicon-form-horizontal';
    }

    public function get_categories()
    {
        return ['general'];
    }


    protected function register_controls()
    {
        register_form_fields_controls($this);
        register_form_botao_controls($this);
        register_form_visualizacoes_controls($this);
        register_form_depois_do_envio_controls($this);
        register_style_box_controls($this);
        register_form_style_titulo_controls($this);
        register_style_descricao_controls($this);
        register_style_input_controls($this);
        register_style_botao_controls($this);
        register_form_style_label_controls($this);
        register_style_progress_buttons_controls($this);
        register_style_progress_controls($this);
        
    }

    protected function render()
    {
        $settings = $this->get_settings_for_display();
        render_alpha_form_fields($settings, $this->get_id());
    }

    
    protected function content_template(): void {}
    
}
