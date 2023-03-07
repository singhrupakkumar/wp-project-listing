<?php 

// ===============================================
// Citadela Item Category Select Control functions
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

				<# if ( data.description ) { #>
					<span class="butterbean-description">{{{ data.description }}}</span>
				<# } #>
				
				<div class="input-wrapper">
					
					<input 
						type="hidden" 
						class="selected-term"
						name="{{ data.field_name }}"
						value="{{ data.value }}"
						aria-label="hidden"
					/>
				
				<# if( data.citadela_settings.terms ) { #> 
						<# for (const [key, values] of Object.entries(data.citadela_settings.terms)) { #>
							<div 
								class="term-wrapper term-{{ values.term_id }} <# if( data.value == values.term_id ) { #>selected<#  } #>" 
								data-term_id="{{ values.term_id }}"
								>
								<span class="icon"><i class="{{ values.term_meta.category_icon }}" style="color:{{ values.term_meta.category_color }};"></i></span>
								<span class="title">{{ values.term_name }}</span>
							</div>
						
						<# } #>

				<# } #>
				<p class="no-posts-notification" style="display:none;"><?php esc_html_e('Post has not selected any term.', 'citadela-directory') ?></p>

				</div>
				

		</div>

		<script type="text/html" class="featured-category-select-control-template">
			<div class="term-wrapper term-{term_id} {selected_class}" data-term_id="{term_id}">
				<span class="icon"><i class="{category_icon}" style="color:{category_color};"></i></span>
				<span class="title">{term_name}</span>
			</div>
		</script>

	</div>

</div>