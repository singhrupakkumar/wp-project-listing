<?php

namespace Citadela\Pro\Layouts\Importers;

class Images
{
	static function import($images_zip_url)
	{
		$zip_file = download_url( $images_zip_url, 30 * MINUTE_IN_SECONDS );

		\ctdl\pro\log(__METHOD__, 'downloaded');

		if ( is_wp_error( $zip_file ) ) {
			return $zip_file;
		}

		$cb = function( $method ) { return 'direct'; };
		add_filter( 'filesystem_method', $cb );
		WP_Filesystem();

		$result = unzip_file( $zip_file, wp_upload_dir()['basedir'] );

		\ctdl\pro\log(__METHOD__, 'unziped');

		remove_filter( 'filesystem_method', $cb );

		if ( is_wp_error( $result ) ) {
			return $result;
		} else {
			wp_delete_file( $zip_file );
		}
	}
}
