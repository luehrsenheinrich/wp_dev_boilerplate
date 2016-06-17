<?php
/**
 * @package lh_core
 */


/**
 * The class for OnePage Layouts
 *
 * This class handles the generation, sorting and adminstration of one page layouts.
 */
class LHOnePage {
	/**
	 * Expiration time for the themes cache bucket.
	 *
	 * By default the bucket is not cached, so this value is useless.
	 *
	 * @static
	 * @access private
	 * @var bool
	 */
	private static $cache_expiration = 1800;

	private static $_onepage_template_file = "page_templates/pt-one_page.php";

	/**
	 * __construct function.
	 *
	 * @access public
	 * @return void
	 */
	public function __construct(){
		$this->action_dispatcher();
		$this->filter_dispatcher();
	}

	/**
	 * action_dispatcher function.
	 *
	 * @access public
	 * @return void
	 */
	public function action_dispatcher(){
		/* Fire our meta box setup function on the post editor screen. */
		add_action( 'load-post.php', array($this, 'post_meta_boxes_setup') );
		add_action( 'load-post-new.php', array($this, 'post_meta_boxes_setup') );
		add_action( 'template_redirect', array($this, 'template_redirect') );
		add_action( 'wp_ajax_ct_info', array($this, 'ct_info_callback') );

		/* The content template actions */
		add_action('ct-options-content_templatesct-title-php', array($this, 'mb_ct_poster'), 10, 1);

	}

	/**
	 * filter_dispatcher function.
	 *
	 * @access public
	 * @return void
	 */
	public function filter_dispatcher(){
		add_filter( 'theme_content_templates', array($this, 'theme_content_templates_filter'), 10, 3);
		add_filter( 'wp_get_nav_menu_items', array($this, 'lh_rewrite_as_hash'), 10, 3 );
	}

	/**
	 * Rewrite Menu Items for Content Templates.
	 *
	 * @access public
	 * @param mixed $items
	 * @param mixed $menu
	 * @param mixed $args
	 * @return void
	 */
	public function lh_rewrite_as_hash($items, $menu, $args){
		$temp_items = array();
		if(is_array($items)){
			foreach($items as $item){
				$post_id = url_to_postid($item->url);
				$page_template = get_post_meta($post_id, "_lh_content_template", true);
				if($page_template && file_exists(trailingslashit(WP_THEME_PATH).$page_template)){
					$post = get_post($post_id);
					$item->url = get_permalink($post->post_parent)."#".$post->post_name;
				}

				$temp_items[] = $item;
			}

			$items = $temp_items;
		}

		return $items;
	}

	/**
	 * ct_info_callback function.
	 *
	 * @access public
	 * @return void
	 */
	public function ct_info_callback(){

		$response = array(
			"error" => true,
			"success" => false,
			"msg"	=> __("A random error occured.", LANG_NAMESPACE),
		);

		$ct = $_GET['ct'];
		$ct_headers = get_file_data(trailingslashit(WP_THEME_PATH) . $ct, array("description" => "Description", "thumbnail" => "Thumbnail"));

		if(is_array($ct_headers) && $ct_headers['description'] != ""){

			if($ct_headers['thumbnail'] != ""){
				$ct_headers['thumbnail']	= trailingslashit(WP_THEME_URL) . $ct_headers['thumbnail'];
			}

			$response = array(
				"error" => false,
				"success" => true,
				"data" => $ct_headers,
			);
		}

		echo json_encode($response);
		exit;
	}

	/**
	 * This template redirect makes sure, that pages with content templates can never be accessed directly.
	 *
	 * @access public
	 * @return void
	 */
	public function template_redirect(){
		global $post;

		if(is_page() && get_post_meta($post->post_parent, '_wp_page_template', true) == self::$_onepage_template_file && get_post_meta($post->ID, '_lh_content_template', true)){
			wp_redirect(get_permalink($post->post_parent)."#".$post->post_name, 301);
			die();
		}
	}

	/**
	 * Returns the theme's page templates.
	 *
	 * @since 3.4.0
	 * @access public
	 * @from class-wp-theme.php
	 *
	 * @param WP_Post|null $post Optional. The post being edited, provided for context.
	 * @return array Array of page templates, keyed by filename, with the value of the translated header name.
	 */
	public function get_content_templates( $post = null ) {
		// If you screw up your current theme and we invalidate your parent, most things still work. Let it slide.
		if ( wp_get_theme()->errors() && wp_get_theme()->errors()->get_error_codes() !== array( 'theme_parent_invalid' ) )
			return array();

		$page_templates = $this->cache_get( 'content_templates' );

		if ( ! is_array( $page_templates ) ) {
			$page_templates = array();

			$files = (array) wp_get_theme()->get_files( 'php', 1 );

			foreach ( $files as $file => $full_path ) {
				if ( ! preg_match( '|Content Name:(.*)$|mi', file_get_contents( $full_path ), $header ) or preg_match('@'.pathinfo(__FILE__, PATHINFO_FILENAME).'@mi', $file) )
					continue;
				$page_templates[ $file ] = _cleanup_header_comment( $header[1] );
			}

			$this->cache_add( 'content_templates', $page_templates );
		}

		if ( wp_get_theme()->load_textdomain() ) {
			foreach ( $page_templates as &$page_template ) {
				$page_template = wp_get_theme()->translate_header( 'Content Name', $page_template );
			}
		}

		if ( wp_get_theme()->parent() )
			$page_templates += wp_get_theme()->parent()->get_content_templates( $post );

		/**
		 * Filter list of page templates for a theme.
		 *
		 * This filter does not currently allow for page templates to be added.
		 *
		 * @since 3.9.0
		 *
		 * @param array        $page_templates Array of page templates. Keys are filenames,
		 *                                     values are translated names.
		 * @param WP_Theme     $this           The theme object.
		 * @param WP_Post|null $post           The post being edited, provided for context, or null.
		 */
		$return = apply_filters( 'theme_content_templates', $page_templates, $this, $post );

		return array_intersect_assoc( $return, $page_templates );
	}

	/**
	 * theme_content_templates_filter function.
	 *
	 * @access public
	 * @param mixed $page_templates
	 * @param mixed $this
	 * @param mixed $post
	 * @return void
	 */
	public function theme_content_templates_filter($page_templates, $object, $post){
		unset($page_templates[str_replace(trailingslashit(WP_THEME_PATH), "", __FILE__)]);
		return $page_templates;
	}

	/**
	 * Adds theme data to cache.
	 *
	 * Cache entries keyed by the theme and the type of data.
	 *
	 * @since 3.4.0
	 * @access private
	 *
	 * @param string $key Type of data to store (theme, screenshot, headers, page_templates)
	 * @param string $data Data to store
	 * @return bool Return value from wp_cache_add()
	 */
	private function cache_add( $key, $data ) {
		return wp_cache_add( $key . '-' . $this->cache_hash, $data, 'themes', self::$cache_expiration );
	}

	/**
	 * Gets theme data from cache.
	 *
	 * Cache entries are keyed by the theme and the type of data.
	 *
	 * @since 3.4.0
	 * @access private
	 *
	 * @param string $key Type of data to retrieve (theme, screenshot, headers, page_templates)
	 * @return mixed Retrieved data
	 */
	private function cache_get( $key ) {
		return wp_cache_get( $key . '-' . $this->cache_hash, 'themes' );
	}

	/**
	 * Clears the cache for the theme.
	 *
	 * @since 3.4.0
	 * @access public
	 */
	public function cache_delete() {
		foreach ( array( 'theme', 'screenshot', 'headers', 'page_templates' ) as $key )
			wp_cache_delete( $key . '-' . $this->cache_hash, 'themes' );
		$this->template = $this->textdomain_loaded = $this->theme_root_uri = $this->parent = $this->errors = $this->headers_sanitized = $this->name_translated = null;
		$this->headers = array();
		$this->__construct( $this->stylesheet, $this->theme_root );
	}

	//
	// META BOXES STUFF
	//

	/**
	 * Fire the actions nessicary to trigger meta box generation and saving.
	 *
	 * @access public
	 * @return void
	 */
	public function post_meta_boxes_setup() {
		/* Add meta boxes on the 'add_meta_boxes' hook. */
		add_action( 'add_meta_boxes', array($this, 'add_post_meta_boxes') );
		add_action( 'save_post', array($this, 'box_save'), 10, 2 );
	}

	/**
	 * lh_add_post_meta_boxes function.
	 *
	 * @access public
	 * @return void
	 */
	public function add_post_meta_boxes() {
		global $post;
		$page_template = get_post_meta( $post->ID, '_wp_page_template', true );
		$parent_template = get_post_meta($post->post_parent, '_wp_page_template', true);
		$post_format = get_post_format( $post->ID );
		$content_template = get_post_meta( $post->ID, '_lh_content_template', true );
		// Define the post types, in which this meta box shall appear

		if($parent_template == self::$_onepage_template_file){
			add_meta_box(
				"content_template_select",
				__("Content Template", LANG_NAMESPACE),
				array($this, "mb_content_template_select"),
				"page",
				"side",
				"default"
			);

			if($content_template){
				add_meta_box(
					"content_template_options",
					__("Content Template Options", LANG_NAMESPACE),
					array($this, "mb_content_template_options"),
					"page",
					"normal",
					"default"
				);
			}
		}

		if($page_template == self::$_onepage_template_file){
			add_meta_box(
				"content_template_overview",
				__("Content Templates"),
				array($this, "mb_content_template_overview"),
				"page",
				"normal",
				"default"
			);

			remove_post_type_support('page', 'editor');

		}

	}

	/**
	 * mb_content_template_select function.
	 *
	 * @access public
	 * @param mixed $object
	 * @param mixed $box
	 * @return void
	 */
	public function mb_content_template_select($object, $box){
		$templates = $this->get_content_templates($object);
		$content_template = get_post_meta($object->ID, "_lh_content_template", true);
		wp_nonce_field( basename( __FILE__ ), 'lh_data_nonce' );
		?>

		<select class="ct-select widefat" name="lh_content_template">
			<option value=""><?php _e("No template", LANG_NAMESPACE); ?></option>
			<?php
				foreach($templates as $path => $name){
					$selected = selected( $content_template, $path, false);
					echo "<option value=\"$path\" $selected>$name</option>";
				}
			?>
		</select>
		<?php
			$ct_headers = get_file_data(trailingslashit(WP_THEME_PATH) . $content_template, array("description" => "Description", "thumbnail" => "Thumbnail"));
		?>
		<div class="description">
			<?php
			if(is_array($ct_headers) && $ct_headers['thumbnail']){
				$path = trailingslashit(WP_THEME_URL) . $ct_headers['thumbnail'];
				echo "<div class=\"thumbnail\"><img src=\"{$path}\" /></div>";
			}
			?>

			<div class="desc-text">
				<?php
					if(is_array($ct_headers) && $ct_headers['description']){
						echo $ct_headers['description'];
					} else {
						_e("No content template is selected, but the parent page is a one page master. Be aware that this content might not be shown.", LANG_NAMESPACE);
					}
				?>
			</div>
		</div>
		<p class="clear">
			<?php edit_post_link(__("Edit One Page", LANG_NAMESPACE), NULL, NULL, $object->post_parent); ?>
		</p>
		<?php

	}

	/**
	 * mb_content_template_overview function.
	 *
	 * @access public
	 * @param mixed $object
	 * @param mixed $box
	 * @return void
	 */
	public function mb_content_template_overview($object, $box){
		global $post;

		?>

		<div class="content-templates-wrapper ui-sortable">

		<?php

		// Build the query for child pages with content templates
		$args = array(
			'post_parent'	=> $object->ID,
			'post_type'		=> 'page',
			'orderby'		=> 'menu_order date',
			'order'			=> 'ASC',
			'posts_per_page'=> -1,
			'meta_key'		=> '_lh_content_template',
		);
		$query = new WP_Query( $args );
		$current_index = 0;
		if($query->have_posts()): while ( $query->have_posts() ) : $query->the_post();
			$content_template = get_post_meta($post->ID, "_lh_content_template", true);
			$ct_headers = get_file_data(trailingslashit(WP_THEME_PATH) . $content_template, array("description" => "Description", "thumbnail" => "Thumbnail"));
			?>
			<div class="content-template">
				<div class="template-thumbnail">
				<?php
					if(is_array($ct_headers) && $ct_headers['thumbnail']){
						$path = trailingslashit(WP_THEME_URL) . $ct_headers['thumbnail'];
						echo "<img src=\"{$path}\" />";
					}
				?>
				</div>
				<div class="template-title">
					<h4><?php edit_post_link(get_the_title()); ?></h4>
					<?php
						if(is_array($ct_headers) && $ct_headers['description']){
							echo "<small>" . $ct_headers['description'] . "</small>";
						}
					?>
				</div>
				<div class="template-handle">
					<span class="handle">

					</span>
				</div>
				<input type="hidden" name="ct_order[]" value="<?php echo $post->ID; ?>" />
			</div>

			<?php
			endwhile; endif;
			?>

		</div>
		<?php
	}

	/**
	 * mb_content_template_options function.
	 *
	 * @access public
	 * @param mixed $object
	 * @param mixed $box
	 * @return void
	 */
	public function mb_content_template_options($object, $box){
		$content_template = get_post_meta($object->ID, "_lh_content_template", true);
		$ct_options = (array) get_post_meta($object->ID, "_ct_options", true);
		wp_nonce_field( basename( __FILE__ ), 'lh_data_nonce' );

		$action_name = "ct-options-" . sanitize_title($content_template);

		do_action($action_name, $ct_options);
	}

	/**
	 * mb_ct_poster function.
	 *
	 * @access public
	 * @param mixed $ct_options
	 * @return void
	 */
	public function mb_ct_poster($ct_options){
		?>
			<p>
				<label for="ct_options_theme"><?php _e("Theme", LANG_NAMESPACE); ?></label><br />
				<select class="" name="ct_options[theme]" id="ct_options_theme">
					<option value=""><?php _e("Default", LANG_NAMESPACE); ?></option>
					<option value="red" <?php selected($ct_options['theme'], "red"); ?>><?php _e("Red", LANG_NAMESPACE); ?></option>
				</select>
			</p>
		<?php
	}

	/**
	 * lh_box_save function.
	 *
	 * @access public
	 * @param mixed $post_id
	 * @param mixed $post
	 * @return void
	 */
	public function box_save( $post_id, $post ) {
		/*
		 * lh_save_post_meta($post_id, $post, 'lh_data_nonce', 'post_value_name', '_meta_value_name');
		 */

		$this->save_post_meta($post_id, $post, 'lh_data_nonce', 'lh_content_template', '_lh_content_template');
		$this->save_post_meta($post_id, $post, 'lh_data_nonce', 'ct_options', '_ct_options');

		if(is_array($_POST['ct_order'])) {
			remove_action('save_post', array($this, 'box_save'));
			foreach($_POST['ct_order'] as $n => $p){
				wp_update_post(
					array(
						"ID"	=> intval($p),
						"menu_order" => $n,
					)
				);
			}
			add_action('save_post', array($this, 'box_save'));
		}
	}

	/**
	 * lh_save_post_meta function.
	 *
	 * @access public
	 * @param mixed $post_id
	 * @param mixed $post
	 * @param mixed $nonce_name
	 * @param mixed $post_value
	 * @param mixed $meta_key
	 * @return void
	 */
	public function save_post_meta( $post_id, $post, $nonce_name, $post_value, $meta_key ) {

		/* Verify the nonce before proceeding. */
		if ( !isset( $_POST[$nonce_name] ) || !wp_verify_nonce( $_POST[$nonce_name], basename( __FILE__ ) ) )
			return $post_id;

		/* Get the post type object. */
		$post_type = get_post_type_object( $post->post_type );

		/* Check if the current user has permission to edit the post. */
		if ( !current_user_can( $post_type->cap->edit_post, $post_id ) )
			return $post_id;

		/* Get the posted data and sanitize it for use as an HTML class. */
		if(isset($_POST[$post_value])){
			$new_meta_value = ($_POST[$post_value]);
		}

		/* Get the meta value of the custom field key. */
		$meta_value = get_post_meta( $post_id, $meta_key, true );

		/* If a new meta value was added and there was no previous value, add it. */
		if ( $new_meta_value && '' == $meta_value )
			add_post_meta( $post_id, $meta_key, $new_meta_value, true );

		/* If the new meta value does not match the old value, update it. */
		elseif ( $new_meta_value && $new_meta_value != $meta_value )
			update_post_meta( $post_id, $meta_key, $new_meta_value );

		/* If there is no new meta value but an old value exists, delete it. */
		elseif ( '' == $new_meta_value && $meta_value )
			delete_post_meta( $post_id, $meta_key, $meta_value );
	}

}
$_lh_onepage = new LHOnePage();
