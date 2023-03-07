<?php

namespace ctdl\pro;

function path( $relative_path = '' ) {
	return dirname( CITADELA_PRO_PLUGIN_FILE ) . ( ( $p = trim( $relative_path, '\\/' ) ) ? "/$p" : '' );
}

function url( $relative_url = '' ) {
	return plugins_url( trim( $relative_url, '\\/' ), CITADELA_PRO_PLUGIN_FILE );
}

function option( $key ) {
	if ( empty( $key ) ) return null;
	$segments = explode( '.', $key, 2 );
	return dot_get( get_option( 'citadela_pro_' . str_replace( '-', '_', $segments[0] ) ), ! empty( $segments[1] ) ? $segments[1] : '' );
}

function dot_get( $target, $key, $default = null ) {
	if ( ! $key ) return $target;
	$key = explode( '.', $key );
	while ( ! is_null( $segment = array_shift( $key ) ) ) {
		if ( is_array( $target ) and isset( $target[ $segment ] ) ) {
			$target = $target[ $segment ];
		} elseif ( is_object( $target ) and isset( $target->{ $segment } ) ) {
			$target = $target->{ $segment };
		} else {
			return $default;
		}
	}
	return $target;
}

function class_attr( $classes = [] ) {
	static $_escape;
	if ( ! $_escape ) {
		$_escape = function( $s ) {
			if ( empty( $s ) ) return '';
			$s = (string) $s;
			if ( strpos( $s, '`' ) !== false && strpbrk( $s, ' <>"\'' ) === false ) {
				$s .= ' '; // protection against innerHTML mXSS vulnerability
			}
			return htmlspecialchars( $s, ENT_QUOTES, 'UTF-8', true );
		};
	}
	$list = [];
	$classes = is_array($classes) ? $classes : func_get_args();

	foreach($classes as $class => $condition){
		if(is_int($class) and is_array($condition)){
			foreach($condition as $clss => $cndtn){
				if(is_int($clss) and is_string($cndtn)){
					$list[] = $_escape($cndtn);
				}elseif(is_string($clss) and $cndtn){
					$list[] = $_escape($clss);
				}
			}
		}elseif(is_int($class) and is_string($condition)){
			$list[] = $_escape($condition);
		}elseif(is_string($class) and $condition){
			$list[] = $_escape($class);
		}

		array_walk($list, function($item){
			$item = ' ' . trim($item) . ' ';
		});
	}
	return trim( implode( ' ', $list ) );
}

function register_autoload( $namespace, $dir ) {
	spl_autoload_register( function ( $class ) use ( $namespace, $dir ) {
		if(
			substr( $class, 0, strlen( $namespace ) ) === $namespace and
			file_exists( $file = sprintf( "{$dir}/%s.php", str_replace( [ $namespace, '\\' ], [ '', '/' ], $class ) ) )
		) {
			require_once $file;
			return;
		}
	} );
}

function log( $label, $msg = '' ) {
	if ( defined( 'WP_DEBUG' ) and defined( 'WP_DEBUG_LOG' ) and WP_DEBUG and WP_DEBUG_LOG ) {
	    error_log(
	        sprintf( "[%s] %s%s\n", date( 'j.n.Y H:i:s' ), $label, $msg ? ": $msg" : '' ),
	        3,
	        ( ( is_string( WP_DEBUG_LOG ) and realpath( dirname( WP_DEBUG_LOG ) ) ) ? dirname( WP_DEBUG_LOG ) : WP_CONTENT_DIR ) . '/.ht-citadela-pro.log'
	    );
	}
}
