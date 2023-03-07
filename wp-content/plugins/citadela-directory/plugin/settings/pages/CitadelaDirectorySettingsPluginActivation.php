<?php

class CitadelaDirectorySettingsPluginActivation extends CitadelaDirectorySettings
{
    public static function create()
    {
        add_settings_section(
            'plugin_activation',
            null,
            [__CLASS__, 'content'],
            \CitadelaDirectorySettings::$settingsPageId
        );
    }
    public static function content()
    {
        ?>
        <div class="wrap">
            <p><?php esc_html_e('Thank you for purchasing Citadela Listing plugin from Codecanyon. Please enter Envato Purchase code to activate the plugin.', 'citadela'); ?></p>
            <form method="post">
                <?php
                settings_fields('citadela-api');
                do_settings_sections('citadela-api');
                ?>
                <table class="form-table" role="presentation">
                    <tbody>
                        <tr>
                            <th scope="row">
                                <label for="<?php echo esc_attr( 'citadela_api_key' ); ?>"><?php esc_html_e('Purchase code', 'citadela') ?></label>
                            </th>
                            <td>
                                <input type="text" class="regular-text" name="<?php echo esc_attr( 'citadela_api_key' ); ?>" id="<?php echo esc_attr( 'citadela_api_key' ); ?>" value="<?php echo esc_attr(Citadela::$key); ?>">
                                <p class="description"><?php echo wp_kses_post(sprintf( /*translators: 1. start html anchor tag, 2. end html anchor tag */ __('You can find Purchase Code in your %1$sEnvato account &rarr; Downloads%2$s under the Download button.', 'citadela'), '<a href="https://codecanyon.net/downloads" target="_blank">', '</a>' )) ?></p>
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
    }
    public static function settings()
    {
        return [];
    }
}