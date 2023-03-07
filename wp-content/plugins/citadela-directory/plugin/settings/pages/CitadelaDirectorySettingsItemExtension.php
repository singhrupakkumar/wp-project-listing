<?php
/**
 * Citadela Listing Settings - Item Reviews page
 *
 */

class CitadelaDirectorySettingsItemExtension extends CitadelaDirectorySettings {

	private static $sectionPrefix = 'citadela_section_item_extension_';
	private static $options;


	public function __construct(){
		throw new LogicException(__CLASS__ . ' is a static class. Can not be instantiate.');
	}


	public static function create(){
		
		wp_enqueue_script( 'jquery-ui-sortable' );

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
		add_settings_section(
			self::$sectionPrefix.'general',
            null,
			[__CLASS__, 'general_section'],
			CitadelaDirectorySettings::$settingsPageId
		);
		
	}

	public static function enable_section(){

		echo CitadelaDirectorySettings::sectionStart(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

		?>
		<p>
			<?php esc_html_e('Item Extension allows create additional inputs on edit pages of Item Posts.', 'citadela-directory'); ?>
		</p> 
		<?php

		CitadelaDirectorySettings::addSetting( 'enable', [
				'title' 	=> esc_html__('Enable', 'citadela-directory'),
				'label' 	=> esc_html__('Turn on Item Extension features', 'citadela-directory'),
				'desc' 		=> '',
				'type' 		=> 'checkbox',
				'section'	=> self::$sectionPrefix.'enable',
				'default'	=> 0,
		] );

        echo CitadelaDirectorySettings::sectionEnd(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	public static function general_section(){
		
		echo CitadelaDirectorySettings::sectionStart(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

		add_settings_field(
			'inputs_group',
			esc_html__('Inputs group', 'citadela-directory'),
			[ __CLASS__, 'item_extension_inputs' ],
			CitadelaDirectorySettings::$settingsPageId,
			self::$sectionPrefix.'general',
			[
				'class' => "citadela-control type-item-extension-clone inputs_group",
				'default' =>[
					'group_name' => esc_html__( 'Custom inputs', 'citadela-directory' ),
					'inputs' => [],
				],
			]
		);
        
        echo CitadelaDirectorySettings::sectionEnd(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
	
	public static function item_extension_inputs( $args ){
		
		
		if( self::$options && isset( self::$options['inputs_group'] ) && isset( self::$options['inputs_group']['inputs'] ) && ! empty( self::$options['inputs_group']['inputs'] ) ){
			$inputs = self::$options['inputs_group']['inputs'];
		}else{
			$inputs = $args['default']['inputs'];
		}
		
		$group_name = self::$options && isset( self::$options['inputs_group'] ) && isset( self::$options['inputs_group']['group_name'] ) && self::$options['inputs_group']['group_name'] != "" ? self::$options['inputs_group']['group_name'] : $args['default']['group_name'];;

		$attrName = CitadelaDirectorySettings::$settingsKey."[inputs_group]";
		?>

		<div class="<?php echo esc_attr( $args['class'] ); ?>">

			<div class="setting input-group">
						
				<div class="label-part">
					<label for="<?php echo esc_attr( "{$attrName}[group_name]" ); ?>"><?php esc_html_e( 'Group name','citadela-directory' ); ?></label>
				</div>
				
				<div class="settings-part">
					<input 
						type="text" 
						class="label-input" 
						name="<?php echo esc_attr( "{$attrName}[group_name]" ); ?>" 
						id="<?php echo esc_attr( "{$attrName}[group_name]" ); ?>"
						value="<?php esc_html_e( $group_name ); ?>"
						required
					/>
					<p class="description"><?php esc_html_e( 'Name of settings tab on Item Edit page.', 'citadela-directory' ); ?></p>
				</div>

			</div>

			<div class="citadela-item-extension-inputs citadela-repeater-control">
				<div class="repeater-inputs">
					<?php foreach ($inputs as $key => $input_data){
						self::get_input_html( $attrName, $key, $input_data );
					} ?>
				</div>

				<div class="add-more"><input type="button" class="repeater-add-button button-secondary" value="<?php echo esc_attr( 'Add new input', 'citadela-directory' ); ?>"></div>
				
				<script type="text/html" class="citadela-repeater-template">
					<?php self::get_input_html( $attrName, 'citadela_input_key', [], true ); ?>
				</script>
				
			</div>

		</div>
		<?php
	}

	public static function get_input_html( $attrName, $key, $input_data, $is_template = false ){
		// define defaults for template
		if( $is_template ){
			$input_data = [
				'type' => 'text',
				'label' => '',
				'description' => '',
				'choices_label' => esc_html__( 'Choose option', 'citadela-directory' ),
				'choices' => '',
				'min' => '',
				'max' => '',
				'unit' => '',
				'unit-position' => 'left',
				'use_url_label' => false,
				'use_as_filter' => false,
				'checkbox_filters_group_name' => esc_html__( 'Filters', 'citadela-directory' ),
			];
		}
		?>
		<div class="input-settings-wrapper repeater-row closed">
					
			<div class="heading">
				<div class="handle">
					<div class="part-icon-move"><i class="fas fa-bars"></i></div>
					<div class="part-icon-collapse"><i class="fas fa-chevron-up"></i></div>
					<div class="part-title"><?php esc_html_e($input_data['label']); ?></div>
					<div class="part-validation-message" style="display:none;"><?php esc_html_e( 'Inputs contain validation errors.', 'citadela-directory'); ?></div>
				</div>
				<div class="remove-button"><i class="fas fa-times"></i></div>
			</div>

			<div class="content">
				<div class="setting input-type">
					
					<div class="label-part">
						<label for="<?php echo esc_attr( "{$attrName}[inputs][{$key}][type]" ); ?>"><?php esc_html_e( 'Input type','citadela-directory' ); ?></label>
					</div>
					
					<div class="settings-part">
						<select 
							class="repeater-input type-input" 
							name="<?php echo esc_attr( "{$attrName}[inputs][{$key}][type]" ); ?>"
							id="<?php echo esc_attr( "{$attrName}[inputs][{$key}][type]" ); ?>"
							data-id-schema="<?php echo esc_attr( "{$attrName}[inputs][{citadela_input_key}][type]" ); ?>">
							<?php
								foreach ( self::available_input_types() as $type => $label ) {
									?>
									<option value="<?php echo esc_attr($type); ?>" <?php if( $input_data['type'] === $type ) echo esc_attr( 'selected' ); ?>><?php esc_html_e( $label); ?></option>
									<?php
								}
							?>
						</select>
						<p class="description"><?php esc_html_e( 'Select type of input field.', 'citadela-directory' ); ?></p>
					</div>

				</div>

				<div class="setting input-label">
					<div class="label-part">
						<label for="<?php echo esc_attr( "{$attrName}[inputs][{$key}][label]" ); ?>"><?php esc_html_e( 'Input label','citadela-directory' ); ?></label>
					</div>
					<div class="settings-part">
						<input 
							type="text" 
							class="repeater-input label-input pair-name-input" 
							name="<?php echo esc_attr( "{$attrName}[inputs][{$key}][label]" ); ?>" 
							id="<?php echo esc_attr( "{$attrName}[inputs][{$key}][label]" ); ?>"
							data-id-schema="<?php echo esc_attr( "{$attrName}[inputs][{citadela_input_key}][label]" ); ?>" 
							value="<?php esc_html_e($input_data['label']); ?>" 
							required
						/>
						<p class="description"><?php esc_html_e( 'Input field name that appear on edit page.', 'citadela-directory' ); ?></p>
					</div>
				</div>

				<div class="setting input-description">
					<div class="label-part">
						<label for="<?php echo esc_attr( "{$attrName}[inputs][{$key}][description]" ); ?>"><?php esc_html_e( 'Input description','citadela-directory' ); ?></label>
					</div>

					<div class="settings-part">
						<input 
							type="text" 
							class="repeater-input description-input" 
							name="<?php echo esc_attr( "{$attrName}[inputs][{$key}][description]" ); ?>" 
							id="<?php echo esc_attr( "{$attrName}[inputs][{$key}][description]" ); ?>" 
							data-id-schema="<?php echo esc_attr( "{$attrName}[inputs][{citadela_input_key}][description]" ); ?>" 
							value="<?php esc_html_e($input_data['description']); ?>" 
						/>
						<p class="description"><?php esc_html_e( 'Description text that appear on edit page with new input.', 'citadela-directory' ); ?></p>
					</div>
				</div>
				
				<div class="setting input-choices" <?php if( ! in_array( $input_data['type'], [ 'select', 'citadela_multiselect' ] ) ) echo 'style="display:none;"'; ?>>
					<div class="label-part">
						<label for="<?php echo esc_attr( "{$attrName}[inputs][{$key}][choices]" ); ?>"><?php esc_html_e( 'Input choices','citadela-directory' ); ?></label>
					</div>

					<div class="settings-part">

						
						<div class="inner-settings-wrapper choices-label" <?php if( $input_data['type'] != "select" ) echo 'style="display:none;"'; ?>>
							<input 
								type="text" 
								class="<?php //repeater-input ?> choices_label-input" 
								name="<?php echo esc_attr( "{$attrName}[inputs][{$key}][choices_label]" ); ?>" 
								id="<?php echo esc_attr( "{$attrName}[inputs][{$key}][choices_label]" ); ?>"
								data-id-schema="<?php echo esc_attr( "{$attrName}[inputs][{citadela_input_key}][choices_label]" ); ?>" 
								value="<?php isset( $input_data['choices_label'] ) && $input_data['choices_label'] ? esc_html_e( $input_data['choices_label'] ) : esc_html_e( 'Choose option', 'citadela-directory' ); ?>" 
							/>
							<p class="description"><?php esc_html_e( 'Default selection text, displayed as first option in selection. ', 'citadela-directory' ); ?></p>
						</div>

						<div class="inner-settings-wrapper">
							<textarea
								type="text" 
								class="repeater-input choices-input" 
								name="<?php echo esc_attr( "{$attrName}[inputs][{$key}][choices]" ); ?>" 
								id="<?php echo esc_attr( "{$attrName}[inputs][{$key}][choices]" ); ?>" 
								data-id-schema="<?php echo esc_attr( "{$attrName}[inputs][{citadela_input_key}][choices]" ); ?>" 
								rows="5"
							><?php echo esc_textarea( isset( $input_data['choices'] ) ? self::get_input_choices_text_from_array( $input_data['choices'] ) : '' );?></textarea>
							
							<p class="description"><?php 
								$input_examples = [
									esc_html__( "red : Red color", "citadela-directory" ),
									esc_html__( "blue : Blue color", "citadela-directory" ),
								];
								_e( 'Choices for selected input type. Insert one choise per line, divide saved input value and text displayed in input using colon.', 'citadela-directory' );
								echo "<br>";
								/* translators: %s: printed example of expected values in input. */
								wp_kses_post( printf( "<strong>" . __( 'Example of inserted values: %s', 'citadela-directory' ) . "</strong>", "<br>". implode( '<br>', $input_examples ) ) );
							?></p>
						</div>
					</div>
				</div>

				<div class="settings-group url-settings" <?php  if( $input_data['type'] !== 'citadela_url' ) echo 'style="display:none;"'; ?>>
					<div class="setting input-use-url-label">
						<div class="label-part">
							<label for="<?php echo esc_attr( "{$attrName}[inputs][{$key}][use_url_label]" ); ?>"><?php esc_html_e( 'Show link label input','citadela-directory' ); ?></label>
						</div>

						<div class="settings-part">
							<input 
								type="checkbox" 
								class="repeater-input use-url-label-input" 
								name="<?php echo esc_attr( "{$attrName}[inputs][{$key}][use_url_label]" ); ?>" 
								id="<?php echo esc_attr( "{$attrName}[inputs][{$key}][use_url_label]" ); ?>" 
								data-id-schema="<?php echo esc_attr( "{$attrName}[inputs][{citadela_input_key}][use_url_label]" ); ?>" 
								<?php echo esc_attr( $input_data['use_url_label'] ? 'checked' : '' ); ?>
							/>
							<p class="description"><?php 
							_e( 'With url will be available additional input to insert text displayed instead of plain link.', 'citadela-directory' );
							?></p>
						</div>
					</div>
				</div>

				<div class="settings-group number-settings" <?php  if( $input_data['type'] !== 'citadela_number' ) echo 'style="display:none;"'; ?>>
					<div class="setting input-unit">
						<div class="label-part">
							<label for="<?php echo esc_attr( "{$attrName}[inputs][{$key}][unit]" ); ?>"><?php esc_html_e( 'Unit of number','citadela-directory' ); ?></label>
						</div>

						<div class="settings-part">
							<input 
								type="text" 
								class="repeater-input unit-input" 
								name="<?php echo esc_attr( "{$attrName}[inputs][{$key}][unit]" ); ?>" 
								id="<?php echo esc_attr( "{$attrName}[inputs][{$key}][unit]" ); ?>" 
								data-id-schema="<?php echo esc_attr( "{$attrName}[inputs][{citadela_input_key}][unit]" ); ?>" 
								value="<?php esc_html_e($input_data['unit']); ?>" 
							/>
							<p class="description"><?php 
							_e( 'Insert unit that will be displayed with number value.', 'citadela-directory' );
							echo '&nbsp;';
							_e( 'Leave empty to not use this option.', 'citadela-directory' ); 
							?></p>
						</div>
					</div>

					<div class="setting input-unit-position">
						<div class="label-part">
							<label><?php esc_html_e( 'Unit position','citadela-directory' ); ?></label>
						</div>

						<div class="settings-part">
							<select 
								class="repeater-input unit-position-input" 
								name="<?php echo esc_attr( "{$attrName}[inputs][{$key}][unit-position]" ); ?>"
								id="<?php echo esc_attr( "{$attrName}[inputs][{$key}][unit-position]" ); ?>"
								data-id-schema="<?php echo esc_attr( "{$attrName}[inputs][{citadela_input_key}][unit-position]" ); ?>"
							>
								<option value="left" <?php echo esc_attr( $input_data['unit-position'] === 'left' ? 'selected' : '' ); ?>><?php esc_html_e( 'Left', 'citadela-directory' ) ?></option>
								<option value="right" <?php echo esc_attr( $input_data['unit-position'] === 'right' ? 'selected' : '' ); ?>><?php esc_html_e( 'Right', 'citadela-directory' ) ?></option>
							</select>
							
							<p class="description"><?php 
							_e( 'Select position of unit displayed with number value.', 'citadela-directory' );
							?></p>
						</div>
					</div>


				</div>
				
				<div class="setting input-key">
					<div class="label-part">
						<label for="<?php echo esc_attr( "{$attrName}[inputs][{$key}][input_key]" ); ?>"><?php esc_html_e( 'Input identifier','citadela-directory' ); ?></label>
					</div>

					<div class="settings-part">
						<input 
							type="text" 
							class="repeater-input key-input pair-key-input" 
							name="<?php echo esc_attr( "{$attrName}[inputs][{$key}][input_key]" ); ?>" 
							id="<?php echo esc_attr( "{$attrName}[inputs][{$key}][input_key]" ); ?>" 
							data-id-schema="<?php echo esc_attr( "{$attrName}[inputs][{citadela_input_key}][input_key]" ); ?>" 
							value="<?php esc_html_e( $is_template ? '' : $key ); ?>" 
							required
						/>
						<p class="description"><?php esc_html_e( 'Unique identifier for new input field. Use single word, no spaces. Underscores and dashes allowed.', 'citadela-directory' ); ?></p>
					</div>
				</div>

				<div class="settings-group filter-settings" <?php  if( ! self::is_advanced_filter_input( $input_data['type'] ) ) echo 'style="display:none;"'; ?>>
					<div class="setting input-filter">
						<div class="label-part">
							<label for="<?php echo "{$attrName}[inputs][{$key}][use_as_filter]"; ?>"><?php _e( 'Use in search filter','citadela-directory' ); ?></label>
						</div>

						<div class="settings-part">
							<input 
								type="checkbox" 
								class="repeater-input use-as-filter-input" 
								name="<?php echo esc_attr( "{$attrName}[inputs][{$key}][use_as_filter]" ); ?>" 
								id="<?php echo esc_attr( "{$attrName}[inputs][{$key}][use_as_filter]" ); ?>" 
								data-id-schema="<?php echo esc_attr( "{$attrName}[inputs][{citadela_input_key}][use_as_filter]" ); ?>" 
								<?php echo isset( $input_data['use_as_filter'] ) && $input_data['use_as_filter'] ? 'checked' : ''; ?>
							/>
							<p class="description"><?php _e( 'If selected, will be available in filters for Item posts.', 'citadela-directory' ); ?></p>
						</div>
					</div>

					<div class="setting input-filters-group-name" <?php  if( $input_data['type'] != 'checkbox' || ( $input_data['type'] == 'checkbox' && isset( $input_data['use_as_filter'] ) && ! $input_data['use_as_filter'] )  ) echo 'style="display:none;"'; ?>>
						<div class="label-part">
							<label for="<?php echo "{$attrName}[inputs][{$key}][checkbox_filters_group_name]"; ?>"><?php _e( 'Filters group name','citadela-directory' ); ?></label>
						</div>

						<div class="settings-part">
							<input 
								type="text" 
								class="repeater-input filters-group-name-input" 
								name="<?php echo esc_attr( "{$attrName}[inputs][{$key}][checkbox_filters_group_name]" ); ?>" 
								id="<?php echo esc_attr( "{$attrName}[inputs][{$key}][checkbox_filters_group_name]" ); ?>" 
								data-id-schema="<?php echo esc_attr( "{$attrName}[inputs][{citadela_input_key}][checkbox_filters_group_name]" ); ?>" 
								value="<?php isset( $input_data['checkbox_filters_group_name'] ) && $input_data['checkbox_filters_group_name'] ? esc_html_e( $input_data['checkbox_filters_group_name'] ) : esc_html_e( 'Filters', 'citadela-directory' ); ?>"
								<?php  if( $input_data['type'] == 'checkbox'  ) echo 'required'; ?>
							/>
							<p class="description"><?php _e( 'Merge checkboxes into filter group and show them together in frontend filters. You can use more groups using different group names.', 'citadela-directory' ); ?></p>
						</div>
					</div>
				</div>

				<div class="setting duplicate-row"><input type="button" class="repeater-duplicate-button button-secondary" value="<?php echo esc_attr('Duplicate row', 'citadela-directory' ); ?>"></div>
			</div>
		</div>
		<?php
	}


	public static function available_input_types(){
		return [
			'text' => esc_html__( 'Text', 'citadela-directory' ),
			'textarea' => esc_html__( 'Textarea', 'citadela-directory' ),
			'email' => esc_html__( 'Email', 'citadela-directory' ),
			'citadela_url' => esc_html__( 'Url', 'citadela-directory' ),
			'citadela_number' => esc_html__( 'Number', 'citadela-directory' ),
			'date' => esc_html__( 'Date', 'citadela-directory' ),
			'checkbox' => esc_html__( 'Checkbox', 'citadela-directory' ),
			'select' => esc_html__( 'Select', 'citadela-directory' ),
			'citadela_multiselect' => esc_html__( 'Multiselect', 'citadela-directory' ),
		];
	}

	public static function get_input_type_sanitize_callback( $input_type ){
		$callbacks = [
			'textarea' => 'wp_kses_post',
			'checkbox' => 'butterbean_validate_boolean',
		];

		return isset( $callbacks[ $input_type ] ) ? $callbacks[ $input_type ] : 'wp_filter_nohtml_kses';
	}

	public static function get_input_type_classes( $input_type ){
		$classes = [
			'text' => 'widefat',
			'textarea' => 'widefat',
			'email' => 'widefat',
			'url' => 'widefat',
		];

		return isset( $classes[ $input_type ] ) ? $classes[ $input_type ] : '';
	}

	public static function get_input_choices_text_from_array( $choices = [] ){
		if( ! is_array($choices ) ||  empty( $choices ) ) return '';

		$lines = '';
		foreach ($choices as $key => $value) {
			if( $key != "" ) $lines .= "{$key} : {$value}\n";
		}

		return $lines;
	}

	public static function get_input_choices_array_from_text( $choices = '' ){
		if( ! $choices ) return [];

		$lines = explode("\n", $choices );
		$choices_array = [];
		foreach ($lines as $line) {
			$line_data = explode(':', $line);
			// there is no correct structure for choice "codename : value"
			if( count( $line_data ) < 2 ) continue;
			$key = trim( rtrim( $line_data[0] ) );
			//if key is empty, ignore this line of choice
			if( $key == "" ) continue;
			$key = str_replace(' ', '_', $key);
			// implode the rest of value data in case the text value include colon, like "codename : value: 1"...
			$text = trim( rtrim( implode(":", array_slice( $line_data, 1 )) ) );
			//if text is empty, ignore this line of choice
			if( $text == "" ) continue;

			$choices_array[$key] = $text;
		}
		return $choices_array;
	}

	public static function settings(){
		return [];
	}

	public static function is_advanced_filter_input( $input_type ){
		return in_array( $input_type, [ 'checkbox', 'select', 'citadela_multiselect' ] );
	}

	public static function use_choice_input( $input_type ){
		return in_array( $input_type, [ 'select', 'citadela_multiselect' ] );
	}

	public static function is_citadela_number_input( $input_type ){
		return $input_type === 'citadela_number';
	}

	public static function is_citadela_url_input( $input_type ){
		return $input_type === 'citadela_url';
	}

	public static function sanitize( $data ){
		$settings = [];

		$settings['enable'] = isset( $data['enable'] ) && $data['enable'] !== false ? true : false;

		$settings['inputs_group']['group_name'] = isset( $data['inputs_group']['group_name'] ) ? $data['inputs_group']['group_name'] : '';

		//validate inputs data, make sure that data are stored under current key name inserted by user, removes data related to old keys.
		$inputs = [];
		if( isset( $data['inputs_group'] ) && isset( $data['inputs_group']['inputs'] ) && ! empty( $data['inputs_group']['inputs'] ) ){

			foreach ($data['inputs_group']['inputs'] as $key => $input_data) {

				// check if we would really save data for this input, type and label must be defined, input key will be checked later
				if( 
					isset( $input_data['type'] ) && $input_data['type'] 
					&& isset( $input_data['label'] ) && $input_data['label'] 		
				) {
				

					//make sure we save input data under new key defined by user
					$new_key = ( isset( $input_data['input_key'] ) && $input_data['input_key'] !== '' ) ? $input_data['input_key'] : $key;

					$inputs[$new_key] = [
						'type' => $input_data['type'],
						'label' => $input_data['label'],
						'description' => isset( $input_data['description'] ) ? $input_data['description'] : '',
						'unit' => self::is_citadela_number_input( $input_data['type'] ) && isset( $input_data['unit'] ) ? $input_data['unit'] : '',
						'unit-position' => self::is_citadela_number_input( $input_data['type'] ) && isset( $input_data['unit-position'] ) ? $input_data['unit-position'] : 'right',
						'use_url_label' => self::is_citadela_url_input( $input_data['type'] ) && isset( $input_data['use_url_label'] ) && $input_data['use_url_label'] !== false ? true : false,
						'use_as_filter' => self::is_advanced_filter_input( $input_data['type'] ) && isset( $input_data['use_as_filter'] ) && $input_data['use_as_filter'] !== false ? true : false,
						'checkbox_filters_group_name' => 
							self::is_advanced_filter_input( $input_data['type'] ) && $input_data['type'] == 'checkbox' && isset( $input_data['checkbox_filters_group_name'] ) 
							? $input_data['checkbox_filters_group_name'] != "" ? $input_data['checkbox_filters_group_name'] : esc_html__( "Filters", "citadela-directory" )
							: '',
					];
					if( self::use_choice_input( $input_data['type'] ) && isset( $input_data['choices'] ) ){
						// sanitize choices if data are array or plain text from textarea, if array, validate for empty values
						$inputs[$new_key]['choices'] = [];
						if( is_array( $input_data['choices'] ) ){
							//choice is array
							foreach ( $input_data['choices'] as $choice_key => $choice_value ) {
								if( $choice_key != "" && $choice_value != "" ) $inputs[$new_key]['choices'][$choice_key] = $choice_value;
							}
						}else{
							//choice is string from textarea
							$inputs[$new_key]['choices'] = self::get_input_choices_array_from_text( preg_replace( "/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", $input_data['choices'] ) );
						}
						
						// choices label for select input 
						$inputs[$new_key]['choices_label'] = $input_data['type'] === 'select' && isset( $input_data['choices_label'] ) ? $input_data['choices_label'] : esc_html__( "Choose option", "citadela-directory" );

					}

				}
			}
		}
		$settings['inputs_group']['inputs'] = $inputs;


		return $settings;
	}

}
