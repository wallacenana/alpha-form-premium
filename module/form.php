<?php

namespace AlphaFormPremium\Module;

use AlphaFormPremium\Module\Widgets\Form_Widget;

if (!defined('ABSPATH')) exit;

class Form
{
    public function init()
    {
        // Registra o widget no Elementor
        add_action('elementor/widgets/register', [$this, 'register_widgets']);
    }

    public function register_widgets($widgets_manager)
    {
        require_once __DIR__ . '/widgets/form-widget.php';

        if (class_exists(Form_Widget::class)) {
            $widgets_manager->register(new Form_Widget());
        }
    }

}
