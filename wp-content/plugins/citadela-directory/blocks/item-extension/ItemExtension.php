<?php

namespace Citadela\Directory\Blocks;

class ItemExtension extends Block {

    protected static $slug = 'item-extension';

    function __construct() {
        parent::__construct();
    }

    public static function renderCallback($attributes, $content) {
        if ( is_admin() ) {
            return;
        }
        
        global $post;
        
        if( ! isset( $post ) ) return '';
        
        $activeProPlugin = defined( 'CITADELA_PRO_PLUGIN' );

        $item_extension = get_option( 'citadela_directory_item_extension', [] );

        //check if the feature is enabled
        if( ! \Citadela\Directory\ItemExtension::$enabled ) return;
        
        $blockTitle = $attributes['title'];
        
        //set defaults that might not be defined
        $attributes['label_width'] = isset( $attributes['label_width'] ) ? $attributes['label_width'] : null;
        $classes = [];
        if( isset( $attributes['className'] ) ){ $classes[] = $attributes['className']; }; 
        $classes[] = "{$attributes['layout']}-layout";
        $classes[] = "align-{$attributes['align']}";
        if( $attributes['hide_empty'] ) $classes[] = 'hide-empty-values';
        if( $attributes['fix_box_width'] ) $classes[] = 'fix-width';
        if( $attributes['layout'] == 'list' && $attributes['label_width'] ) $classes[] = 'custom-label-width';
        if( $attributes['layout'] == 'list' && $attributes['list_table_style'] ) $classes[] = 'table-style';
        
        // color classes if Citadela Pro plugin is active
        if( $activeProPlugin ){
            if( $attributes['layout'] != 'text' && $attributes['lines_color'] ) $classes[] = 'custom-lines-color';
            if( $attributes['labels_color'] ) $classes[] = 'custom-label-color';
            if( $attributes['values_color'] ) $classes[] = 'custom-value-color';
        }

        $title_styles = [];
        if( $activeProPlugin && $attributes['title_color'] ) $title_styles[] = "color: " . esc_attr( $attributes['title_color'] ) . ";";
        $title_style = $title_styles ? 'style="' . implode('', $title_styles ) . '"' : "";

        $separator_styles = [];
        if( $activeProPlugin && $attributes['title_color'] ) $separator_styles[] = "color: " . esc_attr( $attributes['title_color'] ) . ";";
        $separator_style = $separator_styles ? 'style="' . implode('', $separator_styles ) . '"' : "";

        $label_styles = [];
        if( $attributes['layout'] == 'list' && $attributes['label_width'] ) $label_styles[] = "width: " . esc_attr( $attributes['label_width'] ) . "px;";
        if( $activeProPlugin && $attributes['labels_color'] ) $label_styles[] = "color: " . esc_attr( $attributes['labels_color'] ) . ";";
        $label_style = $label_styles ? 'style="' . implode('', $label_styles ) . '"' : "";

        $values_styles = [];
        if( $activeProPlugin && $attributes['values_color'] ) $values_styles[] = "color: " . esc_attr( $attributes['values_color'] ) . ";";
        $values_style = $values_styles ? 'style="' . implode('', $values_styles ) . '"' : "";

        $wrapper_styles = [];
        if( $attributes['layout'] == 'box' && $attributes['box_width'] ) $wrapper_styles[] = "flex-basis: " . esc_attr( $attributes['box_width'] ) . "px;";
        if( $activeProPlugin && $attributes['layout'] != 'text' && $attributes['lines_color'] ) $wrapper_styles[] = "border-color: " . esc_attr( $attributes['lines_color'] ) . ";";
        $wrapper_style = $wrapper_styles ? 'style="' . implode('', $wrapper_styles ) . '"' : "";
        

        $inputs = [];
        foreach ($item_extension['inputs_group']['inputs'] as $key => $data) {
            // check if would be displayed all data or only selected inputs
            if( $attributes['show_data'] === 'all' 
                || ( $attributes['show_data'] === 'selected' && in_array( $key, $attributes['selected_inputs'] ) ) 
                || ( $attributes['show_data'] === 'hide_selected' && ! in_array( $key, $attributes['selected_inputs'] ) )
                ){
                $meta_id = "_citadela_item_extension_{$key}";
                $meta_value = get_post_meta( $post->ID, $meta_id, true );
                $value = \Citadela\Directory\ItemExtension::validate_output( $data, $meta_value, $meta_id );

                $empty_value_class = $value == '' ? 'empty-value' : '';
                
                // check if would be displayed also empty values
                if( ( ! $attributes['hide_empty'] || ( $attributes['hide_empty'] && $value != '' ) ) ) {
                    
                    //if it's checkbox, check also if would be displayed not selected checkbox
                    $show = $data['type'] != 'checkbox' || ! ( $data['type'] == 'checkbox' && $attributes['hide_empty_checkbox'] && $meta_value == '' );
                    
                    if( $show ){

                        // customize output value by input type needs
                        switch ( $data['type'] ) {
                            
                            case 'citadela_number':
                                if( $value != '' && $data['unit'] != '' ){
                                    if( $data['unit-position'] == 'left') $value = "<span class=\"unit left-position\">{$data['unit']}</span>{$value}";
                                    if( $data['unit-position'] == 'right') $value = "{$value}<span class=\"unit right-position\">{$data['unit']}</span>";
                                }
                                break;
                            case 'email':
                                $value = $value !== '' ? "<a href=\"mailto:{$value}\">{$value}</a>" : '';
                                break;
                            case 'citadela_url':
                                if( $value !== '' ){
                                    // check for available label instead plain url
                                    $label = $value;
                                    if( $data[ 'use_url_label' ] ){
                                        $label = get_post_meta( $post->ID, "_citadela_item_extension_{$key}_label", true );
                                        $label = $label ? $label : $value;
                                    }
                                    $value =  "<a href=\"{$value}\">{$label}</a>";
                                }
                                $value = $value ? $value : '';
                                break;
                            case 'date':
                                $value = $value !== '' ? date_i18n( get_option( 'date_format' ), strtotime($value) ) : '';
                                $value = $value ? $value : '';
                                break;
                                
                            default:
                                $value = $value ? $value : '';
                                break;
                        }

                        $inputs[$key] = [
                            'label' => $data['label'],
                            'value' => $value,
                            'class' => $empty_value_class,
                            'type' => $data['type'],
                        ];
                    }
                }

            }

        };

        if( empty( $inputs ) ) return '';

        ob_start();
        include dirname( __FILE__ ) . "/../../plugin/cpt/item/templates/parts/single-item-extension.php";
        return ob_get_clean();

    }

}