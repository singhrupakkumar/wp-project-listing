<?php 

// ===============================================
// Citadela Multiselect Control functions
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
					<# /* if default value is available, there are no added any choises to select */ #>
					<# if( ! data.multiselect.default ) { 
						for (const [key, values] of Object.entries(data.multiselect)) { #>
							<label class="inner-label">
							<input 
								type="checkbox" 
								name="{{ values.name }}"
								value="true"
								<# if ( values.value ) { #>checked="checked"<# } #>
							/>{{ data.choices[key] }}</label>
						
						<# } 
					} #>

				</div>
				
				<# if ( data.description ) { #>
					<span class="butterbean-description">{{{ data.description }}}</span>
				<# } #>

		</div>

	</div>
</div>