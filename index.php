<?php

// header("Location: https://superhero-me-2023.vercel.app/" . $_SERVER['REQUEST_URI']);
// die();

get_header();

if ( have_posts() ) {

	// Load posts loop.
	while ( have_posts() ) {
		the_post();

	}

}

get_footer();