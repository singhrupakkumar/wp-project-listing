<?php 

// ===============================================
// Citadela Map Control functions
// -----------------------------------------------

?>

<div class="citadela-control">
<div class="gpx-upload-control">
	<div {{{ data.attr }}}>
<#
	const track_data = data.track.value ? JSON.parse( _.unescape( data.track.value ) ) : [];
	const endpoints_type = track_data[0] && track_data[0].endpoints_type ? track_data[0].endpoints_type : 'track';
#>
		<div class="citadela-holder">
			
			<input type="hidden" class="gpx-track-input" name="{{{ data.track.field_name }}}" value="{{{ data.track.value }}}">
			<input type="hidden" class="gpx-file-id-input" name="{{{ data.file_id.field_name }}}" value="{{{ data.file_id.value }}}">
			
			<div class="gpx-map-view {{{ data.track.class }}}" data-map-provider="openstreetmap">
				<div class="map-container"></div>
				<div class="action-buttons">
					<a href="#" class="button button-primary button-change-gpx-track">
						<?php esc_html_e( 'Change GPX track', 'citadela-directory' ) ?>
					</a>
					<a href="#" class="button button-secondary button-remove-gpx-track">
						<?php esc_html_e( 'Remove GPX track', 'citadela-directory' ) ?>
					</a>
				</div>
				<div class="settings">
					<div class="butterbean-control butterbean-control-select control-gpx-endpoints-type">
						<label><span class="butterbean-label"><?php esc_html_e( 'Show track endpoint markers on', 'citadela-directory' ) ?></span></label>
							<div class="input-wrapper">
								<select class="gpx-endpoints-type">
									<option value="track" <# if ( endpoints_type === 'track' ) { #> selected="selected" <# } #>><?php esc_html_e( 'Start and end of track', 'citadela-directory' ) ?></option>
									<option value="segments" <# if ( endpoints_type === 'segments' ) { #> selected="selected" <# } #>><?php esc_html_e( 'Every segment in track', 'citadela-directory' ) ?></option>
									<option value="none" <# if ( endpoints_type === 'none' ) { #> selected="selected" <# } #>><?php esc_html_e( 'Do not show endpoints', 'citadela-directory' ) ?></option>
								</select>
							</div>
						<span class="butterbean-description">
								<span class="gpx-endpoints-type-description track-description <# if ( endpoints_type === 'track' ) { #>selected<# } #>" <# if ( endpoints_type !== 'track' ) { #>style="display:none;"<# } #>><?php esc_html_e( 'Endpoint markers are displayed in place of very first and very end point of uploaded GPX track.', 'citadela-directory' ) ?></span>
								<span class="gpx-endpoints-type-description segments-description <# if ( endpoints_type === 'segments' ) { #>selected<# } #>" <# if ( endpoints_type !== 'segments' ) { #>style="display:none;"<# } #>><?php esc_html_e( 'If your GPX track consists of several segments, endpoint markers are displayed in place of start and end of every segment.', 'citadela-directory' ) ?></span>
						</span>
					</div>
				</div>
			</div>
			<?php CitadelaButterbeanGpxUpload::get_uploader(); ?>
			
		</div>


	</div>
</div>
</div>