<?php 

// ===============================================
// Citadela Number Control functions
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

				<div class="input-wrapper <# if ( data.unit ) { #>has-unit {{data.unit_position}}-position<# } #>">
					
					<# if ( data.unit && data.unit_position == 'left'  ) { #>
						<span class="unit">{{data.unit}}</span>
					<# } #>

					<input 
						type="number" 
						step="any"
						name="{{ data.field_name }}"
						value="{{ data.value }}"
						autocomplete="off"
					/>

					<# if ( data.unit && data.unit_position == 'right'  ) { #>
						<span class="unit">{{data.unit}}</span>
					<# } #>

				</div>
				
				<# if ( data.description ) { #>
					<span class="butterbean-description">{{{ data.description }}}</span>
				<# } #>

		</div>

	</div>
</div>