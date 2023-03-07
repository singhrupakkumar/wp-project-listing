<?php

namespace Citadela\Directory\Migration;

use Citadela\Directory\Migration;

class Settings_Tab
{

    public static function create()
    {
        add_settings_section(
            'citadela_section_migration',
            esc_html__('Restart migration', 'citadela-directory'),
            [__CLASS__, 'content'],
            \CitadelaDirectorySettings::$settingsPageId
        );
    }



    public static function content()
    {
        echo \CitadelaDirectorySettings::sectionStart(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        ?>
        <div>
            <?php if(Migration::is_in_progress()): ?>
                <p><?php esc_html_e('Migration is currently in progress. You will be able to restart the migration when it is finished.', 'citadela-directory') ?></p>
                <p><span class="button button-primary button-disabled"><?php esc_html_e('Restart migration', 'citadela-directory') ?></span></p>
            <?php else: ?>
                <p><?php esc_html_e('Clicking on button below will restart migration process. All automatically migrated items, categories and locations will be deleted. All manually created items, categories and locations will not be deleted. Migration process will start automatically after that.', 'citadela-directory') ?></p>
                <p><a href="<?php echo esc_url( Migration::reset_url() ); ?>" class="button button-primary"><?php esc_html_e('Restart migration', 'citadela-directory') ?></a></p>
            <?php endif; ?>
        </div>
        <?php
        echo \CitadelaDirectorySettings::sectionEnd(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    }



    public static function settings()
    {
        return [];
    }
}

