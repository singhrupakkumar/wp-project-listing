<?php

// ===============================================
// Assets for Citadela Butterbean Gpx Upload control
// -----------------------------------------------

class CitadelaButterbeanGpxAssets {

	public static $plugin;

    public static function init(){
		self::$plugin = CitadelaDirectory::getInstance();
		add_action( 'admin_enqueue_scripts', [ __CLASS__, 'admin_enqueue_scripts' ] );
      	add_action( "admin_action_citadela_gpx_upload", [ __CLASS__, 'handle_gpx_upload' ] );
		add_action( "wp_ajax_citadela_check_gpx_from_media", [ __CLASS__, 'check_gpx_from_media' ] );

    }

    public static function admin_enqueue_scripts(){
    	if( get_current_screen() && get_current_screen()->id == 'citadela-item' ){
			wp_enqueue_style( 'citadela-directory-upload-and-import', self::$plugin->paths->url->css . '/admin/upload-and-import.css', [], filemtime( self::$plugin->paths->dir->css . '/admin/upload-and-import.css' ) );
			wp_localize_script( 'citadela-admin-controls', '_citadelaGpxUploadData', [
				'plupload' => [
					'filters' => [
						'max_file_size' => apply_filters( 'import_upload_size_limit', wp_max_upload_size() ) . 'b',
						'mime_types' => [
							[
								'title' => esc_html__( 'GPX files', 'citadela-directory' ),
								'extensions' => 'gpx',
							],
						],
					],
					'file_data_name' => 'gpx_file',
					'multipart_params' => [
						'action'   => 'citadela_gpx_upload',
						'_wpnonce' => wp_create_nonce( 'citadela_gpx_upload' ),
					]
				]
			] );

		}
	}


	public static function handle_gpx_upload() {
		check_ajax_referer( 'citadela_gpx_upload' );

		send_nosniff_header();
		nocache_headers();

		self::ensure_user_can_upload();

		$upload = wp_handle_upload( $_FILES['gpx_file'], [ 'test_form' => false, 'test_type' => false ] );

		if ( is_wp_error( $upload ) ) {
			wp_send_json_error( [
				'title' => '',
				'message'  => $upload->get_error_message(),
			] );
		}

		set_transient( 'citadela-gpx-upload-file', $upload['file'] );

		$data = self::ensure_gpx_requirements( $upload['file'] );

		$upload['post_mime_type'] = "text/xml";
		$file_ID = wp_insert_attachment( $upload );

		wp_send_json_success( [
			'urls' => [
				'complete' => add_query_arg( [ 'action' => 'citadela_gpx_upload_completed' ], admin_url( 'admin-ajax.php' ) ),
			],
			'data' => $data,
			'file_id' => $file_ID,
		] );

		delete_transient( 'citadela-gpx-upload-file' );
	}


	public static function ensure_user_can_upload() {
		if ( ! current_user_can( 'upload_files' ) ) {
			wp_send_json_error( [
				'message'  => esc_html__( 'You do not have permission to upload files.', 'citadela-directory' ),
			] );
		}
	}

	public static function ensure_gpx_requirements( $file ) {
		libxml_use_internal_errors(true);
		
		$gpx = simplexml_load_file( $file );
		
		if( $gpx === false ){
			wp_delete_file( get_transient( 'citadela-gpx-upload-file' ) );
			delete_transient( 'citadela-gpx-upload-file' );
			
			wp_send_json_error( [
				'title' => esc_html__( 'Invalid file format' , 'citadela-directory' ),
				'message'  => esc_html__( 'Uploaded file has not valid XML format.', 'citadela-directory' ),
			] );
		}
		
		$data = [];
		//check for required gpx data
		$gpx->registerXPathNamespace( 'a', 'http://www.topografix.com/GPX/1/0' );
		$gpx->registerXPathNamespace( 'b', 'http://www.topografix.com/GPX/1/1' );
		$gpx->registerXPathNamespace( 'ns3', 'http://www.garmin.com/xmlschemas/TrackPointExtension/v1' );
	

		$trks = $gpx->xpath( '//trk | //a:trk | //b:trk' );

		if( count( $trks ) > 0 ){
			// Standard GPX file format

			//loop through available tracks
			foreach ( $trks as $key => $trk_node ) {
				$trk = simplexml_load_string( $trk_node->asXML() );
				
				$trk->registerXPathNamespace( 'a', 'http://www.topografix.com/GPX/1/0' );
				$trk->registerXPathNamespace( 'b', 'http://www.topografix.com/GPX/1/1' );
				$trk->registerXPathNamespace( 'ns3', 'http://www.garmin.com/xmlschemas/TrackPointExtension/v1' );

				//check for segments
				$trksegs = $trk->xpath( '//trkseg | //a:trkseg | //b:trkseg' );
				$data[$key] = [ 
					'name' => isset( $trk->name ) ? current( $trk->name ) : '',
					'data' => [],
				];

				if( ! empty( $trksegs ) ){
					foreach ($trksegs as $trkseg_node){
						$trkseg = simplexml_load_string( $trkseg_node->asXML() );
						$trkseg->registerXPathNamespace( 'a', 'http://www.topografix.com/GPX/1/0' );
						$trkseg->registerXPathNamespace( 'b', 'http://www.topografix.com/GPX/1/1' );
						$trkseg->registerXPathNamespace( 'ns3', 'http://www.garmin.com/xmlschemas/TrackPointExtension/v1' );

						$seg_points = [];
						$trkpts = $trkseg->xpath( '//trkpt | //a:trkpt | //b:trkpt' );

						$points = self::build_points_data( $trkpts );

						if( ! empty( $points ) ){
							array_push($data[$key]['data'], $points);
						}
					}
				}else{

					$trkpts = $trk->xpath( '//trkpt | //a:trkpt | //b:trkpt' );

					//if( empty($trkpts) ) self::missing_gpx_data();

					$points = self::build_points_data( $trkpts );
					array_push($data[$key]['data'], $points);

				}
			}
			
			if( empty( $data[$key]['data'] ) ){
				self::missing_gpx_data();
			}

			return $data;
		}else{
			// Try to check other GPX formats

			$gpx->registerXPathNamespace( 'gpxx', 'http://www.garmin.com/xmlschemas/GpxExtensions/v3' );

			$nodes = $gpx->xpath( '//gpxx:rpt' );
			
			if ( count( $nodes ) > 0 ) {
				
				$data[0] = [ 
					'name' => '',
					'data' => [],
				];

				$points = self::build_points_data( $nodes );

				if( ! empty( $points ) ){
					array_push($data[0]['data'], $points);
				}
				
				return $data;
			
			}else{

				/* Garmin case */
				$nodes = $gpx->xpath( '//rtept | //a:rtept | //b:rtept' );
				if ( count( $nodes ) > 0 ) {

					$data[0] = [ 
						'name' => '',
						'data' => [],
					];

					$points = self::build_points_data( $nodes );

					if( ! empty( $points ) ){
						array_push($data[0]['data'], $points);
					}
					
					return $data;
				}
			}

		}

		// no one of previous format
			
		wp_delete_file( get_transient( 'citadela-gpx-upload-file' ) );
		delete_transient( 'citadela-gpx-upload-file' );
		
		self::missing_gpx_data();

	}

	public static function build_points_data( $points ){
		$points_data = [];
		foreach ( $points as $point ) {
			array_push($points_data, [
				'lat' => (float) $point['lat'],
				'lng' => (float) $point['lon'],
				'ele' => isset( $point->ele ) ? (float) round( (float) $point->ele, 2 ) : '',
				'time' => isset( $point->time ) ? current( $point->time ) : '',
				'speed' => isset( $point->speed ) ? (float) $point->speed : '',
			]);
		}

		return $points_data;
	}

	public static function missing_gpx_data() {
		wp_send_json_error( [
			'title' => esc_html__( 'Missing required GPX data' , 'citadela-directory' ),
			'message'  => esc_html__( "Uploaded file doesn't include required GPX track data.", 'citadela-directory' ),
		] );
	}

	public static function check_gpx_from_media() {
		$media_id = $_POST['attachment']['id'];
		$data = self::ensure_gpx_requirements( get_attached_file( $media_id ) );
		wp_send_json_success( [
			'data' => $data,
			'file_id' => $media_id,
		] );
	}


}
