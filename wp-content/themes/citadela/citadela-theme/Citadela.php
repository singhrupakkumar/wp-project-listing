<?php
global $citadela;
if (!isset($citadela[33])) {
    $citadela[33] = function () {
        class Citadela
        {
            static $server = 'ait-themes.club';
            static $package;
            static $package_envato;
            static $url;
            static $domain;
            static $key;
            static $allowed;
            static $trial;
            static $business;
            static $cache;
            static function init()
            {
                add_action('http_api_curl', function ($handle, $request, $url) {
                    $domain = explode('/', preg_replace('|https?://|', '', $url))[0];
                    if (strpos($domain, 'ait-themes.club') !== false) {
                        if (defined('CURLOPT_SSL_VERIFYPEER')) {
                            curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false);
                        }
                        if (defined('CURLOPT_SSL_VERIFYSTATUS')) {
                            curl_setopt($handle, CURLOPT_SSL_VERIFYSTATUS, false);
                        }
                        if (defined('CURLOPT_IPRESOLVE') && defined('CURL_IPRESOLVE_V4')) {
                            curl_setopt($handle, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
                        }
                    }
                }, 10, 3);
                Citadela::$url = 'https://system.' . Citadela::$server;
                Citadela::$domain = explode('/', preg_replace('|https?://|', '', get_option('siteurl')))[0];
                Citadela::$key = get_option('citadela_api_key', '');
                Citadela::$package = isset(Citadela::$package) ? Citadela::$package : '';
                Citadela::$package_envato = (Citadela::$package === 'themeforest' && defined('CITADELA_DIRECTORY_PLUGIN') && !defined('CITADELA_THEME') && !defined('CITADELA_PRO_PLUGIN')) ? 'codecanyon' : 'themeforest';
                $localhost = strpos(Citadela::$domain, 'localhost') === 0 || strpos(Citadela::$domain, '127.0.0.1') === 0;
                if ($localhost) {
                    $response['code'] = 200;
                } else {
                    $response = get_option('citadela_api_response');
                    if (empty($response) || ((24 * HOUR_IN_SECONDS) < (time() - $response['time'])) || !isset($response['package']) || $response['package'] !== Citadela::$package) {
                        $response['code'] = 200;
                        try {
                            $response['body'] = Citadela::verifyAccount(array_merge(Citadela::theme()['available'] ? ['citadela'] : [], array_keys(Citadela::plugins()['citadela'])));
                        } catch (Exception $exception) {
                            $response = $exception->response;
                        }
                        $response['time'] = time();
                        $response['package'] = Citadela::$package;
                        update_option('citadela_api_response', $response);
                    }
                }
                Citadela::$allowed = $response['code'] != 401;
                Citadela::$trial = isset($response['body']['trial']);
                Citadela::$business = isset($response['body']['business']);
                if (Citadela::$trial && (defined('CITADELA_DIRECTORY_PLUGIN') || defined('CITADELA_PRO_PLUGIN'))) {
                    $box = '<div class="citadela-notice-trial">' . sprintf(
                        /*translators: 1. Start html anchor tag, 2. End html anchor tag  */ 
                        __('This website is running trial version of Citadela products. Please purchase a membership to activate the website. %1$sView available memberships%2$s', 'citadela'),
                        '<a href="https://www.ait-themes.club/pricing/">',
                        '</a>'
                    ) . '</div>';
                    add_action('admin_notices', function () use ($box) {
                        echo wp_kses_post($box);
                    });
                    add_action('wp_body_open', function () use ($box) {
                        echo wp_kses_post($box);
                    });
                }
                if (!Citadela::$allowed && (defined('CITADELA_DIRECTORY_PLUGIN') || defined('CITADELA_PRO_PLUGIN'))) {
                    if (is_super_admin()) {
                        $message = Citadela::getResponseMessage($response);
                        add_action('admin_notices', function () use ($message) {
                            printf(
                                '<div class="notice notice-warning notice-large"><p><strong class="notice-title">%1$s</strong><br>%2$s</p></div>',
                                esc_html($message['title']),
                                wp_kses_post($message['message'])
                            );
                        });
                    }
                }
                add_action('add_option_citadela_api_key', ['Citadela', 'deleteTransient']);
                add_filter('pre_update_option_citadela_api_key', function ($value) {
                    Citadela::deleteTransient();
                    return $value;
                });
                add_action('update_option_active_plugins', ['Citadela', 'deleteTransient']);
                add_action('update_option_current_theme', ['Citadela', 'deleteTransient']);
                add_filter('pre_set_site_transient_update_plugins', function ($value) {
                    return Citadela::checkUpdates($value, 'plugins');
                });
                add_action('admin_init', function () {
                    register_setting('citadela-api', 'citadela_api_key');
                });
                if (defined('CITADELA_THEME')) {
                    add_action('citadela_updater_options', function () use ($localhost) {
                        ?>
                        <form method="post" action="options.php">
                            <?php
                            settings_fields('citadela-api');
                            do_settings_sections('citadela-api');
                            ?>
                            <?php if (!in_array(Citadela::$package, ['themeforest', 'mojo', 'themely'])) { ?>
                                <div class="setting-part setting-domain">
                                    <div class="label"><?php esc_html_e('Domain', 'citadela'); ?></div>
                                    <div class="setting">
                                        <input type="text" class="regular-text" name="citadela_domain" id="citadela_domain" value="<?php echo esc_attr(Citadela::$domain); ?>" readonly="true">
                                        <p class="description"><?php esc_html_e('You will need this to generate your API Key', 'citadela') ?></p>
                                    </div>
                                </div>
                            <?php } ?>
                            <div class="setting-part setting-key">
                                <div class="label"><?php echo esc_html(Citadela::getKeyLabel()); ?></div>
                                <div class="setting">
                                    <input type="text" class="regular-text" name="citadela_api_key" id="citadela_api_key" value="<?php echo $localhost ? esc_attr__('The API key for localhost is not needed.', 'citadela') : esc_attr(Citadela::$key); ?>"<?php echo $localhost ? esc_html(' readonly="true"') : ''; ?>>
                                    <p class="description"><?php echo wp_kses_post(Citadela::getKeyDescription()); ?></p>
                                </div>
                            </div>
                            <?php if (!$localhost) { ?>
                            <div class="setting-part setting-submit">
                                <?php submit_button( 
                                    esc_html__('Save', 'citadela'),
                                    'primary',
                                    'submit',
                                    false
                                ); ?>
                            </div>
                            <?php } ?>
                        </form>
                        <?php
                    });
                } else if (Citadela::$package_envato !== 'codecanyon') {
                    add_action('admin_menu', function () {
                        add_options_page('Citadela', 'Citadela', 'manage_options', 'citadela-api', function () { ?>
                            <div class="wrap citadela-settings-wrap">
                                <h1>
                                    <i></i> Citadela
                                </h1>
                                <form method="post" action="options.php">
                                    <?php
                                    settings_fields('citadela-api');
                                    do_settings_sections('citadela-api');
                                    ?>
                                    <table class="form-table" role="presentation">
                                        <tbody>
                                            <?php if (!in_array(Citadela::$package, ['themeforest', 'mojo', 'themely'])) { ?>
                                            <tr>
                                                <th scope="row">
                                                    <label for="citadela_domain"><?php esc_html_e('Domain', 'citadela'); ?></label>
                                                </th>
                                                <td>
                                                    <input type="text" class="regular-text" name="citadela_domain" id="citadela_domain" value="<?php echo esc_attr(Citadela::$domain); ?>" readonly="true">
                                                    <p class="description"><?php esc_html_e('You will need this to generate your API Key', 'citadela') ?></p>
                                                </td>
                                            </tr>
                                            <?php } ?>
                                            <tr>
                                                <th scope="row">
                                                    <label for="citadela_api_key"><?php echo esc_html(Citadela::getKeyLabel()); ?></label>
                                                </th>
                                                <td>
                                                    <input type="text" class="regular-text" name="citadela_api_key" id="citadela_api_key" value="<?php echo esc_attr(Citadela::$key); ?>">
                                                    <p class="description"><?php echo wp_kses_post(Citadela::getKeyDescription()); ?></p>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <?php
                                    submit_button();
                                    ?>
                                </form>
                            </div>
                            <?php
                        });
                    });
                }
                add_action('admin_init', function () {
                    if (current_user_can('install_plugins') && !defined('CITADELA_PRO_PLUGIN')) {
                        if (!(isset($_GET['page']) && $_GET['page'] == 'citadela-settings' && !defined('CITADELA_PRO_PLUGIN') && !defined('CITADELA_DIRECTORY_PLUGIN'))) {
                            add_action('admin_notices', function () { ?>
                                <div id="citadela-installation" class="notice">
                                    <p class="notice-info"><?php _e('You can test a full version of premium Citadela features for free.', 'citadela'); ?></p>
                                    <p class="notice-buttons">
                                        <a class="citadela-installation" href="<?php echo admin_url('admin-ajax.php?action=citadela-installation'); ?>"><?php _e('Install Citadela plugins', 'citadela'); ?></a> <strong class="citadela-installation-notice citadela-installation-progress"><?php _e('Installing...', 'citadela'); ?></strong> <strong class="citadela-installation-notice citadela-installation-error"><?php _e('Error with installing', 'citadela'); ?></strong>
                                    </p>
                                </div>
                                <div class="citadela-installation-notice citadela-installation-success notice notice-success is-dismissible">
                                    <p>
                                        <?php echo Citadela::$business ? _e('Citadela Pro plugin installed.', 'citadela') : _e('Premium Citadela plugins installed.', 'citadela'); ?>
                                    </p>
                                </div>
                                <?php
                            });
                        }
                        add_action('wp_ajax_citadela-installation', function () {
                            try {
                                $plugins['pro'] = 'Pro';
                                if (!Citadela::$business) {
                                    $plugins['directory'] = 'Listing';
                                }
                                foreach ($plugins as $key => $value) {
                                    Citadela::installAndActivatePlugin("citadela-$key");
                                }
                            } catch (Exception $exception) {
                                wp_send_json_error(isset($exception->response) ? Citadela::getResponseMessage($exception->response) : []);
                            }
                            wp_send_json_success(['redirect' => admin_url('admin.php?page=citadela-pro-settings&tab=layouts')]);
                        });
                    }
                });
            }
            static function getKeyLabel()
            {
                switch (Citadela::$package) {
                    case 'themeforest':
                        return __('Envato Purchase code', 'citadela');
                    case 'mojo':
                        return __('MOJO Purchase code', 'citadela');
                    case 'themely':
                        return __('Themely Purchase code', 'citadela');
                    default:
                        return __('API Key', 'citadela');
                }
            }
            static function getKeyDescription()
            {
                switch (Citadela::$package) {
                    case 'themeforest':
                        return /*translators: 1. start html anchor tag, 2. end html anchor tag */ sprintf(__('You can find Purchase Code in your %1$sEnvato account &rarr; Downloads%2$s under the Download button.', 'citadela'), '<a href="https://themeforest.net/downloads" target="_blank">', '</a>' );
                    case 'mojo':
                        return /*translators: 1. start html anchor tag, 2. end html anchor tag */ sprintf(__('You can find Purchase Code in your %1$sMOJO account &rarr; Themes%2$s.', 'citadela'), '<a href="https://www.mojomarketplace.com/account/themes" target="_blank">', '</a>' );
                    case 'themely':
                        return __('You can find the Purchase Code in the purchase email from Themely.', 'citadela');
                    default:
                        return /*translators: 1. start html anchor tag, 2. end html anchor tag */ sprintf(__('You can generate API Key for this domain in your %1$saccount%2$s.', 'citadela'), '<a href="' . Citadela::$url . '/account/api" target="_blank">', '</a>' );
                }
            }
            static function deleteTransient()
            {
                delete_site_transient('update_themes');
                delete_site_transient('update_plugins');
                delete_option('citadela_api_response');
            }
            static function theme()
            {
                if (!isset(Citadela::$cache['theme'])) {
                    Citadela::$cache['theme'] = [
                        'active' => false,
                        'available' => false
                    ];
                    $active = wp_get_theme();
                    $active = $active->parent() ? $active->parent() : $active;
                    if ($active->get('Name') === 'Citadela' && $active->get('Author') === 'AitThemes') {
                        return Citadela::$cache['theme'] = [
                            'active' => true,
                            'available' => true,
                            'version' => $active->get('Version')
                        ];
                    }
                    $available = wp_get_theme('citadela');
                    if ($available->exists() && $available->get('Name') === 'Citadela' && $available->get('Author') === 'AitThemes') {
                        return Citadela::$cache['theme'] = [
                            'active' => false,
                            'available' => true,
                            'version' => $available->get('Version')
                        ];
                    }
                }
                return Citadela::$cache['theme'];
            }
            static function plugins()
            {
                if (!isset(Citadela::$cache['plugins'])) {
                    require_once(ABSPATH . 'wp-admin/includes/plugin.php');
                    $active = get_option('active_plugins');
                    Citadela::$cache['plugins'] = [
                        'citadela' => [],
                        'others' => []
                    ];
                    foreach (get_plugins() as $path => $plugin) {
                        $name = explode('/', $path)[0];
                        $plugin['path'] = $path;
                        $plugin['active'] = in_array($path, $active);
                        $plugin['free'] = $name === 'citadela-directory-lite';
                        Citadela::$cache['plugins'][strpos($path, 'citadela-') === 0 ? 'citadela' : 'others'][$name] = $plugin;
                    }
                }
                return Citadela::$cache['plugins'];
            }
            static function checkUpdates($value, $type)
            {
                if (!isset(Citadela::$cache['versionsLatest'])) {
                    Citadela::$cache['versionsLatest'] = json_decode(wp_remote_retrieve_body(wp_remote_post(Citadela::$url . '/core/products/latest-versions', [
                        'body' => json_encode(array_merge(Citadela::theme()['available'] ? ['citadela'] : [], array_keys(Citadela::plugins()['citadela'])))
                    ])), true);
                }
                switch ($type) {
                    case 'themes':
                        if (Citadela::theme()['available'] && (version_compare(Citadela::$cache['versionsLatest']['citadela'], Citadela::theme()['version']) > 0)) {
                            $data['theme'] = 'citadela';
                            $data['new_version'] = Citadela::$cache['versionsLatest']['citadela'];
                            $data['package'] = Citadela::$url . '/core/products/file?' . http_build_query(['package' => Citadela::$package, 'domain' => Citadela::$domain, 'key' => Citadela::$key, 'name' => 'citadela']);
                            $data['url'] = Citadela::$url . '/themes/changelog/citadela';
                            $data['tested'] = '5.7.1';
                            $value->response['citadela'] = $data;
                        }
                        break;
                    case 'plugins':
                        foreach (Citadela::plugins()['citadela'] as $name => $plugin) {
                            if (isset(Citadela::$cache['versionsLatest'][$name]) && version_compare(Citadela::$cache['versionsLatest'][$name], $plugin['Version']) > 0) {
                                $data = new \StdClass;
                                $data->slug = $name;
                                $data->new_version = Citadela::$cache['versionsLatest'][$name];
                                $data->package = Citadela::$url . '/core/products/file?' . http_build_query(['package' => Citadela::$package, 'domain' => Citadela::$domain, 'key' => Citadela::$key, 'name' => $name]);
                                $data->tested = '5.7.1';
                                $value->response[$plugin['path']] = $data;
                            }
                        }
                        break;
                }
                return $value;
            }
            static function getResponseMessage($response)
            {
                $message = [
                    'title' => '',
                    'message' => ''
                ];
                if (defined('CITADELA_THEME')) {
                    $url = '<a href="' . esc_url(admin_url('themes.php?page=citadela-settings')) . '">(' . esc_html__('Admin &rarr; Citadela Theme', 'citadela') . ')</a>';
                } else if (Citadela::$package_envato === 'codecanyon') {
                    $url = '<a href="' . esc_url(admin_url('admin.php?page=citadela-directory-settings&citadela_directory_tab=plugin_activation')) . '">(' . esc_html__('Admin &rarr; Citadela Listing &rarr; Plugin Activation', 'citadela') . ')</a>';
                } else {
                    $url = '<a href="' . esc_url(admin_url('options-general.php?page=citadela-api')) . '">(' . esc_html__('Admin &rarr; Settings &rarr; Citadela','citadela') . ')</a>';
                }
                if ($response['code'] == 401) {
                    if (isset($response['body']['former'])) {
                        $message['title'] = esc_html__('Expired membership', 'citadela');
                        $message['message'] = sprintf(
                            /*translators: 1. Start html anchor tag, 2. End html anchor tag */
                            __('Your membership has expired. You can %1$srenew it here%2$s.', 'citadela'), 
                            '<a href="' . Citadela::$url . '/account/subscriptions">', 
                            '</a>'
                        );                        
                    } else if (isset($response['body']['domain'])) {
                        $message['title'] = esc_html__('Purchase Code is already used', 'citadela');
                        $message['message'] = sprintf(
                            /*translators: 1. Link to settings page, 2. Domain name, 3. Start html anchor tag, 4. End html anchor tag  */ 
                            __('Purchase Code %1$s is already used on "%2$s". If you want to use it on this website, you can %3$sderegister it here%4$s.', 'citadela'), 
                            $url, 
                            $response['body']['domain'], 
                            '<a href="' . Citadela::$url . '/account/' . Citadela::$package . '">', 
                            '</a>'
                        );
                    } else {
                        $message['title'] = in_array(Citadela::$package, ['themeforest', 'mojo', 'themely']) ? esc_html__('Invalid Purchase Code for this domain', 'citadela') : esc_html__('Invalid API Key for this domain', 'citadela');
                        $message['message'] = wp_kses_post(sprintf(
                            in_array(Citadela::$package, ['themeforest', 'mojo', 'themely']) 
                                ? /*translators: 1. Link to page */ __('Please enter a valid Purchase Code for this domain. You can configure it in %s.', 'citadela') 
                                : /*translators: 1. Link to page */ __('Please enter a valid API key for this domain. You can configure it in %s.', 'citadela'), 
                            $url
                        ));
                    }
                } else {
                    $message['title'] = in_array(Citadela::$package, ['themeforest', 'mojo', 'themely']) ? esc_html__('We can\'t verify your Purchase Code right now.', 'citadela') : esc_html__('We can\'t verify your API key right now.', 'citadela');
                    $message['message'] = wp_kses_post(sprintf(__('Please try to check your product activation later here:', 'citadela')) . ' ' . $url);
                }
                if (isset($response['body']['message']['title'])) {
                    $message['title'] = $response['body']['message']['title'];
                }
                if (isset($response['body']['message']['message'])) {
                    $message['message'] = $response['body']['message']['message'];
                }
                return $message;
            }
            static function verifyAccount($products, $active = false)
            {
                $responseRaw = wp_remote_post(Citadela::$url . '/core/account/verification', [
                    'body' => json_encode([
                        'key' => Citadela::$key,
                        'domain' => Citadela::$domain,
                        'package' => Citadela::$package,
                        'products' => $products,
                        'active' => $active
                    ])
                ]);
                $response = is_wp_error($responseRaw) ? [
                    'code' => $responseRaw->get_error_code(),
                    'body' => [
                        'message' => [
                            'message' => $responseRaw->get_error_message()
                        ]
                    ]
                ] : [
                    'code' => wp_remote_retrieve_response_code($responseRaw),
                    'body' => json_decode(wp_remote_retrieve_body($responseRaw), true)
                ];
                if ($response['code'] != 200) {
                    $exception = new Exception;
                    $exception->response = $response;
                    throw $exception;
                }
                return $response['body'];
            }
            static function downloadProduct($name)
            {
                Citadela::verifyAccount([$name], true);
                return download_url(Citadela::$url . '/core/products/file?' . http_build_query(['package' => Citadela::$package, 'domain' => Citadela::$domain, 'key' => Citadela::$key, 'name' => $name]));
            }
            static function checkPlugin($name)
            {
                $citadela = strpos($name, 'citadela-') === 0;
                if (in_array($name, array_keys(Citadela::plugins()[$citadela ? 'citadela' : 'others']))) {
                    return Citadela::plugins()[$citadela ? 'citadela' : 'others'][$name];
                }
            }
            static function installAndActivatePlugin($name)
            {
                if (!current_user_can('install_plugins')) {
                    $exception = new Exception;
                    $exception->response = [
                        'code' => 500,
                        'body' => [
                            'message' => [
                                'title' => esc_html__('Permission denied.', 'citadela'),
                                'message' => esc_html__("You don't have permission to install plugins.", 'citadela')
                            ]
                        ]
                    ];
                    throw $exception;
                }
                if ($plugin = Citadela::checkPlugin($name)) {
                    if ($plugin['active']) {
                        return;
                    }
                    activate_plugin($plugin['path']);
                    return;
                }
                set_time_limit(3600);
                require_once(ABSPATH . 'wp-admin/includes/plugin-install.php');
                require_once(ABSPATH . 'wp-admin/includes/plugin.php');
                require_once(ABSPATH . 'wp-includes/pluggable.php');
                require_once(ABSPATH . 'wp-admin/includes/file.php');
                require_once(ABSPATH . 'wp-admin/includes/misc.php');
                require_once(ABSPATH . 'wp-admin/includes/class-wp-upgrader.php');
                require_once(ABSPATH . 'wp-admin/includes/class-automatic-upgrader-skin.php');
                if (strpos($name, 'citadela-') === 0) {
                    if ($name === 'citadela-directory') {
                        try {
                            Citadela::verifyAccount([$name], true);
                        } catch (Exception $exception) {
                            return;
                        }
                    } else {
                        Citadela::verifyAccount([$name], true);
                    }
                    $api = new StdClass;
                    $api->download_link = Citadela::$url . '/core/products/file?' . http_build_query(['package' => Citadela::$package, 'domain' => Citadela::$domain, 'key' => Citadela::$key, 'name' => $name]);
                } else {
                    $api = plugins_api('plugin_information', ['slug' => $name]);
                }
                $result = (new Plugin_Upgrader(new Automatic_Upgrader_Skin($api)))->install($api->download_link);
                if (!isset($result) || is_wp_error($result)) {
                    $exception = new Exception;
                    $exception->response = [
                        'code' => 500,
                        'body' => [
                            'message' => [
                                'title' => esc_html__('Error installing plugin', 'citadela'),
                                'message' => esc_html__('There was an error while installing plugin.', 'citadela')
                            ]
                        ]
                    ];
                    throw $exception;
                }
                unset(Citadela::$cache['plugins']);
                $plugin = Citadela::checkPlugin($name);
                activate_plugin($plugin['path']);
            }
        }
        Citadela::init();
    };
}
add_action('after_setup_theme', function () {
    if (!class_exists('Citadela')) {
        global $citadela;
        $citadela[max(array_keys($citadela))]();
    }
});
