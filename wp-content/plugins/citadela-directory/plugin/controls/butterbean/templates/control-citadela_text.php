<?php 

// ===============================================
// Citadela Text Control functions
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

						<# if ( data.description ) { #>
							<span class="butterbean-description">{{{ data.description }}}</span>
						<# } #>

						<input 
							type="text" 
							id="{{ data.field_name }}"
							value="{{ data.value }}"
							{{{ data.attr }}}
						/>

					</div>

				</div>

				

		</div>

	</div>
</div>
