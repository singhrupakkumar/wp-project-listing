<?php

namespace Citadela\Directory\Blocks;

class DirectoryAdvancedFilters extends Block {

    protected static $slug = 'directory-advanced-filters';

    function __construct() {
        parent::__construct();
    }

    public static function renderCallback($attributes, $content) {
        if ( is_admin() ) {
            return;
        }
                
        $activeProPlugin = defined( 'CITADELA_PRO_PLUGIN' );

        //check if the feature is enabled
        if( ! \Citadela\Directory\ItemExtension::$enabled ) return;
        
        $item_extension = get_option( 'citadela_directory_item_extension', [] );
        $available_inputs = array_keys( $item_extension['inputs_group']['inputs'] );
        
        $blockTitle = $attributes['title'];
        
        $classes = [];
        if( isset( $attributes['className'] ) ){ $classes[] = $attributes['className']; }; 
        $classes[] = "{$attributes['layout']}-layout";
        $classes[] = "align-{$attributes['align']}";
        if( $attributes['in_search_form'] ) $classes[] = 'in-search-form';
        if( ! $attributes['title'] ) $classes[] = 'no-header-text';
        if( ! $attributes['show_group_title'] ) $classes[] = 'hidden-filter-group-title';
        
        if( $attributes['layout'] == 'box' && isset( $attributes['box_width'] ) ) $classes[] = 'custom-box-width';
        if( $attributes['show_submit_button'] ) $classes[] = 'has-submit-button';
        
        $closedAdvancedHeader = false;
        if( $attributes['advanced_header'] ){
            $classes[] = 'advanced-header';
            if( $attributes['advanced_header_opened'] ) {
                if( $attributes['advanced_header_closed_on_mobile'] ) {
                    $classes[] = 'closed-on-mobile';
                    $closedAdvancedHeader = true;
                }else{
                    $classes[] = 'opened';
                }
            }else{
                $closedAdvancedHeader = true;
            }
        }
        
        // color classes if Citadela Pro plugin is active
        if( $activeProPlugin ){
            $classes[] = "lines-type-{$attributes['lines_type']}";
            if( $attributes['lines_color'] ) $classes[] = 'custom-lines-color';
            if( $attributes['labels_color'] ) $classes[] = 'custom-label-color';
            if( $attributes['data_row_bg_color'] ) $classes[] = 'custom-data-background-color';

            if( $attributes['advanced_header'] ){
                if( $attributes['header_text_color'] ) $classes[] = 'custom-header-text-color';
                if( $attributes['header_bg_color'] ) $classes[] = 'custom-header-background-color';
                if( $attributes['header_border_color'] ) $classes[] = 'custom-header-border-color';
                $classes[] = "header-border-type-{$attributes['header_border_type']}";
                
                

            }else{
                $classes[] = 'simple-header';
            }

        }

        $title_styles = [];
        if( $activeProPlugin && $attributes['title_color'] && ! $attributes['advanced_header'] ) $title_styles[] = "color: {$attributes['title_color']};";
        
        $header_styles = [];
        if( $activeProPlugin && $attributes['advanced_header'] ) {
            if( $attributes['header_text_color'] ) $header_styles[] = "color: {$attributes['header_text_color']};";
            if( $attributes['header_bg_color'] ) $header_styles[] = "background-color: {$attributes['header_bg_color']};";
            if( $attributes['header_border_color'] ) $header_styles[] = "border-color: {$attributes['header_border_color']};";
                
        }

        $heading_styles = [];
        if( $activeProPlugin && $attributes['title_color'] ) $heading_styles[] = "color: {$attributes['title_color']};";
        if( $activeProPlugin && $attributes['lines_type'] == "filter-heading" && $attributes['lines_color'] ) $heading_styles[] = "border-color: {$attributes['lines_color']};";

        $label_styles = [];
        if( $activeProPlugin && $attributes['labels_color'] ) $label_styles[] = "color: {$attributes['labels_color']};";

        $data_row_styles = [];
        if( $attributes['layout'] == 'box' && isset( $attributes['box_width'] ) ) $data_row_styles[] = "flex-basis: {$attributes['box_width']}px;";
        if( $activeProPlugin && $attributes['lines_type'] == "filter-group" && $attributes['lines_color'] ) $data_row_styles[] = "border-color: {$attributes['lines_color']};";
        if( $activeProPlugin && $attributes['data_row_bg_color'] ) $data_row_styles[] = "background-color: {$attributes['data_row_bg_color']};";

        $checkbox_filter_group = [];
        $selection_filter_group = [];
        
        foreach ($item_extension['inputs_group']['inputs'] as $input_name => $input_data) {
            if( isset( $input_data['use_as_filter'] ) && $input_data['use_as_filter'] ) {
                if( $input_data['type'] == 'checkbox' ){
                    
                    // merge checkboxes into groups
                    $checkbox_filters_group_name = isset( $input_data['checkbox_filters_group_name'] ) && $input_data['checkbox_filters_group_name'] != '' ? $input_data['checkbox_filters_group_name'] : __( 'Filters', 'citadela-directory' );
                    $checkbox_filter_group[ $checkbox_filters_group_name ][$input_name] = $input_data;

                }elseif( $input_data['type'] == 'select' || $input_data['type'] == 'citadela_multiselect' ){
                    // all other filters
                    $selection_filter_group[ $input_name ] = $input_data;
                }
            }
        };

        $filtered_data = [];
        // group checkboxes
        foreach( $checkbox_filter_group as $group_name => $group_inputs ){
            $data = [];
            $data['filters_group_name'] = $group_name;
            $data['filters_group_type'] = 'checkbox';
            $data['filters_group_key'] = 'filters'; // url parameter in query to get checkboxes meta
            $data['filters'] = [];
            
            foreach( $group_inputs as $input_name => $input_data ){
                $use_input = false;
                if( $attributes['show_data'] == 'all' ){
                    $use_input = true;
                }elseif( $attributes['show_data'] == 'selected' ){
                    if( in_array( $input_name, $attributes['selected_inputs'] ) && in_array( $input_name, $available_inputs ) ){
                        $use_input = true;
                    }
                }elseif( $attributes['show_data'] == 'hide_selected' ){
                    if( ! ( in_array( $input_name, $attributes['selected_inputs'] ) && in_array( $input_name, $available_inputs ) ) ){
                        $use_input = true;
                    }
                }
                if( $use_input ){
                    $data['filters'][$input_name] = $input_data['label'];
                }
            }

            $filter_key = $data['filters_group_name'] . "_" . $data['filters_group_type'];

            if( ! empty( $data['filters'] ) ) {
                $filtered_data[ $filter_key ] = $data;
            }
            
        }

        foreach( $selection_filter_group as $input_name => $input_data ){
            $data = [];
            $data['filters_group_name'] = $input_data['label'];
            $data['filters_group_type'] = $input_data['type'];
            $data['filters_group_key'] = $input_name;
            $data['filters'] = [];

            $use_input = false;
            if( $attributes['show_data'] == 'all' ){
                $use_input = true;
            }elseif( $attributes['show_data'] == 'selected' ){
                if( in_array( $input_name, $attributes['selected_inputs'] ) && in_array( $input_name, $available_inputs ) ){
                    $use_input = true;
                }
            }elseif( $attributes['show_data'] == 'hide_selected' ){
                if( ! ( in_array( $input_name, $attributes['selected_inputs'] ) && in_array( $input_name, $available_inputs ) ) ){
                    $use_input = true;
                }
            }
            if( $use_input ){
                foreach( $input_data['choices'] as $choice_key => $choice_label ){
                    $data['filters'][$choice_key] = $choice_label;
                }
            }
            $filter_key = $input_name . "_" . $data['filters_group_type'];
            if( ! empty( $data['filters'] ) ) {
                $filtered_data[ $filter_key ] = $data;
            }
            
        }

        $filters_order = $attributes['filters_order'];
        if( ! empty( $filters_order  ) ){
            
            // if is defined order, reorder filtered data
            $original_data = $filtered_data;
            $filtered_data = [];
            foreach( $filters_order as $filter_name ){
                if( isset( $original_data[ $filter_name ] ) ){
                    $filtered_data[$filter_name] = $original_data[ $filter_name ];
                    // remove input group from original data, so we know if there are some new goups which would be displayed after ordered groups
                    unset( $original_data[ $filter_name ] );
                }
            }

            //if there are still some original data (were not included in stored order), show them after ordered goups
            if( ! empty( $original_data ) ){
                $filtered_data = array_merge( $filtered_data, $original_data );
            }

        }
        
        $button_classes = [];
        $button_classes[] = "{$attributes['button_style']}-style";

        if( $attributes['button_text_color'] ) $button_classes[] = "custom-text-color";
        if( $attributes['button_bg_color'] ) $button_classes[] = "custom-background-color";
        if( isset( $attributes['button_border_radius'] ) ) $button_classes[] = "custom-border-radius";

        $button_styles = [];
        if( $activeProPlugin && $attributes['button_text_color'] ) $button_styles[] = "color: {$attributes['button_text_color']};";
        if( $activeProPlugin && $attributes['button_bg_color'] ) $button_styles[] = "background-color: {$attributes['button_bg_color']};";
        if( $activeProPlugin && isset( $attributes['button_border_radius'] ) ) $button_styles[] = "border-radius: {$attributes['button_border_radius']}px;";
        
        
        $button_text = $attributes['button_text'] !== '' ? $attributes['button_text'] : __( 'Filter', 'citadela-directory' );
        
        $a_filters = isset( $_GET['a_filters'] ) && $_GET['a_filters'] == 'true';
        
        if( $attributes['in_search_form'] ) $classes[] = 'hidden-block';

        if( is_tax('citadela-item-category') || is_tax('citadela-item-location') ){
            global $wp;
            $submit_button_action_url = home_url( $wp->request );
            $submit_source = 'taxonomy';
        }else{
            $submit_button_action_url = home_url();
            $submit_source = '';
        }
        ob_start();
        ?>

        <div class="wp-block-citadela-blocks ctdl-directory-advanced-filters <?php echo implode(' ', $classes); ?>">

            <?php
            // show header section only if it's advanced header, or simple header with filled text
            if( $attributes['advanced_header'] || ( ! $attributes['advanced_header'] && $blockTitle ) ) : ?>
                <header class="citadela-block-header" <?php if( $attributes['advanced_header'] ) echo 'style="'.implode( '', $header_styles ).'"'; ?>>
                    <?php if( $blockTitle ) : ?> 
                        <div class="citadela-block-title">
                            <?php  if( $attributes['in_search_form'] ) : ?>
                                <div class="title" <?php echo 'style="'.implode( '', $title_styles ).'"'; ?>><?php echo wp_kses_post( $blockTitle ); ?></div>
                            <?php else : ?>
                                <h2 <?php echo 'style="'.implode( '', $title_styles ).'"'; ?>><?php echo wp_kses_post( $blockTitle ); ?></h2>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                    <?php if( $attributes['advanced_header'] ) : ?> 
                        <div class="header-toggle"><span class="toggle-arrow"></span></div>
                    <?php endif; ?>
                </header>
            <?php endif; ?>

            <?php if( ! empty( $filtered_data ) ) : ?>
            <div class="citadela-block-articles" <?php if( $attributes['advanced_header'] && $closedAdvancedHeader ) echo 'style="display:none;"';?>>
                <div class="citadela-block-articles-wrap">

                    <?php 
                    foreach ($filtered_data as $key => $data) {
                        if( ! empty( $data['filters'] ) ) :
                            $main_class = [ "type-{$data['filters_group_type']}" ];
                            ?>
                            <div 
                                class="data-row <?php echo esc_attr( implode(' ', $main_class ) );  ?>" 
                                <?php if( $data_row_styles ) echo 'style="'.implode( '', $data_row_styles ).'"'; ?>                               
                            >
                                <?php if( $attributes['show_group_title'] ) : ?>
                                    <div class="filters-heading" <?php echo 'style="'.implode( '', $heading_styles ).'"'; ?>><?php esc_html_e( $data['filters_group_name'] ); ?></div>
                                <?php endif; ?>
                                <div class="filters-wrapper">
                                    <?php 

                                    foreach( $data['filters'] as $input_key => $input_label ) { ?>
                                        <?php
                                        $values = [];
                                        if($data['filters_group_type'] == 'checkbox' ){
                                            if( $a_filters && isset( $_GET[$data['filters_group_key']] ) ){
                                                $values = explode( ',', $_GET[$data['filters_group_key']] );
                                            }
                                        }else{
                                            if( $a_filters && isset( $_GET[$data['filters_group_key']] ) ){
                                                $values = explode( ',', $_GET[$data['filters_group_key']] );
                                            }
                                        }
                                        $selected = in_array( $input_key, $values );
                                        ?>
                                        <div class="filter-container <?php if( $selected ) echo 'selected'; ?>" >
                                            <input type="checkbox" class="filter-value" name="<?php echo esc_attr($data['filters_group_key']); ?>" value="<?php echo esc_attr($input_key); ?>" aria-hidden="true" <?php if( $selected ) echo 'checked="checked"'; ?> >
                                            <div class="filter-checkbox"><i class="fas fa-check"></i></div>
                                            <div class="filter-label" <?php if( $label_styles ) echo 'style="'.implode( '', $label_styles ).'"'; ?>><?php esc_html_e( $input_label ); ?></div>
                                        </div>
                                    <?php } ?>
                                </div>
                            <?php
                        endif;
                        ?>

                        </div>
                    
                    <?php } ?>

                </div>
            </div>
            <?php endif; ?>

            <?php if($attributes['show_submit_button'] ) : ?>
                <div class="submit-button-wrapper <?php echo esc_attr( implode( ' ', $button_classes ) ); ?>" <?php if( $attributes['advanced_header'] && $closedAdvancedHeader ) echo 'style="display:none;"';?>>
                    <div class="submit-button">
                        <a class="button-text" data-action="<?php echo esc_url( $submit_button_action_url ); ?>" data-source="<?php echo esc_attr( $submit_source ); ?>"  <?php if( $button_styles ) echo 'style="'.implode( '', $button_styles ).'"'; ?>><?php echo wp_kses_post( $button_text ); ?></a>
                    </div>
                </div>
            <?php endif; ?>
            
            <?php if($attributes['in_search_form'] ) : ?>
                <div class="buttons-wrapper">
                    <a class="submit-filters"><?php if( $attributes['filters_submit_label'] ){ esc_html_e( $attributes['filters_submit_label'] ); }else{ echo '<i class="fas fa-check"></i>'; } ?></a>
                    <a class="cancel-filters"><?php if( $attributes['filters_disable_label'] ){ esc_html_e( $attributes['filters_disable_label'] ); }else{ echo '<i class="fas fa-times"></i>'; } ?></a>
                </div>
            <?php endif; ?>
        </div>

        <?php
        return ob_get_clean();

    }

}