<?php

// ===============================================
// Citadela Butterbean GPX upload Control functions
// -----------------------------------------------

class CitadelaButterbeanGpxUpload extends ButterBean_Control {

	/*
	*	The type of control.
	*/
	public $type = 'citadela_gpx_upload';
		    
	/*
	*	Adds custom data to the json array. Data are passed to the Underscore template.
	*/
	public function to_json() {
		parent::to_json();

		//check if GPX file still exists and was not deleted
		$post_id = get_the_ID();
		$gpxFileId = get_post_meta( $post_id, '_citadela_gpx_file_id', true );
		$file = get_attached_file( intval( $gpxFileId ) );

		$this->json['track'] = array(
			'value'      => $file ? esc_attr( $this->get_value( 'track' ) ) : '',
			'field_name' => $this->get_field_name( 'track' ),
			'class' 	 => $file ? 'load-map' : '',
		);

		$this->json['file_id'] = array(
			'value'      => $file ? esc_attr( $this->get_value( 'file_id' ) ) : '',
			'field_name' => $this->get_field_name( 'file_id' ),
		);

	}

	/*
	*	Adds custom attributes for html.
	*/
	public function get_attr() {
		$this->attr = parent::get_attr();
		$this->attr['class'] .= " {$this->type}-control";
		return $this->attr;
	}


	/*
	*	Render uploader html.
	*/
	public static function get_uploader() {
		?>
			<div class="citadela-file-uploader hidden" data-type="gpx">
				<div class="upload-and-import-root flex">
			        <div class="m-auto">     	
			            <div class="uploader drag-drop m-auto">
			                <div class="upload-card flex">
			                    <div class="m-auto w-full">
			                        <div class="uploader-selector text-center">
			                            <div><h3 class="text-gray-800"><?php esc_html_e( 'Drop GPX file here', 'citadela-directory' ) ?></h3></div>
			                            <div><?php echo esc_html_x( 'or', 'Uploader: Drop file here - or - Select file from computer', 'citadela-directory' ) ?></div>
			                            <div>
			                            	<input class="uploader-browse-button button" type="button" value="<?php esc_attr_e( 'Upload file', 'citadela-directory' ) ?>" />
			                            	<input class="uploader-media-button button" type="button" value="<?php esc_attr_e( 'Insert from media', 'citadela-directory' ) ?>" />
			                            </div>
			                            
			                        </div>
			                        <div class="uploader-progress hidden text-center">
			                            <div><h3 class="text-gray-800"><?php esc_html_e( 'Uploading&#8230;', 'citadela-directory' ) ?></h3></div>
			                            <div><progress value="42" max="100"></progress></div>
			                        </div>
			                        <div class="uploader-error hidden">
			                            <div><h3 class="uploader-error-title text-gray-800"></h3></div>
			                            <div class="uploader-error-msg text-red-700"></div>
			                            <div class="text-center"><button type="button" class="button"><?php esc_html_e( 'OK', 'citadela-directory' ) ?></button></div>
			                        </div>
			                        <div class="uploader-cancel text-center">
			                        	<a href="#" class="button button-secondary button-cancel"><?php esc_html_e( 'Cancel', 'citadela-directory' ) ?></a>
			                        </div>
			                    </div>
			                </div>
			            </div>
	
			            <div class="import-complete hidden m-auto">
			                <div class="import-card flex">
			                    <div class="text-center m-auto w-full">
			                        <h1 class="text-gray-800"><?php esc_html_e( 'GPX file successfully loaded!', 'citadela-directory' ) ?></h1>
			                        <div><a href="#" class="button button-primary button-hero button-confirm-success"><?php esc_html_e( 'View track on the map', 'citadela-directory' ) ?></a></div>
			                    </div>
			                </div>
			            </div>

			        </div>
    			</div>
			</div>
		<?php
	}

}