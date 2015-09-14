<?php
/*
 * Luehrsen // Heinrich - Wordpress Theme Functions
 *
 * A useful collection of great functions of daily usage in wordpress theme development.
 *
 */

class lhThemeFunctions {

	function __construct() {
		$this->action_dispatcher();
		$this->filter_dispatcher();
	}

	function action_dispatcher(){
		add_action( 'login_enqueue_scripts', 	array($this, 'lh_login_logo' ) );
		add_action(	'admin_bar_menu', 			array($this, 'lh_change_toolbar') , 999 );
		add_action( 'admin_init', 				array($this, 'lh_remove_menu_pages' ) );
		add_action( 'admin_enqueue_scripts', 	array($this, 'admin_scripts') );
	}

	function filter_dispatcher(){
		add_filter( 'admin_footer_text', 		array($this, 'lh_admin_footer') ); //change admin footer text
		add_filter( 'mod_rewrite_rules', 		array($this, 'lh_htaccess_contents') );
		add_filter( 'login_headerurl', 			array($this, 'lh_login_logo_url' ) );
		add_filter( 'login_headertitle', 		array($this, 'lh_login_logo_url_title' ) );
		add_filter( 'oembed_result',			array($this, 'change_oembed'), 10, 3 );
		add_filter( 'login_errors',				array($this, 'login_errors'), 10 );
	}

	/**
	 * Echoes custom text in the admin footer, is called by "admin_footer_text" filter
	 *
	 * @author Hendrik Luehrsen
	 * @since 1.0
	 *
	 * @return void
	 */
	function lh_admin_footer() {
		echo "Made with &#x2661; by <a href='http://www.luehrsen-heinrich.de' target='_blank'>Luehrsen // Heinrich</a>. Powered by <a href='http://www.wordpress.org' target='_blank'>Wordpress</a>.";
	}

	/**
	 * Gracefully shortens text whithout cutting words
	 *
	 * @author Hendrik Luehrsen
	 * @since 1.0
	 *
	 * @param $str string The text, that shall be shortened
	 * @param $length int The length to which the text should be shortened
	 * @param $minword int The minimum amount of words, that shall be displayed
	 *
	 * @return The shortened string with "..." attatched.
	 */
	function shorten_text($str, $length, $minword = 3)
	{
	    $sub = '';
	    $len = 0;

	    foreach (explode(' ', $str) as $word)
	    {
	        $part = (($sub != '') ? ' ' : '') . $word;
	        $sub .= $part;
	        $len += strlen($part);

	        if (strlen($word) > $minword && strlen($sub) >= $length)
	        {
	            break;
	        }
	    }

		if($len < strlen($str) and substr($sub, strlen($sub)-1) != "."){
			$end = " ...";
		}
		else{
			$end = NULL;
		}

	    return $sub . $end ;
	}

	/**
	 * Deactivates certain menu items from wordpress administration
	 * Called by wordpress action "admin_init"
	 *
	 * @author Hendrik Luehrsen
	 * @since 1.0
	 *
	 */
	function lh_remove_menu_pages() {
		//remove_menu_page('link-manager.php');
	}

	/**
	 * Changes the wordpress toolbar the way we need it
	 * Called by wordpress action "admin_bar_menu"
	 *
	 * @author Hendrik Luehrsen
	 * @since 1.0
	 *
	 */
	function lh_change_toolbar($wp_toolbar) {
		$wp_toolbar->remove_node('wp-logo');
	}

	/**
	 * Edit the .htaccess File and add our needs
	 *
	 * @author Hendrik Luehrsen
	 * @since 1.0
	 *
	 * @param string $rules The predefined wordpress rules
	 *
	 * @return string The new rules
	 */
	function lh_htaccess_contents( $rules )
	{
		$my_content = '

# BEGIN L//H Content
<IfModule mod_deflate.c>
	SetOutputFilter DEFLATE
</IfModule>
<IfModule mod_expires.c>
	ExpiresActive On
	ExpiresByType image/png A604800
	ExpiresByType image/gif A604800
	ExpiresByType image/jpg A604800
	ExpiresByType image/jpeg A604800
	ExpiresByType text/javascript A604800
	ExpiresByType application/x-javascript A604800
	ExpiresByType text/css A604800
</IfModule>
# END L//H Content

';
	    return $my_content . $rules;
	}

	//
	// STYLING THE LOGIN PAGE
	//

	/*
	 * Add some css code to change the default logo
	 * Called by action "login_enqueue_scripts".
	 *
	 * @author Hendrik Luehrsen
	 * @since 3.1
	 *
	 * @return string The CSS Code for the head
	 */
	function lh_login_logo() { ?>
	    <style type="text/css">
	        body.login div#login h1 a {
		        background-image: url(<?php echo get_bloginfo( 'template_directory' ) ?>/img/lh_logo.png);
				background-size: 274px 41px;
				background-repeat: no-repeat;
				background-position: center center;
	            padding-bottom: 0;
	            margin-bottom: 0;
	            width: 274px;
	        }
	    </style>
	<?php
	}

	/**
	 * Change the login logo url
	 *
	 * @author Hendrik Luehrsen
	 * @since 3.1
	 *
	 * @return string The new url
	 */
	function lh_login_logo_url() {
	    return "http://www.luehrsen-heinrich.de";
	}

	/**
	 * Change the login logo title
	 *
	 * @author Hendrik Luehrsen
	 * @since 3.1
	 *
	 * @return string The new title
	 */
	function lh_login_logo_url_title() {
	    return 'Luehrsen // Heinrich - Agentur für Medienkommunikation';
	}

	/**
	 * Change the embed code, so we can apply awesome css shit
	 * Called by filter "oembed_result"
	 *
	 * @author Hendrik Luehrsen
	 * @since 1.0
	 *
	 * @param $html string The oembed html to edit
	 *
	 * @return string The edited html
	 */
	function change_oembed($html, $url, $args){
		$video_pattern = "#(\W|^)(youtube|vimeo)(\W|$)#";

		if(preg_match($video_pattern, $url)){
			$new_html = '<div class="clearfix"></div><div class="embed-wrapper"><div class="embed-responsive embed-responsive-16by9">'.$html.'</div></div>';
		} else {
			$new_html = $html;
		}

		return $new_html;
	}

	/*******************************************
		Security functions
	*******************************************/

	public function login_errors($e){
		global $errors;

		if(isset($errors->errors['incorrect_password'])){
			return __("<b>Error:</b> The login credentials are incorrect.", LANG_NAMESPACE);
		}

		if(isset($errors->errors['invalid_username'])){
			return __("<b>Error:</b> The login credentials are incorrect.", LANG_NAMESPACE);
		}

		return $e;
	}

	/*******************************************
		Admin scripts
	*******************************************/

	/**
	 * admin_scripts function.
	 *
	 * @access public
	 * @return void
	 */
	public function admin_scripts(){
		global $hook_suffix, $pagenow;

		$scripts_are_needed_in = array(
			'profile.php',
			'appearance_page_lh_theme_settings'
		);

		if( in_array($hook_suffix, $scripts_are_needed_in) ){ // Make sure our scripts are only loaded, when we actually need them
		    wp_enqueue_script( 'lh_admin', WP_THEME_URL . "/admin/admin.min.js", array( 'jquery' ), false, true );
        }

        wp_localize_script( 'lh_admin', 'lh_var',
        array( 	'theme_url' => WP_THEME_URL,
        		'choose_image'		=> __('Choose Image', LANG_NAMESPACE),
        		'select_image'		=> __('Select Image', LANG_NAMESPACE),
        ) );

		if($pagenow == "themes.php") {// && $taxonomy == "brand"){
			wp_enqueue_media();
			wp_enqueue_style( 'wp-color-picker' );
			wp_enqueue_script( 'wp-color-picker');
		}
	}

}
$_lh_theme_functions = new lhThemeFunctions();

//
// Helper Functions
//
if(!function_exists("shorten_text")){

	/**
	 * Register the helper function "shorten_text", it the function not yet exists.
	 *
	 * @access public
	 * @param mixed $str
	 * @param mixed $length
	 * @param int $minword (default: 3)
	 * @return void
	 */
	function shorten_text($str, $length, $minword = 3){
		return $_lh_theme_functions->shorten_text($str, $length, $minword = 3);
	}

}