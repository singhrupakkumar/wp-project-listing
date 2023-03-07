<?php

class CitadelaUser
{
    public static $fields = [];


    public static function init() {
        self::$fields = [
            'cover_image' => [
                'type'		  => 'image',
                'title' 	  => esc_html__('Cover image', 'citadela-directory'),
                'description' => '',
                'default'	  => '',
                'single-meta' => true
            ],
        ];

		add_action( 'show_user_profile', [ __CLASS__, 'render_control_content' ] );
        add_action( 'edit_user_profile', [ __CLASS__, 'render_control_content' ] );
        add_action( 'personal_options_update', [ __CLASS__, 'save_controls_content' ] );
        add_action( 'edit_user_profile_update', [ __CLASS__, 'save_controls_content' ] );


    }


    public static function render_control_content() {
        global $user_id;
        $cover_image_id = $user_id ? get_the_author_meta( 'citadela_cover_image', $user_id ) : "";
        $cover_image_url = "";
        if( $cover_image_id ){
            $src = wp_get_attachment_image_src( $cover_image_id, 'full' );
            $cover_image_url = $src[0];
        }

        ob_start();
        ?>
            <h2><?php esc_html_e( 'User additional options', 'citadela-directory' ); ?></h2>

            <table class="form-table citadela-user-settings">
                <tr>
                    <th><label for="citadela-cover-image"><?php esc_html_e( 'Cover image', 'citadela-directory' ); ?></label></th>
                    <td>
                        <div class="citadela-control-image">
                            <div class="citadela-image-container">
                                <div class="citadela-image-select-container">
                                    <input type="hidden" id="citadela-cover-image" class="citadela-control" name="citadela_cover_image" value="<?php esc_html_e( $cover_image_id ); ?>" data-saveas="id">

                                    <input type="button" class="citadela-select-image-button button button-primary" value="<?php esc_html_e('Select image', 'citadela-directory') ?>" id="citadela-cover-image-media-button">

                                    <input type="button" class="citadela-delete-image-button button button-secondary <?php if( ! $cover_image_id ) echo esc_attr( 'hidden' ); ?>" value="Remove Image" id="citadela-cover-image-delete-button">                   
                                </div>
                                <div class="citadela-image-preview-container">
                                    <?php if( $cover_image_url != "" ): ?>
                                        <img src="<?php echo esc_url( $cover_image_url ); ?>"/>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
            </table>
        <?php
        echo ob_get_clean();
    }


    public static function save_controls_content( $user_id ) {
        if ( empty( $_POST['_wpnonce'] ) || ! wp_verify_nonce( $_POST['_wpnonce'], 'update-user_' . $user_id ) ) {
            return;
        }
        
        if ( !current_user_can( 'edit_user', $user_id ) ) { 
            return false; 
        }

        update_user_meta( $user_id, 'citadela_cover_image', $_POST['citadela_cover_image'] );
    }

}