<?php

function add_supports() {
  add_theme_support( 'post-thumbnails' );
  add_theme_support( 'menus' );

  add_theme_support( 'html5', array(
		'comment-form',
		'comment-list',
		'gallery',
		'caption',
	) );
}
add_supports();

/**
 * @link http://stackoverflow.com/a/3261107/247223
 */
add_filter( 'sanitize_file_name', function( $filename ) {
  $info = pathinfo( $filename );
  $ext  = empty( $info['extension'] ) ? '' : '.' . $info['extension'];
  $name = basename( $filename, $ext );

  return md5( $name ) . $ext;
}, 10);
