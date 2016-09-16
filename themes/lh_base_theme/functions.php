<?php

/*
 * Definition of needed Constants
 */
if(!defined('WP_THEME_URL')) {
	define( 'WP_THEME_URL', get_bloginfo('stylesheet_directory'));
}

if(!defined('WP_THEME_PATH')){
	define( 'WP_THEME_PATH', get_stylesheet_directory());
}

if(!defined('WP_JS_URL')) {
	define( 'WP_JS_URL' , get_bloginfo('template_url').'/js');
}

if(!defined('LANG_NAMESPACE')){
	define( 'LANG_NAMESPACE', "lh");
}


/*
 * Include needed files from the inc directory
 */
foreach ( glob( dirname( __FILE__ )."/inc/*.php" ) as $file )
    require_once( $file );


/**
 * Enqueue the needed scripts and styles in the frontend
 * Called by action "wp_enqueue_scripts"
 *
 * @author Hendrik Luehrsen
 * @since 1.0
 *
 * @return void
 */
function lh_enqueue_scripts(){
	// CSS
	wp_enqueue_style('style', WP_THEME_URL.'/style.css', NULL, '1.0', 'all');

	// Register Scripts used by the theme
	wp_register_script('main', (WP_JS_URL . "/main.min.js"), array("jquery"), '1', true);

	wp_enqueue_script('main');
}
add_action("wp_enqueue_scripts", "lh_enqueue_scripts");

/**
 * Enqueue the needed scripty and styles in the admin backend
 * @return void
 */
function lh_admin_scripts(){
	global $hook_suffix;

	$scripts_are_needed_in = array(
		'post.php',
		'post-new.php',
		'edit-tags.php',
		'appearance_page_lh_theme_settings',
	);

	if( in_array($hook_suffix, $scripts_are_needed_in) ){ // Make sure our scripts are only loaded, when we actually need them
	    //wp_enqueue_style( 'wp-color-picker' );
	    wp_enqueue_style( 'lh_admin_style', WP_THEME_URL . '/admin/admin.css' );
	    wp_enqueue_style( 'wp-jquery-ui-dialog' );
	    wp_enqueue_script( 'lh_admin', WP_THEME_URL . "/admin/admin.js", array( 'wp-color-picker', 'jquery-ui-dialog', 'jquery-ui-draggable' ), false, true );

	    wp_localize_script( 'lh_admin', 'loop_content',
            array( 'title' => __("Add new post", LANG_NAMESPACE) ) );
    }

}
add_action( 'admin_enqueue_scripts', 	'lh_admin_scripts' );

/**
 * Add language support
 * Called by action "after_setup_theme"
 *
 * @author Hendrik Luehrsen
 * @since 1.0
 *
 * @return void
 */
function lh_load_theme_textdomain(){
    load_theme_textdomain(LANG_NAMESPACE, get_template_directory() . '/lang');
}
add_action('after_setup_theme', 'lh_load_theme_textdomain');

/**
 * Add post thumbnail support and define custom image sizes
 * @return void
 */
function lh_theme_image(){
	add_theme_support( 'post-thumbnails' );
	/*
	add_image_size('project', 800, 320, array('center', 'center'));
	set_post_thumbnail_size( 1920, 1080, true );
	add_image_size('full_hd_nocrop', 1920, 1080, false);
	add_image_size('container', 1280, 720, true);
	add_image_size('container-medium', 320, 180, true);
	add_image_size('half-lg-size', 592, 333, true);
	*/
}
add_action("init", "lh_theme_image");

/**
 * lh_register_menus function.
 *
 * @access public
 * @return void
 */
function lh_register_menus(){
	register_nav_menus( array(
		'header' 	=> __("Header", LANG_NAMESPACE),
	) );
}
add_action('init', 'lh_register_menus');