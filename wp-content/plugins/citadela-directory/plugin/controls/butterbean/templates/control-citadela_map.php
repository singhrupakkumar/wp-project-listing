<?php 

// ===============================================
// Citadela Map Control functions
// -----------------------------------------------


//set defaults if necessary 
?>
<#
if(! data.latitude.value){
	data.latitude.value = data.latitude.default;
}
if(! data.longitude.value){
	data.longitude.value = data.longitude.default;
}
if(! data.latitude.value){
	data.latitude.value = data.latitude.default;
}
if(! data.swheading.value){
	data.swheading.value = data.swheading.default;
}
if(! data.swpitch.value){
	data.swpitch.value = data.swpitch.default;
}
if(! data.swzoom.value){
	data.swzoom.value = data.swzoom.default;
}
#>

<div class="citadela-control">
<div {{{ data.attr }}}>
	<div class="option-field">
		<label>
			<# if ( data.address.label ) { #>
				<span class="butterbean-label">{{ data.address.label }}</span>
			<# } #>

			<input type="text" class="address-input" name="{{ data.address.field_name }}" value="{{ data.address.value }}" />
			

			<?php //BUTTON FIND ADDRESS ?>
			<input type="button" class="button button-primary button-large search-button" value="<?php esc_html_e('Find address', 'citadela-directory')?>">

			<# if ( data.address.description ) { #>
				<span class="butterbean-description">{{{ data.address.description }}}</span>
			<# } #>
		</label>
	</div>

	<div class="option-field">
		<label>
			<# if ( data.latitude.label ) { #>
				<span class="butterbean-label">{{ data.latitude.label }}</span>
			<# } #>

			<input type="text" class="latitude-input" name="{{ data.latitude.field_name }}" value="{{ data.latitude.value }}" />

			<# if ( data.latitude.description ) { #>
				<span class="butterbean-description">{{{ data.latitude.description }}}</span>
			<# } #>
		</label>
	</div>

	<div class="option-field">
		<label>
			<# if ( data.longitude.label ) { #>
				<span class="butterbean-label">{{ data.longitude.label }}</span>
			<# } #>

			<input type="text" class="longitude-input" name="{{ data.longitude.field_name }}" value="{{ data.longitude.value }}" />

			<# if ( data.longitude.description ) { #>
				<span class="butterbean-description">{{{ data.longitude.description }}}</span>
			<# } #>
		</label>
	</div>

	<div class="option-field">
		<label>
			<input type="checkbox" class="streetview-input" name="{{ data.streetview.field_name }}" value="true" <# if ( data.streetview.value ) { #> checked="checked" <# } #> />

			<# if ( data.streetview.label ) { #>
				<span class="butterbean-label">{{ data.streetview.label }}</span>
			<# } #>

			<# if ( data.streetview.description ) { #>
				<span class="butterbean-description">{{{ data.streetview.description }}}</span>
			<# } #>
		</label>
	</div>


	<div class="hidden-fields" style="display:none;">
		<input type="hidden" class="swheading-input" name="{{ data.swheading.field_name }}" value="{{ data.swheading.value }}" />
		<input type="hidden" class="swpitch-input" name="{{ data.swpitch.field_name }}" value="{{ data.swpitch.value }}" />
		<input type="hidden" class="swzoom-input" name="{{ data.swzoom.field_name }}" value="{{ data.swzoom.value }}" />
	</div>


	<div class="citadela-google-map-message" style="display:none;">
		<?php esc_html_e("Couldn't find location, try different address.", 'citadela-directory'); ?>
	</div>
		

	<div class="citadela-google-map-holder">
		<div class="citadela-google-map google-map-container"></div>
	</div>


</div>
</div>