<?php 

// ===============================================
// Citadela URL Control functions
// -----------------------------------------------

?>

<div class="citadela-control">
	<div {{{ data.attr }}}>
		<div class="option-field">
				<div class="input-wrapper">
					<div class="inner-input">
						<# if ( data.label ) { #>
						<label for="{{ data.field_name }}">
							<span class="butterbean-label">{{ data.label }}</span>
						</label>
						<# } #>


						<textarea id="{{ data.field_name }}" {{{ data.attr }}} rows="5">{{{ data.value }}}</textarea>

						<# if ( data.description ) { #>
							<span class="butterbean-description">{{{ data.description }}}</span>
						<# } #>
					</div>

				</div>

				

		</div>

	</div>
</div>
