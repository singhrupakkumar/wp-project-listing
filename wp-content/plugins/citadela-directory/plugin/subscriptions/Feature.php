<?php

namespace Citadela\Directory\Subscriptions;

class Feature
{
    protected $active = false;
    protected $options;
    protected $capabilities = [
        'edit_citadela-item'                => true,
        'read_citadela-item'                => true,
        'delete_citadela-item'              => true,
        'edit_citadela-items'               => true,
        'edit_others_citadela-items'        => false,
        'publish_citadela-items'            => true,
        'read_private_citadela-items'       => true,
        'read_citadela-items'               => true,
        'delete_citadela-items'             => true,
        'delete_private_citadela-items'     => true,
        'delete_published_citadela-items'   => true,
        'delete_others_citadela-items'      => false,
        'edit_private_citadela-items'       => true,
        'edit_published_citadela-items'     => true,
        'assign_citadela-item-categories'   => true,
        'assign_citadela-item-locations'    => true,
        'upload_files'                      => true,
        'edit_files'                        => true,
        'delete_posts'                      => true
    ];
    protected $subscriptions;
    protected $all_woo_subscriptions;
    protected $plugin;
    public $enabled;
    public $enabled_subscription_plugin;
    
    function __construct()
    {
        $this->plugin = \CitadelaDirectory::getInstance();
        $this->enabled = $this->plugin->pluginOptions->subscriptions['enable_subscriptions'];
        $this->enabled_subscription_plugin = false;
        
        if( in_array( 'wc-subscriptions/woocommerce-subscriptions.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ){
            $this->enabled_subscription_plugin = true;
            $this->enabled = true;
        }
        
        if( $this->enabled && ! $this->enabled_subscription_plugin ){
            include __DIR__ . '/../libs/wc-subscriptions/woocommerce-subscriptions.php';
        }
        $this->options = [
            [
                'control' => 'checkbox',
                'id' => '_citadela_subscription',
                'label' => esc_html__('Enable', 'citadela-directory'),
                'description' => esc_html__('This product is a Citadela Subscription.', 'citadela-directory')
            ],
            'items' => [
                'control' => 'input',
                'id' => '_citadela_subscription_items',
                'label' => esc_html__('Max number of items', 'citadela-directory'),
                'description' => esc_html__('The maximum number of items that customers with this subscription can publish (Leave empty for the unlimited number of items).', 'citadela-directory'),
                'desc_tip' => true,
                'data_type' => 'decimal'
            ],
            'categories' => [
                'control' => 'checkbox',
                'id' => '_citadela_subscription_categories',
                'label' => esc_html__('Categories', 'citadela-directory'),
                'description' => esc_html__('Allow the customer to set catogories for Citadela items.', 'citadela-directory')
            ],
            'locations' => [
                'control' => 'checkbox',
                'id' => '_citadela_subscription_locations',
                'label' => esc_html__('Locations', 'citadela-directory'),
                'description' => esc_html__('Allow the customer to set locations for Citadela items.', 'citadela-directory')
            ],
            'editor' => [
                'control' => 'checkbox',
                'id' => '_citadela_subscription_editor',
                'label' => esc_html__('Content', 'citadela-directory'),
                'description' => esc_html__('Allow the customer to set the content for Citadela items.', 'citadela-directory')
            ],
            'thumbnail' => [
                'control' => 'checkbox',
                'id' => '_citadela_subscription_thumbnail',
                'label' => esc_html__('Featured image', 'citadela-directory'),
                'description' => esc_html__('Allow the customer to set the featured image for Citadela items.', 'citadela-directory')
            ],
            'excerpt' => [
                'control' => 'checkbox',
                'id' => '_citadela_subscription_excerpt',
                'label' => esc_html__('Excerpt', 'citadela-directory'),
                'description' => esc_html__('Allow the customer to set the excerpt for Citadela items.', 'citadela-directory')
            ],
            'page-attributes' => [
                'control' => 'checkbox',
                'id' => '_citadela_subscription_page-attributes',
                'label' => esc_html__('Attributes', 'citadela-directory'),
                'description' => esc_html__('Allow the customer to set the page attributes for Citadela items.', 'citadela-directory')
            ],
            'comments' => [
                'control' => 'checkbox',
                'id' => '_citadela_subscription_comments',
                'label' => esc_html__('Comments', 'citadela-directory'),
                'description' => esc_html__('Allow the customer to manage comments for Citadela items.', 'citadela-directory')
            ],
            'reviews' => [
                'control' => 'checkbox',
                'id' => '_citadela_subscription_reviews',
                'label' => esc_html__('Reviews', 'citadela-directory'),
                'description' => esc_html__('Allow the customer to manage reviews for Citadela items.', 'citadela-directory')
            ],
            'general' => [
                'control' => 'checkbox',
                'id' => '_citadela_subscription_general',
                'label' => esc_html__('General options', 'citadela-directory'),
                'description' => esc_html__('Allow the customer to set the general options for Citadela items.', 'citadela-directory')
            ],
            'address-location' => [
                'control' => 'checkbox',
                'id' => '_citadela_subscription_address-location',
                'label' => esc_html__('Address and location', 'citadela-directory'),
                'description' => esc_html__('Allow the customer to set the address and location for Citadela items.', 'citadela-directory')
            ],
            'contact' => [
                'control' => 'checkbox',
                'id' => '_citadela_subscription_contact',
                'label' => esc_html__('Contact', 'citadela-directory'),
                'description' => esc_html__('Allow the customer to set the contact for Citadela items.', 'citadela-directory')
            ],
            'opening_hours' => [
                'control' => 'checkbox',
                'id' => '_citadela_subscription_opening_hours',
                'label' => esc_html__('Opening hours', 'citadela-directory'),
                'description' => esc_html__('Allow the customer to set the opening hours for Citadela items.', 'citadela-directory')
            ],
            'gallery' => [
                'control' => 'checkbox',
                'id' => '_citadela_subscription_gallery',
                'label' => esc_html__('Images gallery', 'citadela-directory'),
                'description' => esc_html__('Allow the customer to set the images gallery for Citadela items.', 'citadela-directory')
            ],
            'featured_category' => [
                'control' => 'checkbox',
                'id' => '_citadela_subscription_featured_category',
                'label' => esc_html__('Featured category', 'citadela-directory'),
                'description' => esc_html__('Allow the customer to set the Featured Category for Citadela items.', 'citadela-directory')
            ],
            'gpx_file_upload' => [
                'control' => 'checkbox',
                'id' => '_citadela_subscription_gpx_file_upload',
                'label' => esc_html__('GPX Track', 'citadela-directory'),
                'description' => esc_html__('Allow the customer to set the GPX track file for Citadela items.', 'citadela-directory')
            ],
            'extension' => [
                'control' => 'checkbox',
                'id' => '_citadela_subscription_extension',
                'label' => esc_html__('Item Extension', 'citadela-directory'),
                'description' => esc_html__('Allow the customer to set the Item Extension options for Citadela items.', 'citadela-directory')
            ]
        ];

        add_action('after_setup_theme', function () {
			if (\Citadela::$allowed) {

                add_action('init', function () {
                    if (!class_exists('woocommerce') || !$this->enabled) {
                        return;
                    }
                    foreach (get_posts([
                        'numberposts' => -1,
                        'post_type' => 'product',
                        'meta_key' => '_subscription_price',
                    ])as $subscription) {
                        if( get_post_meta($subscription->ID, '_citadela_subscription', true) === 'yes' ){
                            $this->subscriptions[] = $subscription->ID;
                        }
                        $this->all_woo_subscriptions[] = $subscription->ID;
                    }
                    if (empty($this->subscriptions)) {
                        return;
                    }
                    if (get_current_user_id()) {
                        foreach ($this->subscriptions as $subscription) {
                            if (wcs_user_has_subscription(get_current_user_id(), $subscription, 'active')) {
                                $this->active = true;
                                foreach ($this->options as $key => $option) {
                                    if (!empty($key)) {
                                        $value = get_post_meta($subscription, $option['id'], true);
                                        switch ($option['control']) {
                                            case 'checkbox':
                                                if (!empty($value) && empty($this->options[$key]['value'])) {
                                                    $this->options[$key]['value'] = true;
                                                }
                                                break;
                                            case 'input':
                                                if (empty($value)) {
                                                    $this->options[$key]['value'] = true;
                                                } else if (!isset($this->options[$key]['value']) || ($this->options[$key]['value'] !== true && intval($value) > intval($this->options[$key]['value']))) {
                                                    $this->options[$key]['value'] = intval($value);
                                                }
                                                break;
                                        }
                                    }
                                }
                            }
                        }
                        
                        $allow_media = false;
                        foreach ([
                            'editor',
                            'thumbnail',
                            'gallery',
                        ] as $image_option) {
                            if ( ! empty($this->options[$image_option]['value']) ) {
                                $allow_media = true;
                            }
                        }
                        if( $allow_media === false ){
                            $this->capabilities['edit_files'] = false;
                            $this->capabilities['upload_files'] = false;
                        }

                        if (empty($this->options['categories']['value'])) {
                            $this->capabilities['assign_citadela-item-categories'] = false;
                        }
                        if (empty($this->options['locations']['value'])) {
                            $this->capabilities['assign_citadela-item-locations'] = false;
                        }
                        if ($this->active) {
                            add_filter('user_has_cap', function ($all) {
                                return array_merge($all, $this->capabilities);
                            });

                            // wee need to allow users post unfiltered post content like admin,
                            // otherwise are not saved some inline styles of blocks in post content what cause blocks validation errors
                            add_filter('wp_insert_post_data', function( $data, $postarr, $unsanitized_postarr ){
                                if( isset( $data['post_content'] ) && isset( $unsanitized_postarr['post_content'] ) ){
                                    $data['post_content'] = $unsanitized_postarr['post_content'];
                                }
                                return $data;
                            }, 10, 3 );
                        }
                    }
                    add_filter('pre_get_posts', function ($query) {
                        // TODO: better check to alter only queries for items
                        // idea: use rather custom wordpress filter to edit arguments before they are passed to query
                        // asume admin queries for logged in woocommerce subscribers
                        if ($query->is_admin && in_array($query->query_vars['post_type'], ['attachment', 'citadela-item'])) {
                            // limit query author parameter only for users with subscription
                            if ($this->user_has_active_subscription()) {
                                $query->set('author', get_current_user_id());
                            }
                        // asume frontend query for items
                        } else if (!$query->is_admin && !empty($query->query_vars['post_type']) && in_array($query->query_vars['post_type'], ['attachment', 'citadela-item'])) {
                            // consider that user with 'customer' role is woocommerce user without active subscription (user with active subscription should have role 'subscriber')
                            $customers_without_subscription = get_users(['role' => 'customer', 'fields' => 'ids']);
                            // filter out content created by customers without active subscription
                            $query->set('author__not_in', $customers_without_subscription);
                        }
                        return $query;
                    });
                    add_filter('woocommerce_prevent_admin_access', '__return_false');
                    add_filter('woocommerce_disable_admin_bar', '__return_false');
                });
                add_action('admin_init', function () {
                    if( !$this->enabled ){
                        return;
                    }
                    global $pagenow, $current_user;
                    add_filter('woocommerce_product_data_tabs', function ($tabs) {
                        $tabs['citadela-subscription'] = [
                            'label' => esc_html__('Citadela Subscription', 'citadela-directory'),
                            'target' => 'citadela_subscription',
                        ];
                        return $tabs;
                    });
                    add_action('woocommerce_product_data_panels', function () {
                        echo '<div id="citadela_subscription" class="panel woocommerce_options_panel">';
                        foreach ($this->options as $option) {
                            switch ($option['control']) {
                                case 'input':
                                    woocommerce_wp_text_input($option);
                                    break;
                                case 'checkbox':
                                    woocommerce_wp_checkbox($option);
                                    break;
                            }
                        }
                        echo '</div>';
                    });
                    add_action('woocommerce_process_product_meta', function ($id) {
                        foreach ($this->options as $option) {
                            update_post_meta($id, $option['id'], isset($_POST[$option['id']]) ? $_POST[$option['id']] : '');
                        }
                    });
                    if (!empty(array_intersect(['subscriber', 'customer'], $current_user->roles))) {
                        if (!isset($this->active)) {
                            return;
                        }
                        add_action('pre_get_posts', function () {
                            if ($screen = get_current_screen()) {
                                add_filter('views_' . $screen->id, function ($count) {
                                    unset($count['all']);
                                    unset($count['publish']);
                                    return $count;
                                });
                            }
                        });
                        if (empty($this->options['categories']['value'])) {
                            remove_meta_box('citadela-item-categorydiv', 'citadela-item', 'side');
                        }
                        if (empty($this->options['locations']['value'])) {
                            remove_meta_box('citadela-item-locationdiv', 'citadela-item', 'side');
                        }
                        foreach ([
                            'editor',
                            'thumbnail',
                            'excerpt',
                            'comments',
                            'page-attributes'
                        ] as $feature) {
                            if (empty($this->options[$feature]['value'])) {
                                remove_post_type_support('citadela-item', $feature);
                                if ($feature === 'comments') {
                                    remove_meta_box('commentstatusdiv', 'citadela-item', 'normal');
                                    remove_meta_box('commentsdiv', 'citadela-item', 'normal');
                                }
                            }
                        }
                        if ($pagenow == 'post-new.php' && !empty($_GET['post_type']) && $_GET['post_type'] == 'citadela-item') {
                            if ($this->options['items']['value'] !== true) {
                                $current = get_posts([
                                    'numberposts' => -1,
                                    'post_type' => 'citadela-item',
                                    'author' => $current_user->ID
                                ]);
                                if (count($current) >= $this->options['items']['value']) {
                                    wp_redirect(admin_url('/edit.php?post_type=citadela-item&citadela-subscriptions-notification=items'));
                                }
                            }
                        }
                        if (isset($_GET['citadela-subscriptions-notification'])) {
                            add_action('admin_notices', function() {
                                $notifications = [
                                    'items' => esc_html__('Maximum items for the current subscription exceeded.', 'citadela-directory')
                                ];
                                printf(
                                    '<div class="notice notice-warning notice-large"><p>%1$s</p></div>',
                                    $notifications[$_GET['citadela-subscriptions-notification']]
                                );
                            });
                        }
                    }
                });

            }
		}, 100);
    }
    function get_user_active_subscription($id = null)
    {
        if( !$this->enabled ) {
            return false;
        }
        if (!isset($id)) {
            $id = get_current_user_id();
        }
        if( is_array( $this->subscriptions ) && ! empty( $this->subscriptions ) ){
            foreach ($this->subscriptions as $subscription) {
                if ( class_exists('woocommerce') && wcs_user_has_subscription($id, $subscription, 'active') ) {
                    return $subscription;
                }
            }
        }
        return false;
    }
    function get_user_active_any_woo_subscription($id = null)
    {
        if (!isset($id)) {
            $id = get_current_user_id();
        }
        if( is_array( $this->all_woo_subscriptions ) && ! empty( $this->all_woo_subscriptions ) ){
            foreach ($this->all_woo_subscriptions as $subscription) {
                if ( class_exists('woocommerce') && wcs_user_has_subscription($id, $subscription, 'active') ) {
                    return $subscription;
                }
            }
        }
        return false;
    }
    function user_has_active_subscription($id = null)
    {
        if( !$this->enabled ) {
            return false;
        }
        if (!isset($id)) {
            $id = get_current_user_id();
        }
        if( is_array( $this->subscriptions ) && ! empty( $this->subscriptions ) ){
            foreach ($this->subscriptions as $subscription) {
                if ( class_exists('woocommerce') && wcs_user_has_subscription($id, $subscription, 'active') ) {
                    return true;
                }
            }
        }
        return false;
    }
    function user_has_active_any_woo_subscription($id = null)
    {
        if (!isset($id)) {
            $id = get_current_user_id();
        }
        if( is_array( $this->all_woo_subscriptions ) && ! empty( $this->all_woo_subscriptions ) ){
            foreach ($this->all_woo_subscriptions as $subscription) {
                if ( class_exists('woocommerce') && wcs_user_has_subscription($id, $subscription, 'active') ) {
                    return true;
                }
            }
        }
        return false;
    }
    function user_has_subscription($id = null)
    {
        if( !$this->enabled ) {
            return false;
        }
        if (!isset($id)) {
            $id = get_current_user_id();
        }
        if( is_array( $this->subscriptions ) && ! empty( $this->subscriptions ) ){
            foreach ($this->subscriptions as $subscription) {
                if ( class_exists('woocommerce') && wcs_user_has_subscription($id, $subscription) ) {
                    return true;
                }
            }
        }
        return false;
    }
    function user_has_any_woo_subscription($id = null)
    {
        if (!isset($id)) {
            $id = get_current_user_id();
        }
        if( is_array( $this->all_woo_subscriptions ) && ! empty( $this->all_woo_subscriptions ) ){
            foreach ($this->all_woo_subscriptions as $subscription) {
                if ( class_exists('woocommerce') && wcs_user_has_subscription($id, $subscription) ) {
                    return true;
                }
            }
        }
        return false;
    }
    static function getPermission( $permission )
    {
        global $current_user;
        if (!empty(array_intersect(['subscriber', 'customer'], $current_user->roles))) {
            foreach (get_posts([
                'numberposts' => -1,
                'post_type' => 'product',
                'meta_key' => '_citadela_subscription',
                'meta_value' => 'yes'
            ]) as $subscription) {
                if ( class_exists('woocommerce') && wcs_user_has_subscription(get_current_user_id(), $subscription->ID, 'active') ) {
                    if (get_post_meta($subscription->ID, "_citadela_subscription_{$permission}", true)) {
                        return true;
                    }                    
                }
            }
        } else {
            return true;
        }
        return false;
    }

    static function getPermissionReviews()
    {
        global $current_user;
        if (!empty(array_intersect(['subscriber', 'customer'], $current_user->roles))) {
            foreach (get_posts([
                'numberposts' => -1,
                'post_type' => 'product',
                'meta_key' => '_citadela_subscription',
                'meta_value' => 'yes'
            ]) as $subscription) {
                if ( class_exists('woocommerce') && function_exists('wcs_user_has_subscription') && wcs_user_has_subscription(get_current_user_id(), $subscription->ID, 'active') ) {
                    if (get_post_meta($subscription->ID, '_citadela_subscription_reviews', true)) {
                        return true;
                    }                    
                }
            }
        } else {
            return true;
        }
        return false;
    }
    static function getPermissionsItem()
    {
        $permissions = [
            'general' => false,
            'address-location' => false,
            'contact' => false,
            'opening_hours' => false,
            'featured_category' => false,
            'gpx_file_upload' => false,
            'extension' => false,
            'comments' => false,
            'reviews' => false,
            'gallery' => false,
        ];
        
        global $current_user;
        if (!empty(array_intersect(['subscriber', 'customer'], $current_user->roles))) {
            foreach (get_posts([
                'numberposts' => -1,
                'post_type' => 'product',
                'meta_key' => '_citadela_subscription',
                'meta_value' => 'yes'
            ]) as $subscription) {
                if ( class_exists('woocommerce') && function_exists('wcs_user_has_subscription') && wcs_user_has_subscription(get_current_user_id(), $subscription->ID, 'active') ) {
                    foreach ($permissions as $key => $permission) {
                        $value = get_post_meta($subscription->ID, '_citadela_subscription_' . $key, true);
                        if (!empty($value) && empty($permissions[$key])) {
                            $permissions[$key] = true;
                        }
                    }
                }
            }
        } else {
            foreach ($permissions as $key => $permission) {
                $permissions[$key] = true;
            }
        }

        if( $permissions['extension'] === true && ! \Citadela\Directory\ItemExtension::$enabled ){
            $permissions['extension'] = false;
        }
        return $permissions;
    }
}