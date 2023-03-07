<?php

namespace Citadela\Pro;


trait Settings_Api {

	function register( $settings, $args = [] ) {
		if( ! isset( $args['sanitize_callback'] ) and is_admin() ) {
			$args['sanitize_callback'] = [ $this, 'sanitize' ];
		}

		// defaults when settings are not yet saved in DB
		$args['default'] = $settings;
		register_setting( $this->settings_slug(), $this->settings_slug(), $args );

		// to ensure that get_option($this->settings_slug()) will allways return value - defaults merged with saved in db
		add_filter( "option_{$this->settings_slug()}", function( $value ) use ($settings) {
			return array_replace_recursive( $settings, (array) $value );
		} );

		return $this;
	}



	function defaults( $key = null ) {
		return \ctdl\pro\dot_get( get_registered_settings()[ $this->settings_slug() ]['default'], $key );
	}



	function settings_slug() {
		return 'citadela_pro_' . str_replace( '-', '_', $this->slug() );
	}



	function add_section( $section, $args = []) {
		$args = wp_parse_args( $args, [
			'title' => '',
			'description' => '',
			'callback' => null,
		] );

		$callback = $args['callback']
			// just wrapper fn that we can call our custom section callback like this: function( $description ) { echo $description; }
			? \Closure::bind( function() use( $args ) { call_user_func( $args['callback'], $args['description'] ); }, $this )
			// generic section callback Form#section( $description );
			: \Closure::bind( function() use( $args ) { $this->section( $args['description'] ); }, $this );

		add_settings_section( $section, $args['title'], $callback, $this->settings_slug() );
	}



	function add_field( $field, $args ) {
		$args = array_replace_recursive( [
			'title'    => '',
			'callback' => null,
			'section'  => 'default',
			'args' => [
				'class'       => "citadela-control type-{$args['args']['type']}", // wp
				'label_for'   => '', // wp
				// our custom
				'type'        => 'text',
				'description' => '',
				'attrs'       => [
					'class' => '',
				],
			],
		], $args );

		global $wp_settings_sections;
		if ( $args['section'] === 'default' and empty( $wp_settings_sections[ $this->settings_slug() ]['default'] ) ) {
			$this->add_section( 'default' );
		}

		if ( ! in_array( $args['args']['type'], [ 'checkbox', 'radio-list' ] ) ) {
			$args['args']['label_for'] = $this->id_attr( $field );
		}

		if( is_callable( $args['callback'] ) ) {
			// just wrapper fn that we can call our custom field callback like this: function( $field, $args ) { $this->input( $field ); echo $args->description; }
			unset( $args['args']['type'] );
			$callback = \Closure::bind( function() use( $field, $args ) { call_user_func( $args['callback'], $field, (object) $args['args'] ); }, $this );
		}else{
			// generic built callback, when fields are output according to their type
			$callback = $this->field_callback( $field, $args['args']['type'], (object) $args['args'] );
		}

		add_settings_field( $field, $args['title'], $callback, $this->settings_slug(), $args['section'], $args['args'] );
	}



	protected function field_callback( $field, $type, $args ) {
		return function () use ( $field, $type, $args ) {

			$args->attrs['class'] .= " field-type-{$type}";

			switch( $type ) {
				case 'text':
					$this->input( $field, $args->attrs );
					break;
				case 'number':
					$this->number( $field, $args->attrs );
					break;
				case 'url':
					$this->url( $field, $args->attrs );
					break;
				case 'checkbox':
					$this->checkbox( $field, $args->label, $args->attrs );
					break;
				case 'radio-list':
					$this->radio_list( $field, $args->list, $args->attrs );
					break;
				case 'datetime':
					$this->datetime( $field, ! empty( $args->picker ) ? $args->picker : [], $args->attrs );
					break;
				case 'code-editor':
					$this->code_editor( $field, $args->mode, $args->attrs );
					break;
				case 'textarea':
					$this->textarea( $field, $args->attrs );
					break;
				case 'select':
					$this->select( $field, $args->options, $args->attrs );
					break;
			}

			$this->description( $args->description );
		};
	}



	function add_fields() {
	}



	function fields() {
		global $wp_settings_fields;

		$this->add_fields();

		$return = [];

		if ( ! isset( $wp_settings_fields[ $this->settings_slug() ] ) ) return $return;

		foreach( $wp_settings_fields[ $this->settings_slug() ] as $section => $fields) {
			$return = array_replace_recursive($return, $fields);
		}

		return $return;
	}



	function field_args( $field, $arg = '' ) {
		return \ctdl\pro\dot_get( $this->fields(), "$field.args.$arg" );
	}



	function sanitize( $values ) {
		return $values;
	}



	function section( $description, $after_description_html = '' ) {
		echo '<div class="section-description"><p>' . esc_html( $description ) . '</p>' . wp_kses_post( $after_description_html ) . '</div>';
	}



	function id_attr( $field ) {
		return esc_attr( 'field-' . str_replace( '_', '-', $field ) );
	}



	function name_attr( $field ) {
		return esc_attr( "{$this->settings_slug()}[{$field}]" );
	}



	function label( $field, $label ) {
		printf( '<label for="%s">%s</label>', $this->id_attr( $field ), $label );
	}



	function description( $description ) {
		if ( $description ): ?>
			<p class="description">
				<?php echo esc_html( $description ); ?>
			</p>
		<?php
		endif;
	}



	function input( $field, $attrs = [] ) {
		$attrs = array_merge( [
			'id'   => $this->id_attr( $field ),
			'name' => $this->name_attr( $field ),
			'type' => 'text',
			'value' => $this->value( $field )
		], $attrs );
		?>
		<input <?php echo $this->attrs($attrs) ?>>
		<?php
	}



	function checkbox( $field, $label, $attrs = [] ) {
		$attrs['type'] = 'checkbox';
		$attrs['value'] = '1';
		if ( $this->value( $field ) ) $attrs['checked'] = 'checked';
		echo '<label>', $this->input( $field, $attrs ), $label, '</label>';
	}



	function radio_list( $field, $list, $attrs = [] ) {
		foreach( $list as $value => $label ){
			$attrs['type'] = 'radio';
			$attrs['value'] = $value;
			$attrs['id'] =  $this->id_attr( $field ) . "-$value";
			if ( $this->value( $field ) === $value ){
				$attrs['checked'] = 'checked';
			}else{
				unset($attrs['checked']);
			}
			echo '<p><label>', $this->input( $field, $attrs ), $label, '</label></p>';
		}
	}



	function number( $field, $attrs = [] ) {
		$attrs['type'] = 'number';
		$this->input( $field, $attrs );
	}



	function url( $field, $attrs = [] ) {
		$attrs['type'] = 'url';
		$this->input( $field, $attrs );
	}



	function select( $field, $options, $attrs = [] ) {
		$attrs = array_merge( [
			'id'   => $this->id_attr( $field ),
			'name' => $this->name_attr( $field ),
		], $attrs );
		?>
		<select <?php echo $this->attrs($attrs) ?>>
			<?php foreach( $options as $value => $label ): ?>
				<option
					value="<?php echo esc_attr( $value ) ?>"
					<?php selected( $this->value( $field ), $value ) ?>
				>
					<?php echo esc_html( $label ) ?>
				</option>
			<?php endforeach ?>
		</select>
		<?php
	}



	function datetime( $field, $config = [], $attrs = [] ) {
		$attrs = array_merge( [
			'data-ctdl-pro-field-config' => json_encode( array_merge( [
				'hasDate' => true,
				'hasTime' => true,
			],
			$config,
			[
				'startOfWeek' => get_option('start_of_week'),
				'locale'      => substr( get_user_locale(), 0, 2 ),
				'timeAs24hr'  => ( strpos( strtolower( get_option( 'time_format') ), 'a' ) === false ),
			] ) ),
		], $attrs );
		$this->input( $field, $attrs );
		printf( '<span class="%s-clear dashicons dashicons-no-alt" style="vertical-align:inherit;cursor:pointer;"></span>', $attrs['class'] );
	}



	function code_editor( $field, $mode, $attrs ) {
		$attrs = array_merge( [
			'data-ctdl-pro-field-config' => json_encode( [
				'mode' => $mode,
			] ),
		], $attrs );
		$this->textarea( $field, $attrs );
	}



	function textarea( $field, $attrs = [] ) {
		$attrs = array_merge( [
			'id'   => $this->id_attr( $field ),
			'name' => $this->name_attr( $field ),
		], $attrs );
		?>
		<textarea <?php echo $this->attrs($attrs) ?>><?php echo esc_textarea( $this->value( $field ) ) ?></textarea>
		<?php
	}



	function submit_button() {
		submit_button( __( 'Save Settings', 'citadela-pro' ) );
	}



	function attrs( $attrs ) {
		return array_reduce( array_keys( $attrs ), function( $carry, $key ) use ( $attrs ) {
				return $carry . ' ' . $key . '="' . esc_attr( $attrs[ $key ] ) . '"';
			}, ''
		);
	}



	function value( $field ) {
		return \ctdl\pro\dot_get( get_option( $this->settings_slug() ), $field );
	}



	function values() {
		return get_option( $this->settings_slug() );
	}



	function section_begin( $section_id ) {
		echo '<div ' . $this->attrs( [
				'id' => $id = ( 'citadela-section-' . str_replace( '_', '-', $section_id ) ),
				'class' => "citadela-section $id",
			] ) .
			'>';
	}



	function section_end() {
		?></div><?php
	}



	function form_begin() {
		?><form action="options.php" method="post"><?php
	}



	function form_end() {
		?></form><?php
	}



	function settings_begin() {
		?><div class="citadela-settings <?php echo esc_attr( 'tab-' . str_replace( '-', '_', $this->slug() ) ); ?>"><?php
	}



	function settings_end() {
		?></div><?php
	}


	function do_sections( $page ) {
		global $wp_settings_sections, $wp_settings_fields;

		if ( ! isset( $wp_settings_sections[ $page ] ) ) {
			return;
		}

		foreach ( (array) $wp_settings_sections[ $page ] as $section ) {
			$this->section_begin( $section['id'] );
			if ( $section['title'] ) {
				echo "<h2 class=\"section-title\">{$section['title']}</h2>\n";
			}

			if ( $section['callback'] ) {
				call_user_func( $section['callback'], $section );
			}

			if ( ! isset( $wp_settings_fields ) || ! isset( $wp_settings_fields[ $page ] ) || ! isset( $wp_settings_fields[ $page ][ $section['id'] ] ) ) {
				$this->section_end();
				continue;
			}
			echo '<table class="form-table" role="presentation">';
			do_settings_fields( $page, $section['id'] );
			echo '</table>';
			$this->section_end();
		}
	}



	function do_form() {
		$this->settings_begin();
		
		$this->add_fields();

		$this->form_begin();

		settings_fields( $this->settings_slug() );
		$this->do_sections( $this->settings_slug() );

		$this->submit_button();

		$this->form_end();

		$this->settings_end();
	}

}
