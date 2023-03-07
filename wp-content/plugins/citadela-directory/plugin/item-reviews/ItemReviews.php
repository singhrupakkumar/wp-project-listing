<?php

namespace Citadela\Directory;

class ItemReviews {

    protected static $plugin = null;
    public static $enabled;
    public static $options;

    public static function run() {

        self::$plugin = \CitadelaDirectory::getInstance();
        self::$options = get_option( 'citadela_directory_item_reviews', [] );
        self::$enabled = self::enabled(); 

        add_action( 'init', [ __CLASS__, 'init' ] );
        add_filter( 'get_avatar_comment_types', [ __CLASS__, 'get_avatar_comment_types' ] );
    }


    public static function init() {

        if( self::$enabled ){
            add_action( 'citadela_directory_item_reviews_comments_template', [ __CLASS__, 'comments_template' ] );
            add_action( 'comment_form', [ __CLASS__, 'comment_form' ] );
            // set custom comment type
            add_action( 'preprocess_comment', [ __CLASS__, 'update_comment_type' ], 1 );
            add_action( 'comment_post', [ __CLASS__, 'comment_post' ] );
            if (Subscriptions\Feature::getPermissionReviews()) {
                
                add_action( 'add_meta_boxes', [ __CLASS__, 'add_meta_boxes' ], 10, 2 );
                add_action( 'admin_footer', [ 'CitadelaItem', 'admin_footer' ] );
                // scripts for Item Reviews metabox when editor is enabled for Item Posts
                if( self::$plugin->ItemPageLayout_instance->allowed_editor ){
                    // if we are in editor, disable some comment actions due to problems during saving related to core wordpress
                    add_filter( 'comment_row_actions', [ 'CitadelaItem', 'comment_row_actions' ], 10, 2 );
                    add_action( 'enqueue_block_editor_assets', [ __CLASS__, 'enqueue_block_editor_assets' ] );
                }
            }
            add_filter( 'wp_update_comment_data', [ __CLASS__, 'save_rating_metabox' ], 1 );
            add_filter( 'comment_form_default_fields', [ __CLASS__, 'comment_form_default_fields'] );
            add_filter( 'citadela_directory_add_item_rating_stars_selection', [ __CLASS__, 'add_item_rating_stars_selection' ] );
            add_filter( 'admin_comment_types_dropdown', [ __CLASS__, 'admin_comment_types_dropdown' ] );
            // custom column in Comments table
            add_filter( 'manage_edit-comments_columns', [ __CLASS__, 'manage_edit_comments_columns' ] );
            add_action( 'manage_comments_custom_column', [ __CLASS__, 'manage_comments_custom_column' ], 10, 2 );
            
        }
        
        // do actions to recalculate Item ratings even the feature is disabled -> item reviews can be still approved/unapproved/trashed as standard comments when Item Reviews feature is disabled 
        foreach ( ['unapproved', 'spam', 'trash' ] as $action ) {
            // actions when we should update rating
            // do nothing when status changed between states trash, spam and unapproved
            add_action( "comment_{$action}_to_approved", [ __CLASS__, 'comment_status_changed' ] );
            add_action( "comment_approved_to_{$action}", [ __CLASS__, 'comment_status_changed' ] );
        }

        add_action( 'edit_comment', [ __CLASS__, 'edit_comment' ] );

    }

    public static function add_meta_boxes( $post_type, $post ){
        if( self::$enabled ){
            $screen    = get_current_screen();
            $screen_id = $screen ? $screen->id : '';
            $allowed_editor = self::$plugin->ItemPageLayout_instance->allowed_editor;
            
            // add Comments (Item Reviews) metabox for Item Post pages
            if ( $screen_id === 'citadela-item' ){
                // decide if show default metabox when editor is not enabled, or use custom when editor is available on Item Posts
                add_meta_box( 'commentsdiv', esc_html__( 'Item Reviews', 'citadela-directory' ), 'post_comment_meta_box', 'citadela-item', 'normal' );
                add_filter( 'get_comment_author_IP', [ 'CitadelaItem', 'get_comment_author_IP' ], 10, 3 );
            }
                
            // Comment rating metabox for Comment Edit screen
            if ( $screen_id === 'comment' && isset( $_GET['c'] ) && metadata_exists( 'comment', intval( wp_unslash( $_GET['c'] ) ), 'item-rating' ) ) {
                add_meta_box( 'citadela-item-rating', esc_html__( 'Rating', 'citadela-directory' ), [ __CLASS__, 'render_rating_metabox'], 'comment', 'normal', 'high' );
            }
        }

    }

    public static function pre_get_comments( $query ){
        $args = [ 'post_type' => 'citadela-item', 'author' => get_current_user_id() ]; 
        $posts_query = new \WP_Query($args);

        $item_ids = [];
        foreach ($posts_query->posts as $post) {
            array_push($item_ids, $post->ID);
        }
        
        $query->set('post__in', $item_ids );
        $query->query_vars['post__in'] = $item_ids;
        return $query;

    }

    public static function maybe_grant_comment_capabilities( $allcaps ) {

        // add capability to edit Comments for registered users with subscription
        if( self::$plugin->Subscriptions_instance->enabled ){
            $user = wp_get_current_user();

            // check if current user is Subscriber and has active subscription
            if ( in_array( 'subscriber', (array) $user->roles ) && self::$plugin->Subscriptions_instance->user_has_active_subscription( intval( $user->ID ) ) ) {
                $allcaps = array_merge( $allcaps, [ 'edit_posts' => true ] );

            }
        }

        return $allcaps;

    }

    public static function comments_template(){
        add_filter( 'comments_template', [ __CLASS__, 'template_loader'] );
        comments_template();
    }

    public static function update_comment_type( $comment_data ){
        // update comment type for comment submited from Item Reviews form on frontend
        if( isset( $_GET["item_reviews_form"] ) && $_GET["item_reviews_form"] === '1' ){
            $comment_data['comment_type'] = "item_review";
        }

        // update comment type for reply comment, check for comment parent comment type
        if( isset( $comment_data['comment_parent'] ) && $comment_data['comment_parent'] !== '0' ){
            $parent = get_comment( $comment_data['comment_parent'] );
            if( $parent && $parent->comment_type === 'item_review' ){
                //parent comment is Item Review, set comment type for reply too
                $comment_data['comment_type'] = "item_review";
            }

        }

        return $comment_data;
    }

    public static function render_review( $comment, $args, $depth ){
        $item_post = get_post( $comment->comment_post_ID );
        $rating = intval( self::get_comment_rating( $comment->comment_ID ) );

        $rating_stars_color = self::rating_stars_color();
        //check if Item Post author can reply to reviews
        $can_reply = false;
        if( is_user_logged_in() ){
            $current_user_id = wp_get_current_user()->ID;
            $can_reply = $current_user_id === intval( $item_post->post_author ) && $depth === 1;
        }

        $review_data = [ 
            'comment'   => $comment, 
            'args'      => $args, 
            'depth'     => $depth,
            'item_post' => $item_post,
            'rating'    => $rating,
            'rating_stars_color'    => $rating_stars_color,
            'can_reply'    => $can_reply && $rating ? true : false, // comment reply is available only for reviews with rating, disable replies to replies
        ];
        extract( $review_data );
        include self::$plugin->paths->dir->cpt . "/item/templates/parts/review.php";
    }


    public static function add_item_rating_stars_selection( $default_comment_textarea ){
        $reviews = get_option( 'citadela_directory_item_reviews', [] );
        $rating_stars_color = self::rating_stars_color();
        $rating_wrapper_class = [];
        if( $rating_stars_color ) $rating_wrapper_class[] = 'custom-color';
        ob_start();
        ?>
        <div class="rating-wrapper <?php echo esc_attr( implode(' ', $rating_wrapper_class) ); ?>">
            <label for="rating"><?php esc_html_e( "Your rating", "citadela-directory" ); ?>&nbsp;<span class="required">*</span></label>
            <div class="rating" <?php if( $rating_stars_color ) echo 'style="color:'. esc_attr( $rating_stars_color ).';"'; ?>></div>
        </div>
        <div class="rating-notification citadela-notification" style="display:none;"><?php esc_html_e('Select your rating, please.', 'citadela-directory') ?></div>
          
        <?php      
        $reviews_html = ob_get_clean();

        return $reviews_html . $default_comment_textarea;
    }


    public static function comment_post( $comment_id ) {
        
        $comment  = get_comment( $comment_id );
        
        // check if comment is item review with rating and check if it's not only reply to comment
        $is_reply = self::is_comment_reply( $comment );
        if ( $comment->comment_type === 'item_review' && !$is_reply ){
            // added item_review with rating, update comment rating and recalculate rating for related Item Post
            // if not rating defined - it's reply to Item Review
            if ( isset( $_POST['rating'] ) && $_POST['rating'] != '' ){
                $rating = intval( wp_filter_nohtml_kses( $_POST['rating'] ) );
                add_comment_meta( $comment->comment_ID, 'item-rating', $rating );

                if( intval( $comment->comment_approved ) === 1 ){
                    self::update_item_rating( intval( $comment->comment_post_ID ), $rating );
                }

            }
        }
    }


    public static function edit_comment( $comment_id ){
        $comment = get_comment( $comment_id );
        if( $comment->comment_type === 'item_review' ){
            $rating = self::get_comment_rating( $comment->comment_ID );
            if( $rating ){
                self::recalculate_item_rating( $comment->comment_post_ID );
            }
        }
    }

     // changed status of comment - ajax actions
     // object $comment - Comment WP Object.
    public static function comment_status_changed( $comment ){
        if( $comment->comment_type === 'item_review' ){
            self::maybe_update_item_rating( $comment );
        }
    }

    public static function maybe_update_item_rating( $comment ){
        $rating = self::get_comment_rating( $comment->comment_ID );
        //update rating only for item_review type comments which have defined rating, otherwise it's only reply to item_review
        if( $rating ){
            self::update_item_rating( $comment->comment_post_ID, $rating );
        }
    }

    public static function recalculate_item_rating( $item_id ){
        $ratings_count = self::get_item_ratings_count( $item_id, true );
        $ratings_number_total = self::get_item_ratings_total_number( $item_id );
        $total_rating = $ratings_number_total / $ratings_count;
        update_post_meta( $item_id, '_citadela_rating', round( $total_rating, 2 ) );
    }

    public static function update_item_rating( $item_id, $rating ){
        $ratings_count = self::get_item_ratings_count( $item_id, true );

        update_post_meta( $item_id, '_citadela_ratings_count', $ratings_count );
        $ratings_number_total = self::get_item_ratings_total_number( $item_id );
        $total_rating = $ratings_count == 0 ? 0 : $ratings_number_total / $ratings_count;
        update_post_meta( $item_id, '_citadela_rating', round( $total_rating, 2 ) );
    }

    public static function get_item_ratings_count( $item_id, $count_comments = false  ) {
        if( $count_comments ){
            $count = get_comments([
                'post_id'   => $item_id,
                'count'     => true, //get only count of comments
                'type'      => 'item_review',
                'status'    => 'approve',
                'meta_query' => array(
                        'relation' => 'AND',
                        array( // select only review comments that have rating defined, exclude replies to reviews
                            'compare' => 'EXISTS',
                            'key' => 'item-rating',
                        ),
                    )
            ]);
            if( $count ){
                update_option('get_item_ratings_count', $count);
                return intval( $count );
            }
        }else{
            $count = get_post_meta( $item_id, '_citadela_ratings_count', true );
            if( $count ){
                return intval( $count );
            }
        }
        return 0;
    }

    public static function get_item_ratings_total_number( $item_id ){
        
        $item_reviews = get_comments([
            'post_id'   => $item_id,
            'type'      => 'item_review',
            'status'    => 'approve',
            'meta_query' => array(
                    'relation' => 'AND',
                    array( // select only review comments that have rating defined,  exclude replies to reviews
                        'compare' => 'EXISTS',
                        'key' => 'item-rating',
                    ),
                )
        ]);

        if( $item_reviews ){
            $total_ratings = 0;
            foreach ($item_reviews as $review) {
                $rating = self::get_comment_rating( $review->comment_ID );
                $total_ratings = $total_ratings + $rating;
            }
            return $total_ratings;
        }
        
        return 0;
        
    }

    public static function comment_form( $post_id ){
        ob_start();
        ?>
        <div class="general-notification citadela-notification" style="display:none;"><?php esc_html_e('Fill all required fields, please.', 'citadela-directory') ?></div>
        <div class="email-notification citadela-notification" style="display:none;"><?php esc_html_e('Use valid email address, please.', 'citadela-directory') ?></div>
          
        <?php      
        echo ob_get_clean(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    }


    public static function render_rating_metabox( $comment ){
        wp_nonce_field( 'citadela_save_data', 'citadela_meta_nonce' );

        $current = self::get_comment_rating( $comment->comment_ID );
        ?>
        <select name="item-rating" id="item-rating">
            <?php
            for ( $rating = 1; $rating <= 5; $rating ++ ) {
                printf( '<option value="%1$s"%2$s>%1$s</option>', $rating, selected( $current, $rating, false ) );
            }
            ?>
        </select>
        <?php
    }

    public static function save_rating_metabox( $data ) {
        // Not allowed, return regular value without updating meta.
        if ( ! isset( $_POST['citadela_meta_nonce'], $_POST['item-rating'] ) || ! wp_verify_nonce( wp_unslash( $_POST['citadela_meta_nonce'] ), 'citadela_save_data' ) ) {
            return $data;
        }

        if ( $_POST['item-rating'] > 5 || $_POST['item-rating'] < 0 ) {
            return $data;
        }

        $comment_id = $data['comment_ID'];
        update_comment_meta( $comment_id, 'item-rating', intval( wp_unslash( $_POST['item-rating'] ) ) ); 

        return $data;
    }


    public static function comment_form_default_fields( $fields ){
        if ( is_singular( 'citadela-item' ) ){
            unset($fields['url']);
        }
        return $fields;
    }

    public static function is_comment_reply( $comment ){
        if( intval( $comment->comment_parent ) ){
            return true;
        }else{
            return false;
        }
    }

    public static function template_loader(){
        return self::$plugin->paths->dir->cpt . "/item/templates/parts/item-reviews.php";
    }

    public static function manage_edit_comments_columns( $columns ){
        if( self::$enabled ){
            $review_col = [
                'item_review' => esc_html__('Item Review', 'citadela-directory'),
            ];

            return array_slice( $columns, 0, 3, true ) + $review_col + array_slice( $columns, 3, NULL, true );;
        }
    }

    public static function manage_comments_custom_column( $column, $comment_ID ){
        global $comment;
        if( $column === 'item_review' && $comment->comment_type === "item_review" ){
            $rating = self::get_comment_rating( $comment->comment_ID );
            if( $rating ){
                echo '<div class="citadela-item-rating">';
                for ($i = 1; $i <= 5 ; $i++) { 
                    if( $i <= intval( $rating ) ){
                        echo '<span class="dashicons dashicons-star-filled"></span>';
                    }else{
                        echo '<span class="dashicons dashicons-star-empty"></span>';
                    }
                }
                echo '</div';
                ?>
                
                <?php
            }else{
                esc_html_e( 'Item owner reply.', 'citadela-directory' );
            }
        }
    }

    public static function comments_open(){
        if( 
            // frontend check to show Reviews form even comments are closed for post
            ( self::$enabled && is_singular( 'citadela-item' ) ) 
            || 
            // admin side check to open comments during Review submit even comments are closed for post
            ( isset( $_GET["item_reviews_form"] ) && $_GET["item_reviews_form"] === '1' )
        ){
            return true;
        }else{
            global $post;
            return $post->comment_status === 'open' ? true : false; 
        }
    }

    public static function admin_comment_types_dropdown( $comment_types ){
        $comment_types['item_review'] = esc_html__( 'Item Reviews', 'citadela-directory');
        return $comment_types;
    }

    public static function get_comment_rating( $comment_id ){
        return intval( get_comment_meta( $comment_id, 'item-rating', true ) );
    }

    public static function get_post_rating( $post_id ){
        return floatval( get_post_meta( $post_id, '_citadela_rating', true ) );
    }

    public static function render_post_rating( $post_id, $args = [], $show_reviews_count = true ){
        if( ! self::$enabled ) return '';       
        $activeProPlugin = defined( 'CITADELA_PRO_PLUGIN' );

        $rating = self::get_post_rating( $post_id );
        $total_count = self::get_item_ratings_count( $post_id );
        $rating_stars_color_main = self::rating_stars_color(); //from Item Reviews settings
        $rating_stars_color_block = isset( $args['rating_color'] ) && $activeProPlugin ? $args['rating_color'] : '';

        if( ! $rating ) return '';
        
        $rating_data = [ 
            'rating'                => $rating,
            'total_count'           => $total_count,
            'show_reviews_count'    => $show_reviews_count,
            'rating_stars_color'    => $rating_stars_color_block ? $rating_stars_color_block : ( $rating_stars_color_main ? $rating_stars_color_main : '' ),
        ];
        ob_start();

        extract( $rating_data );
        include self::$plugin->paths->dir->cpt . "/item/templates/parts/item-rating.php";

        return ob_get_clean();

    }
    public static function get_avatar_comment_types( $comment_types ){
        $comment_types[] = 'item_review';
        return $comment_types;
    }
    
    private static function enabled(){
        return isset( self::$options['enable'] ) && self::$options['enable'];
    }

    public static function rating_stars_color(){
        return ( isset( self::$options['rating_stars_color'] ) && self::$options['rating_stars_color'] ) ? self::$options['rating_stars_color'] : '';
    }

    public static function enqueue_block_editor_assets(){
        wp_enqueue_script( 'postbox' );
        wp_enqueue_script( 'post' );
        wp_enqueue_script( 'admin-comments' );
        wp_enqueue_script( 'edit-comments' );
        wp_enqueue_script( 'comment-reply' );
    }
    
}