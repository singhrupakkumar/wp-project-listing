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
						<label for="{{ data.url.field_name }}">
							<span class="butterbean-label">{{ data.url.label }}</span>
						</label>
						<# } #>

						<# if ( data.description ) { #>
							<span class="butterbean-description">{{{ data.description }}}</span>
						<# } #>

						<input 
							type="url" 
							class="widefat"
							id="{{ data.url.field_name }}"
							name="{{ data.url.field_name }}"
							value="{{ data.url.value }}"
						/>
					</div>

					<# if ( data.citadela_settings.use_url_label ) { #>
						<div class="inner-input">
							<label for="{{ data.url_label.field_name }}">
								<span class="butterbean-label">{{ data.url_label.label }}</span>
							</label>

							<span class="butterbean-description">{{{ data.url_label.description }}}</span>

							<input 
								type="text"
								class="widefat" 
								id="{{ data.url_label.field_name }}"
								name="{{ data.url_label.field_name }}"
								value="{{ data.url_label.value }}"
							/>
						</div>
					<# } #>
				</div>

				

		</div>

	</div>
</div>