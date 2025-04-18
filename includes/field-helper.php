<?php

namespace AlphaFormPremium\Helpers;

class FieldHelper
{
    public static function get_available_fields( $widget ) {
        if ( ! $widget instanceof \Elementor\Widget_Base ) {
            return [];
        }
    
        $settings = method_exists( $widget, 'get_settings_for_display' )
            ? $widget->get_settings_for_display()
            : ( method_exists( $widget, 'get_settings' ) ? $widget->get_settings() : [] );
    
        // Garante que settings é um array
        if ( ! is_array( $settings ) || empty( $settings['form_fields'] ) ) {
            return [];
        }
    
        $fields = [];
        foreach ( $settings['form_fields'] as $field ) {
            if ( ! empty( $field['custom_id'] ) && ! empty( $field['field_label'] ) ) {
                $fields[ $field['custom_id'] ] = $field['field_label'];
            }
        }
    
        return $fields;
    }
    
}
