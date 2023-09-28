<?php

if(!$_SERVER['HTTP_REQUEST_PAGE']) {
	header("Location: https://superherome.sg/" . $_SERVER['REQUEST_URI'], true, 301);
	die();
}

get_header();

if ( have_posts() ) {

	// Load posts loop.
	while ( have_posts() ) {
		the_post();

	}

}

get_footer();