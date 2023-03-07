<?php

/**
 * Citadela Listing Settings screen
 *
 */

class CitadelaDirectorySettings {

	// currently opened tab id
	public static $currentTab;

	// directory options in db
	public static $currentOptions;

	// id for settings page
	public static $settingsPageId = 'citadela-directory-settings';

	// key for options of currently opened tab
	public static $settingsKey;

	// prefix used for option id in db
	public static $settingPrefix = 'citadela_directory_';

	public function __construct(){
		throw new LogicException(__CLASS__ . ' is a static class. Can not be instantiate.');
	}

	public static function init(){

		add_filter( 'admin_body_class', array( __CLASS__, 'admin_body_class' ) );
		add_action( 'admin_menu', array(__CLASS__, 'createMenu'), 12 );
		self::$currentTab = $currentTab = empty( $_GET['citadela_directory_tab'] ) ? 'general' : sanitize_title( wp_unslash( $_GET['citadela_directory_tab'] ) );
		self::$settingsKey = self::$settingPrefix . $currentTab;
		self::$currentOptions = self::getTabSettings($currentTab);

		add_action( 'admin_init', array(__CLASS__, 'createSettings'), 10 );

		add_action( 'citadela_render_icon', function(){
			echo self::icon(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		});
	}

	public static function admin_body_class( $classes ){
		$screen = get_current_screen();
		if( $screen && $screen->base == 'toplevel_page_citadela-directory-settings' ){
			$classes .= ' citadela-settings-page citadela-listing-settings';
		}
		return $classes;
	}

	public static function createMenu(){
		$parentMenuItem = (object) array(
			'page_title'	=> esc_html__('Citadela Listing', 'citadela-directory'),
			'menu_title'	=> esc_html__('Citadela Listing', 'citadela-directory'),
			'capability'	=> 'edit_dashboard', //needs to be changed for future
			'menu_slug'		=> self::$settingsPageId,
			'function'		=> array(__CLASS__, 'createSettingsPage'),
			'icon_url'		=> 'data:image/svg+xml;base64,' . base64_encode(self::getCitadelaSvgLogo()),
			'position'		=> 25 //after Comments menu
		);

		add_menu_page( $parentMenuItem->page_title, $parentMenuItem->menu_title, $parentMenuItem->capability, $parentMenuItem->menu_slug, $parentMenuItem->function,$parentMenuItem->icon_url, $parentMenuItem->position );

		$childMenuItem = (object) array(
			'parent_slug'	=> self::$settingsPageId,
			'page_title'	=> esc_html__('Citadela Listing', 'citadela-directory'),
			'menu_title'	=> esc_html__('Citadela Listing', 'citadela-directory'),
			'capability'	=> 'edit_dashboard', //needs to be changed for future
			'menu_slug'		=> self::$settingsPageId,
			'function'		=> array(__CLASS__, 'createSettingsPage'),
		);


		add_submenu_page( $childMenuItem->parent_slug, $childMenuItem->page_title, $childMenuItem->menu_title, $childMenuItem->capability, $childMenuItem->menu_slug, $childMenuItem->function );
	}

	public static function getCitadelaSvgLogo( $filename = 'citadela-logo-directory.svg' ){
		$file = CitadelaDirectory::getInstance()->paths->dir->design . '/images/' . $filename;
		$svg = file_get_contents($file);

		return $svg;
	}

	public static function createSettingsPage(){
		
		 if ( isset( $_GET['settings-updated'] ) ) {
			 // add settings saved message with the class of "updated"
			add_settings_error( 'citadela_settings_message', 'citadela_settings_message', esc_html__( 'Settings Saved', 'citadela-directory' ), 'updated' );
		 	
		 	// maybe needed flush rewrite slug, after post slug change
		 	if( in_array( self::$currentTab, [ 'item_detail' ] ) ){
		 		flush_rewrite_rules();
		 	}


		 }
		 // show error/update messages
		 settings_errors( 'citadela_settings_message' );

		?>

		<div class="wrap citadela-settings-wrap">

			<h1>
				<?php echo self::icon(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<?php echo esc_html( get_admin_page_title() ); ?>&nbsp;&sdot;&nbsp;<?php echo esc_html( self::getCurrentTabTitle() ); ?>
			</h1>

			<div class="citadela-settings-content">
				<?php echo self::createNavigationTabs();  // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>

				<div class="citadela-settings tab-<?php echo self::$currentTab; ?>">
					<form action="options.php?citadela_directory_tab=<?php echo self::$currentTab; ?>" method="post">

						<?php
						settings_fields( self::$settingsPageId );
						// output setting sections and their fields
						self::renderSettings( self::$settingsPageId );

						if(self::shouldDisplaySubmitButton()){
							// output save settings button
							submit_button( esc_html__('Save Settings', 'citadela-directory') );
						}
						?>

					</form>
				</div>
			</div>
		</div>

		<?php
	}

	//replacement for default do_settings_sections()
	public static function renderSettings( $page ) {
		global $wp_settings_sections, $wp_settings_fields;

		if ( ! isset( $wp_settings_sections[ $page ] ) ) {
			return;
		}

		foreach ( (array) $wp_settings_sections[ $page ] as $section ) {
			$section_id = esc_attr( str_replace( '_', '-', $section['id'] ) );
			echo "<div id=\"{$section_id}\" class=\"citadela-section {$section_id}\">"; 
			if ( $section['title'] ) {
				echo "<h2 class=\"section-title\">". esc_html( $section['title'] ) . "</h2>\n";
			}

			if ( $section['callback'] ) {
				call_user_func( $section['callback'], $section );
			}

			if ( ! isset( $wp_settings_fields ) || ! isset( $wp_settings_fields[ $page ] ) || ! isset( $wp_settings_fields[ $page ][ $section['id'] ] ) ) {
				echo '</div>';
				continue;
			}
			echo '<table class="form-table">';
				do_settings_fields( $page, $section['id'] );
			echo '</table>';
			echo '</div>';
		}
	}

	public static function createSettings(){

		$currentTab = self::$currentTab;
		$navigationTabs = self::getNavigationTabs();
	
		//do not register settings for settings tab which have custom settings in own class
		if( ! isset($navigationTabs[$currentTab]['customSettings']) || $navigationTabs[$currentTab]['customSettings'] === false ){
			register_setting(
				self::$settingsPageId,
				self::$settingsKey,
				array(
					'sanitize_callback' => array(__CLASS__, 'sanitizeSavedSettings'),
				)
			);
		}


		if(isset($navigationTabs[$currentTab]['className'])){
			$className = $navigationTabs[$currentTab]['className'];
			$className::create();
		}

	}

	public static function sanitizeSavedSettings( $data ){
		if(!$data) return;

		//compare settings from config with data for save, validation needed because of checkboxes not saved in database when are not checked

		$tabs = self::getNavigationTabs();
		$className = $tabs[self::$currentTab]['className'];
		$tabSettingsConfig = $className::settings();

		foreach ($tabSettingsConfig as $settingId => $settingData) {

			if( $settingData['type'] == 'checkbox' ){
				$data[$settingId] = isset($data[$settingId]) && $data[$settingId] !== false ? true : false;
			}

		}
		return $data;
	}


	public static function createNavigationTabs() {
		$result = '<nav class="nav-tab-wrapper citadela-nav-tab-wrapper">';

		foreach (self::getNavigationTabs() as $key => $data) {
			if (!($key === 'plugin_activation' && Citadela::$package_envato !== 'codecanyon')) {
				$url = esc_html( admin_url( "admin.php?page=citadela-directory-settings&citadela_directory_tab={$key}" ) );
				$result .= '<a href="' . esc_url( $url ) . '" class="nav-tab ' . esc_attr( self::$currentTab === $key ? 'nav-tab-active' : '' ) . '">' . esc_html( $data['tab'] ) . '</a>';
			}
		}

		$result .= '</nav>';

		return $result;
	}


	public static function getNavigationTabs() {
		$tabs = [
			'general' => [
				'tab' => esc_html__('Integrations', 'citadela-directory'),
				'title' => esc_html__('Integrations', 'citadela-directory'),
				'submitButton' => true,
				'className' => 'CitadelaDirectorySettingsGeneral',
            ],

            'subscriptions' => [
				'tab' => esc_html__('Subscriptions', 'citadela-directory'),
				'title' => esc_html__('Subscriptions', 'citadela-directory'),
				'submitButton' => CitadelaDirectory::getInstance()->Subscriptions_instance && CitadelaDirectory::getInstance()->Subscriptions_instance->enabled_subscription_plugin ? false : true,
				'className' => 'CitadelaDirectorySettingsSubscriptions',
			],
			'easyadmin' => [
				'tab' => esc_html__('Easy Admin', 'citadela-directory'),
				'title' => esc_html__('Easy Admin', 'citadela-directory'),
				'submitButton' => true,
				'className' => 'CitadelaDirectorySettingsEasyAdmin',
			],
			'item_reviews' => [
				'tab' => esc_html__('Item Reviews', 'citadela-directory'),
				'title' => esc_html__('Item Reviews', 'citadela-directory'),
				'submitButton' => true,
				'customSettings' => true, //settings are registered in custom feature class
				'className' => 'CitadelaDirectorySettingsItemReviews',
			],
			'item_extension' => [
				'tab' => esc_html__('Item Extension', 'citadela-directory'),
				'title' => esc_html__('Item Extension', 'citadela-directory'),
				'submitButton' => true,
				'customSettings' => true,
				'className' => 'CitadelaDirectorySettingsItemExtension',
			],
			'item_detail' => [
				'tab' => esc_html__('Item Detail', 'citadela-directory'),
				'title' => esc_html__('Item Detail', 'citadela-directory'),
				'submitButton' => true,
				'className' => 'CitadelaDirectorySettingsItemDetail',
			],
			'claim_listing' => [
				'tab' => esc_html__('Claim Listing', 'citadela-directory'),
				'title' => esc_html__('Claim Listing', 'citadela-directory'),
				'submitButton' => true,
				'className' => 'CitadelaDirectorySettingsClaimListing',
			],
			'plugin_activation' => [
				'tab' => esc_html__('Plugin Activation', 'citadela-directory'),
				'title' => esc_html__('Plugin Activation', 'citadela-directory'),
				'className' => 'CitadelaDirectorySettingsPluginActivation',
			]
		];

        if(\Citadela\Directory\Migration::is_done()){
			$tabs['migration'] = array(
				'tab' => esc_html__('Data migration', 'citadela-directory'),
				'title' => esc_html__('Data migration', 'citadela-directory'),
				'submitButton' => false,
				'className' => 'Citadela\Directory\Migration\Settings_Tab',
			);
        }

		return $tabs;
	}


	public static function getCurrentTabTitle(){
		$navigationTabs = self::getNavigationTabs();
		return $navigationTabs[self::$currentTab]['title'];
	}


	public static function shouldDisplaySubmitButton(){
		return (
			isset(self::getNavigationTabs()[self::$currentTab]['submitButton'])
			and self::getNavigationTabs()[self::$currentTab]['submitButton']
		);
	}


	public static function addSettings( $settings ){
		foreach ($settings as $settingId => $settingData) {
			self::addSetting($settingId, $settingData);
		}
	}


	public static function addSetting( $settingId, $settingData ){
		$setting = (object) $settingData;
		$setting->id = $settingId;
		$callback = self::getSettingCallback($setting->type);
		add_settings_field(
			$setting->id,
			$setting->title,
			array( __CLASS__, $callback),
			self::$settingsPageId,
			$setting->section,
			[
				'label' => isset($setting->label) ? $setting->label : '',
				'label_for' => ! in_array( $setting->type, [ 'checkbox', 'radio-list' ] ) ? $setting->id : '',
				'class' => "citadela-control type-{$setting->type}" . (isset($setting->class) ? " ".$setting->class : ''),
				'id' => $setting->id,
				'desc' => isset($setting->desc) ? $setting->desc : '',
				'default' => isset($setting->default) ? $setting->default : '',
				'options' => isset($setting->options) ? $setting->options : array(),
				'params' => isset($setting->params) ? $setting->params : array(),
				'opacity' => isset($setting->opacity) ? $setting->opacity : false,
				'input_class' => "field-type-{$setting->type}",
			]
		);

	}



	public static function getSettingCallback( $settingType ){
		$callbacks = array(
				'text'		=> 'renderInputText',
				'url'		=> 'renderInputUrl',
				'email'		=> 'renderInputEmail',
				'select'	=> 'renderInputSelect',
				'number'	=> 'renderInputNumber',
				'image'		=> 'renderInputImage',
				'textarea'	=> 'renderInputTextarea',
				'checkbox'	=> 'renderInputCheckbox',
				'colorpicker'	=> 'renderInputColorpicker',
				'background'	=> 'renderInputBackground',
			);
		return $callbacks[$settingType];
	}



	public static function getTabSettings( $tabId ){
		$tabSettings = get_option( self::$settingPrefix . $tabId );
		if(!$tabSettings){
			$tabs = self::getNavigationTabs();
			//tab options are not in db, get default values
			$className = $tabs[$tabId]['className'];
			$tabSettingsConfig = $className::settings();
			$tabSettings = array();
			foreach ($tabSettingsConfig as $settingId => $settingData) {
				$tabSettings[$settingId] = isset($settingData['default']) ? $settingData['default'] : '';
			}
		}
		return $tabSettings;
	}


	public static function icon( $size = 32 ){
		return sprintf(
			'<i></i>',
			$size,
			'data:image/svg+xml;base64,' . base64_encode( self::getCitadelaSvgLogo() )
		);
	}


	/*
	*	Custom Setting Inputs
	*/


	public static function renderInputImage( $args ){

		$options = self::$currentOptions;
		$settingsKey = self::$settingsKey;

		$inputId = $args['id'];
		$attrName = $settingsKey.'['.esc_attr( $inputId ).']';

		if( $options && isset( $options[$inputId] ) ){
			$value = $options[$inputId];
		}else{
			$value = isset($args['default']) ? $args['default'] : '';
		}

		if( !empty( $args['params'] ) && isset( $args['params']['text_input'] ) && $args['params']['text_input'] === false ){
			$inputType = "hidden";
		}else{
			$inputType = "text";
		}

		$deleteButton = false;
		if( !empty( $args['params'] ) && isset( $args['params']['delete'] ) && $args['params']['delete'] === true ){
			$deleteButton = true;
		}
		?>
		<div class="citadela-control-image">
			<div class="citadela-image-container">
				<div class="citadela-image-select-container">
					<input
						type="<?php echo esc_attr( $inputType ); ?>"
						id="<?php echo esc_attr( $inputId ); ?>"
						class="image-url <?php echo esc_attr( $args['input_class'] ); ?>"
						name="<?php echo esc_attr( $attrName ); ?>"
						value="<?php echo esc_attr( $value ); ?>"
						/>

					<input
						type="button"
						class="citadela-select-image-button button button-primary"
						value="<?php esc_html_e('Select Image', 'citadela-directory'); ?>"
						id="<?php echo esc_attr( $inputId ).'-media-button'; ?>"
						/>

					<?php if( $deleteButton ) : ?>
						<input
							type="button"
							class="citadela-delete-image-button button button-secondary <?php if( $value == '' ) { echo esc_attr( 'hidden' ); } ?>"
							value="<?php esc_html_e('Remove Image', 'citadela-directory'); ?>"
							id="<?php echo esc_attr( $inputId ).'-delete-button'; ?>"
							/>					
					<?php endif; ?>
				</div>

				<div class='citadela-image-preview-container' style="">
					<?php if($value != ''): ?>
						<img src="<?php echo esc_url( $value ); ?>"/>
					<?php endif; ?>
				</div>
			</div>
		</div>

		<?php if($args['desc']): ?>
			<p class="description">
				<?php esc_html_e( $args['desc'] ); ?>
			</p>
		<?php endif; ?>

	<?php
	}



	public static function renderInputNumber( $args ){

		$options = self::$currentOptions;
		$settingsKey = self::$settingsKey;

		$inputId = $args['id'];
		$attrName = $settingsKey.'['.esc_attr( $inputId ).']';

		if( $options && isset( $options[$inputId] ) ){
			$value = $options[$inputId];
		}else{
			$value = isset($args['default']) ? $args['default'] : '';
		}

		$step = isset($args['params']['step']) ? $args['params']['step'] : '1';
		$min = isset($args['params']['min']) ? $args['params']['min'] : '0';
		$max = isset($args['params']['max']) ? $args['params']['max'] : '';

		?>

		<input
			type="number"
			id="<?php echo esc_attr( $inputId ); ?>"
			class="<?php echo esc_attr( $args['input_class'] ); ?> small-text"
			name="<?php echo esc_attr( $attrName ); ?>"
			value="<?php echo esc_attr( $value ); ?>"
			step="<?php echo esc_attr( $step ); ?>"
			min="<?php echo esc_attr( $min ); ?>"
			max="<?php echo esc_attr( $max ); ?>"
		/>

		<?php if($args['desc']): ?>
			<p class="description">
				<?php esc_html_e( $args['desc'] ); ?>
			</p>
		<?php endif; ?>

	<?php
	}


	public static function renderInputSelect( $args ){

		$options = self::$currentOptions;
		$settingsKey = self::$settingsKey;

		$inputId = $args['id'];
		$attrName = $settingsKey.'['.esc_attr( $inputId ).']';

		if( $options && isset( $options[$inputId] ) ){
			$value = $options[$inputId];
		}else{
			$value = isset($args['default']) ? $args['default'] : '';
		}

		?>

		<select
			id="<?php echo esc_attr( $inputId ); ?>"
			class="<?php echo esc_attr( $args['input_class'] ); ?>"
			name="<?php echo esc_attr( $attrName ); ?>"
		/>

		<?php
		foreach ($args['options'] as $key => $text) {
			$selected = ($key == $value) ? 'selected' : '';
			?>
			<option value="<?php echo esc_attr( $key ); ?>" class="<?php echo esc_attr( $args['label_for'] . '-' . $key ); ?>" <?php selected($value, $key)?> ><?php esc_html_e( $text ); ?></option>
			<?php
		}
		?>
		</select>

		<?php if($args['desc']): ?>
			<p class="description">
				<?php esc_html_e( $args['desc'] ); ?>
			</p>
		<?php endif; ?>


	<?php
	}



	public static function renderInputText( $args ){

		$options = self::$currentOptions;
		$settingsKey = self::$settingsKey;

		$inputId = $args['id'];
		$attrName = $settingsKey.'['.esc_attr( $inputId ).']';

		if( $options && isset( $options[$inputId] ) ){
			$value = $options[$inputId];
		}else{
			$value = isset($args['default']) ? $args['default'] : '';
		}

		?>

		<input
			type="text"
			id="<?php echo esc_attr( $inputId ); ?>"
			class="<?php echo esc_attr( $args['input_class'] ); ?> regular-text"
			name="<?php echo esc_attr( $attrName ); ?>"
			value="<?php echo esc_attr( $value ); ?>" />

		<?php if($args['desc']): ?>
			<p class="description">
				<?php esc_html_e( $args['desc'] ); ?>
			</p>
		<?php endif; ?>


	<?php
	}



	public static function renderInputUrl( $args ){

		$options = self::$currentOptions;
		$settingsKey = self::$settingsKey;

		$inputId = $args['id'];
		$attrName = $settingsKey.'['.esc_attr( $inputId ).']';

		if( $options && isset( $options[$inputId] ) ){
			$value = $options[$inputId];
		}else{
			$value = isset($args['default']) ? $args['default'] : '';
		}

		?>

		<input
			type="url"
			id="<?php echo esc_attr( $inputId ); ?>"
			class="<?php echo esc_attr( $args['input_class'] ); ?> regular-text"
			name="<?php echo esc_attr( $attrName ); ?>"
			value="<?php echo esc_attr( $value ); ?>" />

		<?php if($args['desc']): ?>
			<p class="description">
				<?php esc_html_e( $args['desc'] ); ?>
			</p>
		<?php endif; ?>


	<?php
	}



	public static function renderInputEmail( $args ){

		$options = self::$currentOptions;
		$settingsKey = self::$settingsKey;

		$inputId = $args['id'];
		$attrName = $settingsKey.'['.esc_attr( $inputId ).']';

		if( $options && isset( $options[$inputId] ) ){
			$value = $options[$inputId];
		}else{
			$value = isset($args['default']) ? $args['default'] : '';
		}

		?>

		<input
			type="email"
			id="<?php echo esc_attr( $inputId ); ?>"
			class="<?php echo esc_attr( $args['input_class'] ); ?> regular-text"
			name="<?php echo esc_attr( $attrName ); ?>"
			value="<?php echo esc_attr( $value ); ?>" />

		<?php if($args['desc']): ?>
			<p class="description">
				<?php esc_html_e( $args['desc'] ); ?>
			</p>
		<?php endif; ?>


	<?php
	}



	public static function renderInputCheckbox( $args ){
		$options = self::$currentOptions;
		$settingsKey = self::$settingsKey;
		$inputId = $args['id'];
		$attrName = $settingsKey.'['.esc_attr( $inputId ).']';

		if( $options && isset( $options[$inputId] ) ){
			$value = $options[$inputId];
		}else{
			$value = isset($args['default']) ? $args['default'] : 0;
		}
		?>
		<label>
			<input
				id="<?php echo 'field-', esc_attr( $inputId ); ?>"
				name="<?php echo esc_attr( $attrName ); ?>"
				type="checkbox"
				class="<?php echo esc_attr( $args['input_class'] ); ?>"
				value="<?php echo esc_attr( $value ); ?>"
				<?php echo checked( $value, 1, false ) ?>
			/>
			<?php echo esc_html( $args['label'] ); ?>
		</label>

		<?php if($args['desc']): ?>
			<p class="description">
				<?php esc_html_e( $args['desc'] ); ?>
			</p>
		<?php endif; ?>


	<?php
	}



	public static function renderInputTextarea( $args ){

		$options = self::$currentOptions;
		$settingsKey = self::$settingsKey;

		$inputId = $args['id'];
		$attrName = $settingsKey.'['.esc_attr( $inputId ).']';

		if( $options && isset( $options[$inputId] ) ){
			$value = $options[$inputId];
		}else{
			$value = isset($args['default']) ? $args['default'] : '';
		}

		?>

		<textarea
			type="text"
			id="<?php echo esc_attr( $inputId ); ?>"
			class="<?php echo esc_attr( $args['input_class'] ); ?> regular-text"
			name="<?php echo esc_attr( $attrName ); ?>"
		><?php echo esc_textarea( $value ); ?></textarea>

		<?php if($args['desc']): ?>
			<p class="description">
				<?php esc_html_e( $args['desc'] ); ?>
			</p>
		<?php endif; ?>


	<?php
	}


	public static function renderInputBackground( $args ){

		$options = self::$currentOptions;
		$settingsKey = self::$settingsKey;

		$inputId = $args['id'];
		$attrName = $settingsKey.'['.esc_attr( $inputId ).']';

		if( $options && isset( $options[$inputId] ) ){
			$color = $options[$inputId]['color'];
			$image = $options[$inputId]['image'];
			$repeat = $options[$inputId]['repeat'];
			$position = $options[$inputId]['position'];
			$scroll = $options[$inputId]['scroll'];
			$size = $options[$inputId]['size'];
		}else{
			$color = isset($args['default']['color']) ? $args['default']['color'] : '';
			$image = isset($args['default']['image']) ? $args['default']['image'] : '';
			$repeat = isset($args['default']['repeat']) ? $args['default']['repeat'] : '';
			$position = isset($args['default']['position']) ? $args['default']['position'] : '';
			$scroll = isset($args['default']['scroll']) ? $args['default']['scroll'] : '';
			$size = isset($args['default']['size']) ? $args['default']['size'] : '';
		}
		?>
		<div class="citadela-control-background">
			<?php 
			// color part
			$format = ( isset($args['default']['opacity']) && $args['default']['opacity'] ) ? 'rgba' : 'hex';
			$hex = CitadelaDirectory::rgba2hex($color);
			$opacityClass = ($format == "hex") ? '' : 'has-opacity';
			?>
			<div class="citadela-control-colorpicker">

				<p class="label"><?php esc_html_e('Background color', 'citadela-directory') ?></p>

				<div class="citadela-colorpicker-container <?php echo esc_attr( $opacityClass ); ?>">
					<span class="citadela-colorpicker-preview"><i style="background-color: <?php echo esc_attr( $color ); ?>"></i></span>
					<input type="text" class="citadela-colorpicker-color" data-color-format="<?php echo esc_attr( $format ); ?>" id="<?php echo esc_attr( $inputId ).'_color'; ?>" value="<?php echo esc_attr( $hex->hex ); ?>">
					<input type="hidden" class="citadela-colorpicker-storage" name="<?php echo esc_attr( $attrName.'[color]' ); ?>" value="<?php echo esc_attr( $color ); ?>">

				<?php if($format != "hex"): ?>
					<input type="number" step="1" min="0" max="100" class="citadela-colorpicker-opacity" value="<?php echo esc_attr( $hex->opacity ); ?>"><span class="citadela-unit"> %</span>
				<?php endif; ?>
				</div>
			</div>

			<?php
			

			// image part
			
			$deleteButton = true;
			?>
			<div class="citadela-control-image">
				
				<p class="label"><?php esc_html_e('Background image', 'citadela-directory') ?></p>
				<div class="citadela-image-container">
					<div class="citadela-image-select-container">
						<input
							type="hidden"
							id="<?php echo esc_attr( $inputId ).'_image'; ?>"
							class="image-url <?php echo esc_attr( $args['input_class'] ); ?>"
							name="<?php echo esc_attr( $attrName.'[image]' ); ?>"
							value="<?php echo esc_attr( $image ); ?>"
							/>

						<input
							type="button"
							class="citadela-select-image-button button button-primary"
							value="<?php esc_html_e('Select Image', 'citadela-directory'); ?>"
							id="<?php echo esc_attr( $inputId ).'_image-media-button'; ?>"
							/>

						<?php if( $deleteButton ) : ?>
							<input
								type="button"
								class="citadela-delete-image-button button button-secondary <?php if( $image == '' ) { echo esc_attr( 'hidden' ); } ?>"
								value="<?php esc_html_e('Remove Image', 'citadela-directory'); ?>"
								id="<?php echo esc_attr( $inputId ).'_image-delete-button'; ?>"
								/>					
						<?php endif; ?>
					</div>

					<div class='citadela-image-preview-container' style="">
						<?php if($image != ''): ?>
							<img src="<?php echo esc_url( $image ); ?>"/>
						<?php endif; ?>
					</div>
				</div>
			</div>


			<div class="citadela-background-select-params">
				<?php
				// Repeat background parameter

				$repeatOptions = [
					'repeat' 	=> esc_html__('Repeat', 'citadela-directory'), 
					'no-repeat' => esc_html__('No repeat', 'citadela-directory'), 
					'repeat-x' 	=> esc_html__('Repeat horizontally', 'citadela-directory'), 
					'repeat-y' 	=> esc_html__('Repeat vertically', 'citadela-directory')
				];
				?>
				<div class="select-repeat">

					<p class="label"><?php esc_html_e('Image repeat', 'citadela-directory') ?></p>

					<select
						id="<?php echo esc_attr( $inputId ).'_repeat'; ?>"
						class="background-repeat"
						name="<?php echo esc_attr( $attrName.'[repeat]' ); ?>"
					/>
						<?php					
						foreach ($repeatOptions as $key => $text) {
							$selected = ($key == $repeat) ? 'selected' : '';
							?>
							<option value="<?php echo esc_attr( $key ); ?>" <?php selected($repeat, $key)?> ><?php esc_html_e( $text ); ?></option>
							<?php
						}
						?>
					</select>
				</div>

				<?php
				// Position background parameter

				$positionOptions = [
					'top left' 		=> esc_html__('Top left', 'citadela-directory'),
					'top' 			=> esc_html__('Top', 'citadela-directory'),
					'top right' 	=> esc_html__('Top right', 'citadela-directory'),
					'center left'	=> esc_html__('Center left', 'citadela-directory'),
					'center'		=> esc_html__('Center', 'citadela-directory'),
					'center right' 	=> esc_html__('Center right', 'citadela-directory'),
					'bottom left'	=> esc_html__('Bottom left', 'citadela-directory'),
					'bottom'		=> esc_html__('Bottom', 'citadela-directory'),
					'bottom right' 	=> esc_html__('Bottom right', 'citadela-directory'),
				];
				?>
				<div class="select-position">

					<p class="label"><?php esc_html_e('Image position', 'citadela-directory') ?></p>

					<select
						id="<?php echo esc_attr( $inputId ).'_position'; ?>"
						class="background-position"
						name="<?php echo esc_attr( $attrName.'[position]' ); ?>"
					/>
						<?php					
						foreach ($positionOptions as $key => $text) {
							$selected = ($key == $position) ? 'selected' : '';
							?>
							<option value="<?php echo esc_attr( $key ); ?>" <?php selected($position, $key)?> ><?php esc_html_e( $text ); ?></option>
							<?php
						}
						?>
					</select>
				</div>

				<?php
				// Position background parameter

				$scrollOptions = [
					'scroll' 	=> esc_html__('Scroll', 'citadela-directory'),
					'fixed' 	=> esc_html__('Fixed', 'citadela-directory'),
				];
				?>
				<div class="select-scroll">

					<p class="label"><?php esc_html_e('Image scroll', 'citadela-directory') ?></p>

					<select
						id="<?php echo esc_attr( $inputId ).'_scroll'; ?>"
						class="background-scroll"
						name="<?php echo esc_attr( $attrName.'[scroll]' ); ?>"
					/>
						<?php					
						foreach ($scrollOptions as $key => $text) {
							$selected = ($key == $scroll) ? 'selected' : '';
							?>
							<option value="<?php echo esc_attr( $key ); ?>" <?php selected($scroll, $key)?> ><?php esc_html_e( $text ); ?></option>
							<?php
						}
						?>
					</select>
				</div>

				<?php
				// Size background parameter

				$sizeOptions = [
					'cover' 	=> esc_html__('Cover', 'citadela-directory'),
					'100% auto' => esc_html__('Full horizontal', 'citadela-directory'),
					'auto 100%' => esc_html__('Full vertical', 'citadela-directory'),
					'auto' 		=> esc_html__('Default size', 'citadela-directory'),
				];
				?>
				<div class="select-size">

					<p class="label"><?php esc_html_e('Image size', 'citadela-directory') ?></p>

					<select
						id="<?php echo esc_attr( $inputId ).'_size'; ?>"
						class="background-size"
						name="<?php echo esc_attr( $attrName.'[size]' ); ?>"
					/>
						<?php					
						foreach ($sizeOptions as $key => $text) {
							$selected = ($key == $size) ? 'selected' : '';
							?>
							<option value="<?php echo esc_attr( $key ); ?>" <?php selected($size, $key)?> ><?php esc_html_e( $text ); ?></option>
							<?php
						}
						?>
					</select>
				</div>

			</div>
		</div>
		<?php if($args['desc']): ?>
			<p class="description">
				<?php esc_html_e( $args['desc'] ); ?>
			</p>
		<?php endif; ?>
	<?php
	}


	public static function renderInputColorpicker( $args ){

		$options = self::$currentOptions;
		$settingsKey = self::$settingsKey;

		$inputId = $args['id'];
		$attrName = $settingsKey.'['.esc_attr( $inputId ).']';

		if( $options && isset( $options[$inputId] ) ){
			$value = $options[$inputId];
		}else{
			$value = isset($args['default']) ? $args['default'] : '';
		}

		$format = ( isset($args['opacity']) && $args['opacity'] ) ? 'rgba' : 'hex';
		$hex = CitadelaDirectory::rgba2hex($value);

		$opacityClass = ($format == "hex") ? '' : 'has-opacity';
		?>
		<div class="citadela-control-colorpicker">
			<div class="citadela-colorpicker-container <?php echo esc_attr( $opacityClass ); ?>">
				<span class="citadela-colorpicker-preview"><i style="background-color: <?php echo esc_attr( $value ); ?>"></i></span>
				<input type="text" class="citadela-colorpicker-color" data-color-format="<?php echo esc_attr( $format ); ?>" id="<?php echo esc_attr( $inputId ); ?>" value="<?php echo esc_attr( $hex->hex ); ?>">
				<input type="hidden" class="citadela-colorpicker-storage" name="<?php echo esc_attr( $attrName ); ?>" value="<?php echo esc_attr( $value ); ?>">

			<?php if($format != "hex"): ?>
				<input type="number" step="1" min="0" max="100" class="citadela-colorpicker-opacity" value="<?php echo esc_attr( $hex->opacity ); ?>"><span class="citadela-unit"> %</span>
			<?php endif; ?>
			</div>
		</div>

		<?php if($args['desc']): ?>
			<p class="description">
				<?php esc_html_e( $args['desc'] ); ?>
			</p>
		<?php endif; ?>
	<?php
	}

	/*
	*	Sections callback functions - create content between section title and settings
	*/

	public static function sectionStart() {
		return '<div class="section-description">';
	}

	public static function sectionEnd() {
		return '</div>';
	}

	public static function recaptchaSection() {
		echo self::sectionStart(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

		$reCaptchaLink = '<a href="https://www.google.com/recaptcha/intro/v3.html" target="_blank">reCaptcha v3</a>';
		?>
		<p><?php esc_html_e('Google reCaptcha protects your website against spam and other automated form submissions by spam bots. If you are using third party solution to protect forms on your website, ignore following key inputs.', 'citadela-directory'); ?><br><?php
			// translators: %s url reCaptcha v3 page
			echo wp_kses_post( sprintf(__('For more details see %s.', 'citadela-directory'), $reCaptchaLink) ); ?></p>

		<?php

		echo self::sectionEnd(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    }

    public static function googleMapsSection() {
        echo self::sectionStart(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		?>

		<p><?php
			// translators: 1. starting html <a> tag with link to google documentation page, 2. ending html <a> tag
			echo wp_kses_post( sprintf(__('Google Maps API key is required by Google. It is necessary for all websites that use advanced maps features such as displaying listing items on the map. Learn more about how to get API key in the %1$sfollowing tutorial%2$s.', 'citadela-directory'), '<a target="_blank" href="https://www.ait-themes.club/how-to-get-google-maps-api-key/">', '</a>') ); ?></p>

		<?php
		echo self::sectionEnd(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    }

	public static function subscriptionsSection() {
		echo self::sectionStart(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		?>

		<p>
			<?php esc_html_e('Subscriptions feature allows website visitors to purchase Woocommerce subscription product and grants them capabilities to create and manage their own Listing Items.'); ?>
		</p>
		<p>
			<?php esc_html_e('If you would like to use your own WooCommerce Subscriptions plugin, simply deactivate bundled version below and activate your plugin.', 'citadela-directory'); ?>
		</p>

			<?php if( CitadelaDirectory::getInstance()->Subscriptions_instance->enabled_subscription_plugin ) : ?>
				<p><strong><?php esc_html_e("We've noticed WooCommerce Subscriptions plugin is active, subscriptions functionality from Citadela Listing plugin is available.", 'citadela-directory'); ?></strong></p>
			<?php endif; ?>

		<?php
		echo self::sectionEnd(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	public static function easyadminSection() {
		echo self::sectionStart(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		?>

		<p>
			<?php esc_html_e('Easy Admin offer simplified WordPress administration without any unnecessary distractions.', 'citadela-directory'); ?>
			</p>
		<?php
		echo self::sectionEnd(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	public static function claimListingSection() {
		echo self::sectionStart(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		?>
		<p>
			<?php esc_html_e('This feature adds Claim Listing functionality for Listing items', 'citadela-directory'); ?>
			</p>
		<?php
		echo self::sectionEnd(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

}
