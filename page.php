<?php

// header("Location: https://superhero-me-2023.vercel.app/" . $_SERVER['REQUEST_URI']);
// die();

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