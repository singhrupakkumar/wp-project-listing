<?php

namespace Citadela\Directory;

class Events {
    public static function run()
    {
        add_action('tribe_events_eventform_top', function ($id) {
            ?>
            <table class="eventtable">
                <tbody>
                <tr>
                    <td colspan="2" class="tribe_sectionheader">
                        <h4><?php esc_html_e('Citadela Listing', 'citadela-directory'); ?></h4>
                    </td>
                </tr>
                <tr>
                    <td class='tribe-table-field-label'><?php esc_html_e('Item:', 'citadela-directory'); ?></td>
                    <td>
                        <?php self::renderSelectItem($id); ?>
                    </td>
                </tr>
                </tbody>
            </table>
            <?php
        });
        add_action('tribe_events_update_meta', function ($id, $data) {
            update_post_meta($id, '_EventDirectoryItem', $data['EventDirectoryItem']);
        }, 10, 2);
        add_action('add_meta_boxes', function () {
            if (function_exists('tribe_get_option') && tribe_get_option('toggle_blocks_editor')) {
                add_meta_box(
                    'tribe_events_directory_item',
                    __('Citadela Listing', 'citadela-directory'),
                    function ($event) {
                        self::renderSelectItem($event->ID);
                    },
                    'tribe_events',
                    'side',
                    'high'
                );
            }
        });
        add_action('save_post_tribe_events', function ($id) {
            if (isset($_POST['EventDirectoryItem'])) {
                update_post_meta($id, '_EventDirectoryItem', $_POST['EventDirectoryItem']);
            }
        });
        add_action('tribe_events_single_meta_details_section_start', function () {
            global $post;
            $id = get_post_meta($post->ID, '_EventDirectoryItem', true);
            if (!empty($id)) {
                $item = get_post($id);
                ?>
                <dt> <?php esc_html_e('Organizer:', 'citadela-directory'); ?> </dt>
                <dd>
                    <a href="<?php echo esc_attr(get_permalink($item->ID)) ?>"><?php echo esc_html($item->post_title); ?></a>
                </dd>
            <?php }
        });
    }
    private static function renderSelectItem($id)
    {
        $value = get_post_meta($id, '_EventDirectoryItem', true);
	$num_items = wp_count_posts( 'citadela-item' )->publish;

	if ($num_items > 500) {
	?>
		<input type="text" id="EventDirectoryItem" name="EventDirectoryItem" value="<?php echo $value ?>"> 
	<?php
                echo __('There are '.$num_items.' items in the database. Dropdown with items is disabled for performance reasons. Please enter Item ID manually.', 'citadela-directory');
	} else {
		$items = get_posts([
		    'numberposts' => -1,
		    'post_type' => 'citadela-item'
		]);
		?>
		<select id="EventDirectoryItem" name="EventDirectoryItem" class="tribe-dropdown" data-prevent-clear>
		    <option value=""><?php esc_html_e('Select an Item', 'citadela-directory'); ?></option>
		    <?php
		    foreach ($items as $item) {
			echo '<option value="' . esc_attr($item->ID) . '"' . selected($item->ID == $value) . '>' . esc_html('#' . $item->ID . ' - ' . $item->post_title) . '</option>';
		    }
		    ?>
		</select>
        <?php
	}
    }
}
