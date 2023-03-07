<?php 

// ===============================================
// Citadela Select Control functions
// -----------------------------------------------

?>
<div class="citadela-control">
	<div {{{ data.attr }}}>
		<div class="option-field">

				<# if ( data.label ) { #>
				<label>
					<span class="butterbean-label">{{ data.label }}</span>
				</label>
				<# } #>

				<div class="input-wrapper">
					
					<select {{{ data.attr }}}>
						
						<option value="" <# if ( data.value == "" ) { #> selected="selected" <# } #>>{{ data.citadela_settings['choices_label'] }}</option>
						
						<# _.each( data.choices, function( label, choice ) { #>
							<option value="{{ choice }}" <# if ( data.value === choice ) { #> selected="selected" <# } #>>{{ label }}</option>
						<# } ) #>

					</select>

				</div>
				
				<# if ( data.description ) { #>
					<span class="butterbean-description">{{{ data.description }}}</span>
				<# } #>

		</div>

	</div>
</div>