<?php
use Citadela\Pro\Template;

$disabled_feature = \ctdl\pro\dot_get( get_option( 'citadela_pro_integrations' ), 'disable_layout_import_export' );

?>

<div class="wrap citadela-settings-wrap">
    <?php Template::load('/_settings-header'); ?>
    <div class="citadela-settings-content">
        <?php Template::load('/_settings-navigation'); ?>
        <div class="citadela-settings tab-import_layouts_from_file">
        <?php
            if( $disabled_feature ){
                do_action('ctdl_disable_layout_import_export_content');
            }else{
                Template::load('layouts/progress', ['type' => 'upload']);
            }
        ?>
        </div>
    </div>
</div>
