<?php
/**
 * Citadela Listing Settings - Item Reviews page
 *
 */

class CitadelaDirectorySettingsItemReviews extends CitadelaDirectorySettings {

	private static $sectionPrefix = 'citadela_section_item_reviews_';
	private static $options;


	public function __construct(){
		throw new LogicException(__CLASS__ . ' is a static class. Can not be instantiate.');
	}


	public static function create(){
		
		self::$options = CitadelaDirectorySettings::$currentOptions;

		register_setting(
			CitadelaDirectorySettings::$settingsPageId,
			CitadelaDirectorySettings::$settingsKey,
			array(
				'sanitize_callback' => array(__CLASS__, 'sanitize'),
			)
		);

		self::createSections();

		$settings = self::settings();
		if( $settings ){
			CitadelaDirectorySettings::addSettings($settings);
		}
		
	}


	private static function createSections(){

		add_settings_section(
			self::$sectionPrefix.'enable',
            null,
			[__CLASS__, 'enable_section'],
			CitadelaDirectorySettings::$settingsPageId
		);
		
	}

	public static function enable_section(){
		echo CitadelaDirectorySettings::sectionStart(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		?>
		<p>
			<?php esc_html_e('Item Reviews extends WordPress Comments to allow website visitors leave a review and rating for Item Posts.', 'citadela-directory'); ?>
		</p> 
		<?php
		CitadelaDirectorySettings::addSetting( 'enable', [
				'title' 	=> esc_html__('Enable', 'citadela-directory'),
				'label' 	=> esc_html__('Turn on Item Reviews for listing items', 'citadela-directory'),
				'desc' 		=> '',
				'type' 		=> 'checkbox',
				'section'	=> self::$sectionPrefix.'enable',
				'default'	=> 0,
		] );

		CitadelaDirectorySettings::addSetting( 'rating_stars_color', [
				'title' 	=> esc_html__('Rating stars color', 'citadela-directory'),
				'desc' 		=> '',
				'type' 		=> 'colorpicker',
				'section'	=> self::$sectionPrefix.'enable',
				'default'	=> '',
		] );
        
        echo CitadelaDirectorySettings::sectionEnd(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	
	public static function settings(){
		return [];
	}


	public static function sanitize( $data ){
		
		$data['enable'] = isset($data['enable']) && $data['enable'] !== false ? true : false;

		return $data;
	}

}
