<?php

namespace Citadela\Directory;

class ClaimListing {
    private static $subscriptions;
    static function run()
    {
        add_action('wp_ajax_citadela_claim_listing', function () {
            self::claim(wp_get_current_user());
        });
        add_action('wp_ajax_nopriv_citadela_claim_listing', function () {
            self::claim();
        });
        add_action('init', function () {
            add_post_type_support('citadela-item', 'author');
        });
        add_action('admin_init', function() {
            if (self::enabled() && current_user_can('administrator')) {
                if (isset($_GET['citadela-claim-action'])) {
                    switch ($_GET['citadela-claim-action']) {
                        case 'approve':
                            self::approve($_GET['citadela-claim-item']);
                            break;
                        case 'decline':
                            self::decline($_GET['citadela-claim-item']);
                            break;
                    }
                    wp_safe_redirect(admin_url('edit.php?post_type=citadela-item&citadela-claim-notification=' . $_GET['citadela-claim-action']));
                    exit();
                }
                if (isset($_GET['citadela-claim-notification'])) {
                    add_action('admin_notices', function () {
                        echo '<div class="updated"><p>' . ($_GET['citadela-claim-notification'] === 'approve' ? esc_html__('Claim listing has been approved', 'citadela-directory') : esc_html__('Claim listing has been declined', 'citadela-directory')) . '</p></div>';
                    });
                }
                add_filter('manage_citadela-item_posts_columns', function ($columns) {
                    return array_merge($columns, [
                        'citadela-claim-owner' => esc_html__('Owner', 'citadela-directory'),
                        'citadela-claim-listing' => esc_html__('Claim status', 'citadela-directory')
                    ]);
                });
                add_action('manage_posts_custom_column', function ($column, $id) {
                    switch ($column) {
                        case 'citadela-claim-owner':
                            $post = get_post($id);
                            $user = get_user_by('ID', $post->post_author);
                            if ($user) {
                                echo '<a href="' . esc_url( admin_url('user-edit.php?user_id=' . $user->get('ID')) ) . '">' . esc_html( $user->get('user_login') ) . '</a>';
                            }
                            break;
                        case 'citadela-claim-listing':
                            $status = self::getStatus($id);
                            if ($status === 'Unclaimed') {
                                esc_html_e('Unclaimed', 'citadela-directory');
                            } else if ($status === 'Pending') {
                                    $userId = get_post_meta($id, '_citadela_claim_listing_user', true);
                                    $user = get_user_by('ID', $userId);
                                    if ($user) {
                                        echo '<div>' . esc_html__('Claimed by', 'citadela-directory'); echo ' '; echo '<a href="' . esc_url( admin_url('user-edit.php?user_id=' . $userId) ) . '">' . esc_html( $user->get('user_login') ) . ' (' . $user->get('user_email') .')</a></div>';
                                        echo '<a href="' . esc_url( admin_url('edit.php?post_type=citadela-item&citadela-claim-action=approve&citadela-claim-item=' . $id) ) . '" class="button">' . esc_html__('Approve', 'citadela-directory') . '</a>';
                                        echo '<a href="' . esc_url( admin_url('edit.php?post_type=citadela-item&citadela-claim-action=decline&citadela-claim-item=' . $id) ) . '" class="button" onclick="return confirm(\'' . esc_html__('Are you sure you want to decline this claim?', 'citadela-directory') . '\')">' . esc_html__('Decline', 'citadela-directory') . '</a>';
                                    } else {
                                        esc_html_e('Unclaimed', 'citadela-directory');
                                    }
                            } else if ($status === 'Approved') {
                                    echo '<div>' . esc_html__('Approved', 'citadela-directory') . '</div>';
                                    if ($status === 'Approved' && get_post_meta($id, '_citadela_claim_listing_author', true)) {
                                        echo '<a href="' . esc_url( admin_url('edit.php?post_type=citadela-item&citadela-claim-action=decline&citadela-claim-item=' . $id) ) . '" class="button" onclick="return confirm(\'' . esc_html__('Are you sure you want to decline this claim?', 'citadela-directory') . '\')">' . esc_html__('Decline', 'citadela-directory') . '</a>';
                                    }
                                }
                            }
                }, 10, 2);
                add_filter('manage_users_columns', function ($columns) {
                    return array_merge($columns, [
                        'citadela-item-count' => esc_html__('Items', 'citadela-directory')
                    ]);
                });
                add_filter('manage_users_custom_column', function ($value, $column, $id) {
                    global $wpdb;
                    switch ($column) {
                        case 'citadela-item-count':
                            $count = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $wpdb->posts WHERE post_type = 'citadela-item' AND post_status = 'publish' AND post_author = %d", $id));
                            return $count ? '<a href="' . esc_url( admin_url('edit.php?post_type=citadela-item&author=' . $id) ) . '">' . $count  . '</a>' : $count;
                    }
                    return $value;
                }, 10, 3);
                add_action('pre_get_posts', function ($query) {
                    global $pagenow, $typenow;
                    if ($pagenow === 'edit.php' && $typenow === 'citadela-item' && !empty($_GET['author'])) {
                        $query->set('author', $_GET['author']);
                    }
                });
                add_action('delete_user', function ($id) {
                    global $wpdb;
                    $items = $wpdb->get_col($wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE post_type = 'citadela-item' AND post_author = %d", $id), 0);
                    foreach ($items as $item) {
                        self::reset($item);
                    }
                    $items = $wpdb->get_col($wpdb->prepare("SELECT post_id FROM $wpdb->postmeta WHERE meta_key = '_citadela_claim_listing_user' AND meta_value = %d", $id), 0);
                    foreach ($items as $item) {
                        self::reset($item);
                    }
                    $items = $wpdb->get_col($wpdb->prepare("SELECT post_id FROM $wpdb->postmeta WHERE meta_key = '_citadela_claim_listing_author' AND meta_value = %d", $id), 0);
                    foreach ($items as $item) {
                        update_post_meta($item, '_citadela_claim_listing_author', null);
                    }
                });
            }
        });
    }
    static function enabled()
    {
        return class_exists('woocommerce') && !empty(get_option('citadela_directory_subscriptions')['enable_subscriptions']) && !empty(get_option('citadela_directory_claim_listing')['enable']);
    }
    static function getSubscriptions()
    {
        if (!isset(self::$subscriptions)) {
            self::$subscriptions = get_posts([
                'numberposts' => -1,
                'post_type' => 'product',
                'meta_key' => '_citadela_subscription',
                'meta_value' => 'yes'
            ]);
        }
        return self::$subscriptions;
    }
    static function hasActiveSubscription($id = null)
    {
        if (!isset($id)) {
            $id = get_current_user_id();
        }
        foreach (self::getSubscriptions() as $subscription) {
            if (wcs_user_has_subscription($id, $subscription->ID, 'active')) {
                return true;
            }
        }
    }
    static function hasSubscription($id = null)
    {
        if (!isset($id)) {
            $id = get_current_user_id();
        }
        foreach (self::getSubscriptions() as $subscription) {
            if (wcs_user_has_subscription($id, $subscription->ID)) {
                return true;
            }
        }
    }
    static function getStatus($id)
    {
        if (($author = get_post_field('post_author', $id)) && self::hasSubscription($author)) {
            return 'Approved';
        }
        $status = get_post_meta($id, '_citadela_claim_listing_status', true);
        return empty($status) ? 'Unclaimed' : $status;
    }
    static function claim($user = null)
    {
        if (isset($_POST['token'])) {
            $response = \CitadelaDirectoryRecaptcha::verify($_POST['token']);
            if (!$response->success) {
                wp_send_json_error([
                    'message' => esc_html__('Captcha check failed!', 'citadela-directory')
                ]);
            }
        }
        $status = self::getStatus($_POST['id']);
        if ($status === 'Unclaimed') {
            if ($user) {
                $id = $user->ID;
                $username = $user->get('user_login');
                $email = $user->get('user_email');
            } else {
                $errors = new \WP_Error();
                $username = $sanitized_user_login = $user_login = sanitize_user($_POST['username']);
                $email = $user_email = $_POST['email'];
                // Validation copied from the register_new_user function
                // Check the username.
                if ( '' === $sanitized_user_login ) {
                    $errors->add( 'empty_username', wp_kses_post( __( '<strong>Error</strong>: Please enter a username.' ) ) );
                } elseif ( ! validate_username( $user_login ) ) {
                    $errors->add( 'invalid_username', wp_kses_post( __( '<strong>Error</strong>: This username is invalid because it uses illegal characters. Please enter a valid username.' ) ) );
                    $sanitized_user_login = '';
                } elseif ( username_exists( $sanitized_user_login ) ) {
                    $errors->add( 'username_exists', wp_kses_post( __( '<strong>Error</strong>: This username is already registered. Please choose another one.' ) ) );
                } else {
                    /** This filter is documented in wp-includes/user.php */
                    $illegal_user_logins = (array) apply_filters( 'illegal_user_logins', array() );
                    if ( in_array( strtolower( $sanitized_user_login ), array_map( 'strtolower', $illegal_user_logins ), true ) ) {
                        $errors->add( 'invalid_username', wp_kses_post( __( '<strong>Error</strong>: Sorry, that username is not allowed.' ) ) );
                    }
                }
                // Check the email address.
                if ( '' === $user_email ) {
                    $errors->add( 'empty_email', wp_kses_post( __( '<strong>Error</strong>: Please type your email address.' ) ) );
                } elseif ( ! is_email( $user_email ) ) {
                    $errors->add( 'invalid_email', wp_kses_post( __( '<strong>Error</strong>: The email address isn&#8217;t correct.' ) ) );
                    $user_email = '';
                } elseif ( email_exists( $user_email ) ) {
                    $errors->add( 'email_exists', wp_kses_post( __( '<strong>Error</strong>: This email is already registered. Please choose another one.' ) ) );
                }
                if ($errors->has_errors()) {
                    wp_send_json_error([
                        'message' => $errors->get_error_message()
                    ]);
                }
                $password = wp_generate_password();
                $id = wp_insert_user([
                    'user_login' => $username,
                    'user_email' => $email,
                    'user_pass' => $password,
                    'role' => 'subscriber'
                ]);
                if (is_wp_error($id)) {
                    wp_send_json_error([
                        'message' => $id->get_error_message()
                    ]);
                }
                wp_send_new_user_notifications($id);
                wp_signon([
                    'user_login' => $username,
                    'user_password' => $password,
                    'remember' => true
                ]);
            }
            update_post_meta($_POST['id'], '_citadela_claim_listing_status', 'Pending');
            update_post_meta($_POST['id'], '_citadela_claim_listing_time', time());
            update_post_meta($_POST['id'], '_citadela_claim_listing_user', $id);
            update_post_meta($_POST['id'], '_citadela_claim_listing_author', get_post_field('post_author', $_POST['id']));
            $options = get_option('citadela_directory_claim_listing');
            $options['email_message'] = str_replace([
                '{user}',
                '{item}',
                '{actions}'
            ],[
                '<a href="' . esc_url( admin_url('user-edit.php?user_id=' . $id) ) . '">' . $username . ' (' . $email .')</a>',
                '<a href="' . esc_url( admin_url('post.php?post=' . $_POST['id'] . '&action=edit') ) .'">' . esc_html( get_the_title($_POST['id']) ) . '</a>',
                esc_html__('Actions:', 'citadela-directory') . '<a href="' . esc_url( admin_url('edit.php?post_type=citadela-item&citadela-claim-action=approve&citadela-claim-item=' . $_POST['id']) ) . '">' . esc_html__('Approve claim', 'citadela-directory') . '</a> | <a href="' . esc_url( admin_url('edit.php?post_type=citadela-item&citadela-claim-action=decline&citadela-claim-item=' . $_POST['id']) ) . '">' . esc_html__('Decline claim', 'citadela-directory') . '</a>'
            ], $options['email_message']);
            function citadela_directory_claim_listing_mail() { return 'text/html'; }
            add_filter('wp_mail_content_type', 'citadela_directory_claim_listing_mail');
            wp_mail(get_option('admin_email'), $options['email_subject'], $options['email_message']);
            remove_filter('wp_mail_content_type', 'citadela_directory_claim_listing_mail');
            if (isset($_POST['product'])) {
                global $woocommerce;
                $woocommerce->cart->empty_cart();
                $woocommerce->cart->add_to_cart($_POST['product']);
                wp_send_json_success(['redirect' => $woocommerce->cart->get_checkout_url()]);
            }
        } else {
            wp_send_json_error([
                'message' => $status === 'Pending' ? esc_html__('Item pending moderation from admin', 'citadela-directory') : esc_html__('Item already claimed', 'citadela-directory')
            ]);
        }
        wp_send_json_success();
    }
    static function approve($id)
    {
        $user = get_post_meta($id, '_citadela_claim_listing_user', true);
        wp_update_post(['ID' => $id, 'post_author' => $user]);
        update_post_meta($id, '_citadela_claim_listing_status', 'Approved');
    }
    static function decline($id)
    {
        $status = get_post_meta($id, '_citadela_claim_listing_status', true);
        if ($status === 'Approved' && ($author = get_post_meta($id, '_citadela_claim_listing_author', true))) {
            wp_update_post(['ID' => $id, 'post_author' => $author]);
        }
        self::reset($id);
    }
    static function reset($id)
    {
        update_post_meta($id, '_citadela_claim_listing_status', 'Unclaimed');
        update_post_meta($id, '_citadela_claim_listing_time', null);
        update_post_meta($id, '_citadela_claim_listing_user', null);
        update_post_meta($id, '_citadela_claim_listing_author', null);
    }
}