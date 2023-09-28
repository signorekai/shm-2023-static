<?php

if(!$_SERVER['HTTP_REQUEST_PAGE']) {
	header("Location: https://superherome.sg/" . $_SERVER['REQUEST_URI'], true, 301);
	die();
}

get_header();

?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

	<div class="entry-content">
		<?php
		the_content();
		?>
	</div><!-- .entry-content -->

</article><!-- #post-<?php the_ID(); ?> -->
<?php

get_footer();