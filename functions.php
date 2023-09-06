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

function add_images() {
  add_image_size( 'social-media', 1200, 630, array( 'center', 'center') );
}

add_action( 'after_setup_theme', 'add_images' );

function add_menus() {
  register_nav_menu( 'desktop_nav', __('Desktop Navigation') );
  register_nav_menu( 'mobile_nav', __('Mobile Navigation') );
  register_nav_menu( 'footer_nav', __('Footer Navigation') );
}
add_action( 'init', 'add_menus' );

/**
 * @link http://stackoverflow.com/a/3261107/247223
 */
add_filter( 'sanitize_file_name', function( $filename ) {
  $info = pathinfo( $filename );
  $ext  = empty( $info['extension'] ) ? '' : '.' . $info['extension'];
  $name = basename( $filename, $ext );

  return md5( $name ) . $ext;
}, 10);

CONST CUSTOM_PAGE_TEMPLATES = array(
  array('slug' => 'about', 'label' => 'About Us'),
  array('slug' => 'get-involved', 'label' => 'Get Involved'),
  array('slug' => 'people', 'label' => 'People'),
  array('slug' => 'projects', 'label' => 'All Projects'),
  array('slug' => 'shop', 'label' => 'Shop'),
  array('slug' => 'homepage', 'label' => 'Home Page'),
); 

/**
 * Add file-less page templates to the page template dropdown 
 */
add_filter('theme_page_templates', function($page_templates, $wp_theme, $post) {
  foreach(CUSTOM_PAGE_TEMPLATES as $template) {
    // Append if it doesn't already exists
    if (!isset($page_templates[$template['slug']])) {
      $page_templates[$template['slug']] = $template['label'];
    }
  }
  return $page_templates;
}, PHP_INT_MAX, 3);


function my_render_layout_support_flag( $block_content, $block ) {
  $block_type     = WP_Block_Type_Registry::get_instance()->get_registered( $block['blockName'] );
  $support_layout = block_has_support( $block_type, array( '__experimentalLayout' ), false );

  if ( ! $support_layout ) {
      return $block_content;
  }

  $block_gap             = wp_get_global_settings( array( 'spacing', 'blockGap' ) );
  $default_layout        = wp_get_global_settings( array( 'layout' ) );
  $has_block_gap_support = isset( $block_gap ) ? null !== $block_gap : false;
  $default_block_layout  = _wp_array_get( $block_type->supports, array( '__experimentalLayout', 'default' ), array() );
  $used_layout           = isset( $block['attrs']['layout'] ) ? $block['attrs']['layout'] : $default_block_layout;
  if ( isset( $used_layout['inherit'] ) && $used_layout['inherit'] ) {
      if ( ! $default_layout ) {
          return $block_content;
      }
      $used_layout = $default_layout;
  }

  $class_name = wp_unique_id( 'wp-container-' );
  $gap_value  = _wp_array_get( $block, array( 'attrs', 'style', 'spacing', 'blockGap' ) );
  // Skip if gap value contains unsupported characters.
  // Regex for CSS value borrowed from `safecss_filter_attr`, and used here
  // because we only want to match against the value, not the CSS attribute.
  if ( is_array( $gap_value ) ) {
      foreach ( $gap_value as $key => $value ) {
          $gap_value[ $key ] = $value && preg_match( '%[\\\(&=}]|/\*%', $value ) ? null : $value;
      }
  } else {
      $gap_value = $gap_value && preg_match( '%[\\\(&=}]|/\*%', $gap_value ) ? null : $gap_value;
  }

  $fallback_gap_value = _wp_array_get( $block_type->supports, array( 'spacing', 'blockGap', '__experimentalDefault' ), '0.5em' );

  // If a block's block.json skips serialization for spacing or spacing.blockGap,
  // don't apply the user-defined value to the styles.
  $should_skip_gap_serialization = wp_should_skip_block_supports_serialization( $block_type, 'spacing', 'blockGap' );
  $style                         = wp_get_layout_style( ".$class_name", $used_layout, $has_block_gap_support, $gap_value, $should_skip_gap_serialization, $fallback_gap_value );
  // This assumes the hook only applies to blocks with a single wrapper.
  // I think this is a reasonable limitation for that particular hook.
  $content = preg_replace(
      '/' . preg_quote( 'class="', '/' ) . '/',
      'class="' . esc_attr( $class_name ) . ' ',
      $block_content,
      1
  );

  // This is where the changes happen

  return '<style>' . $style . '</style>' . $content;
}

// remove_filter( 'render_block', 'wp_render_layout_support_flag' );
// add_filter( 'render_block', 'my_render_layout_support_flag', 10, 2 );

// https://gist.github.com/KevinBatdorf/daec9345115279f1c9fe49deb589882f
add_filter('render_block', function ($block_content, $block) {
  $block_type     = \WP_Block_Type_Registry::get_instance()->get_registered($block['blockName']);
  $support_layout = block_has_support($block_type, array( '__experimentalLayout' ), false);
  if (! $support_layout) {
      return $block_content;
  }
  $block_gap             = wp_get_global_settings(array( 'spacing', 'blockGap' ));
  $default_layout        = wp_get_global_settings(array( 'layout' ));
  $has_block_gap_support = isset($block_gap) ? null !== $block_gap : false;
  $default_block_layout  = _wp_array_get($block_type->supports, array( '__experimentalLayout', 'default' ), array());
  $used_layout           = isset($block['attrs']['layout']) ? $block['attrs']['layout'] : $default_block_layout;
  if (isset($used_layout['inherit']) && $used_layout['inherit']) {
      if (! $default_layout) {
          return $block_content;
      }
      $used_layout = $default_layout;
  }
  $id        = uniqid();
  $gap_value = _wp_array_get($block, array( 'attrs', 'style', 'spacing', 'blockGap' ));
  // Skip if gap value contains unsupported characters.
  // Regex for CSS value borrowed from `safecss_filter_attr`, and used here
  // because we only want to match against the value, not the CSS attribute.
  $gap_value = preg_match('%[\\\(&=}]|/\*%', $gap_value) ? null : $gap_value;
  $style     = wp_get_layout_style(".wp-container-$id", $used_layout, $has_block_gap_support, $gap_value);

  // This assumes the hook only applies to blocks with a single wrapper.
  // I think this is a reasonable limitation for that particular hook.
  $content = preg_replace(
      '/' . preg_quote('class="', '/') . '/',
      'class="wp-container-' . $id . ' ',
      $block_content,
      1
  );

  // This is all that's really being modified here
  return $content . ($style ? '<style>' . $style . '</style>' : '');
}, 10, 2);


// enqueue all spectra files
add_action( 'wp_enqueue_scripts', 'enqueue_scripts_by_post_id' );
function enqueue_scripts_by_post_id() {
  $pages = get_pages();
  
  foreach ($pages as $page) {
    // Create Instance. Pass the Post ID.
    $post_assets_instance = new UAGB_Post_Assets( $page->ID );
    
    // Enqueue the Assets.
    $post_assets_instance->enqueue_scripts();
  }
}

// https://stackoverflow.com/questions/73970158/how-can-i-avoid-gutenberg-blocks-styles-be-written-inline
add_filter( 'should_load_separate_core_block_assets', '__return_false' );

add_action('wp_footer', function () {
  // wp_dequeue_style('core-block-supports');
});