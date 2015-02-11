<?php

class lh_theme_settings {

	/**
	 * __construct function.
	 *
	 * @access public
	 * @return void
	 */
	public function __construct(){
		// Actions
		add_action(	'admin_menu',		array($this, 'add_theme_settings_page') );
		add_action( 'admin_notices', 	array($this, 'theme_settings_admin_notices') );
		add_action( 'admin_init',		array($this, 'register_settings') );
	}

	/**
	 * register_settings function.
	 *
	 * @access public
	 * @return void
	 */
	public function register_settings(){
		// appearance_page_lh_theme_settings

		$args = array(
			'id'			=> "settings_examples",
			'title'			=> __("Some Settings", LANG_NAMESPACE),
			'page'			=> "lh_theme_settings",
			'description'	=> __("These are just some examples of how settings could look like.", LANG_NAMESPACE),
		);
		$settings_example_section = new lh_settings_section($args);


		$args = array(
			'id'				=> 'text_field_example',
			'title'				=> __("Text Field", LANG_NAMESPACE),
			'page'				=> 'lh_theme_settings',
			'section'			=> 'settings_examples',
			'description'		=> __("A default text field", LANG_NAMESPACE),
			'type'				=> 'text', // text, textarea, password, checkbox
			'option_group'		=> "appearance_page_lh_theme_settings",
		);
		$text_field_example = new lh_settings_field($args);


		$args = array(
			'id'				=> 'text_area_example',
			'title'				=> __("Text Area", LANG_NAMESPACE),
			'page'				=> 'lh_theme_settings',
			'section'			=> 'settings_examples',
			'description'		=> __("A default text area", LANG_NAMESPACE),
			'type'				=> 'textarea', // text, textarea, password, checkbox
			'option_group'		=> "appearance_page_lh_theme_settings",
		);
		$text_area_example = new lh_settings_field($args);

		$args = array(
			'id'				=> 'password_area_example',
			'title'				=> __("Password Area", LANG_NAMESPACE),
			'page'				=> 'lh_theme_settings',
			'section'			=> 'settings_examples',
			'description'		=> __("A default password area", LANG_NAMESPACE),
			'type'				=> 'password', // text, textarea, password, checkbox
			'option_group'		=> "appearance_page_lh_theme_settings",
		);
		$text_area_example = new lh_settings_field($args);


	}

	//
	// Register the Theme Settings Page
	//

	/**
	 * add_theme_settings_page function.
	 *
	 * @access public
	 * @return void
	 */
	public function add_theme_settings_page(){
		$theme_page = add_theme_page( __("Theme Settings", LANG_NAMESPACE), __("Theme Settings", LANG_NAMESPACE), 'switch_themes', 'lh_theme_settings', array($this, 'lh_settings_page') );
	}

	/**
	 * lh_settings_page function.
	 *
	 * @access public
	 * @return void
	 */
	public function lh_settings_page(){
		?>
		<div class="wrap">
			<div class="icon32" id="icon-options-general"></div>
			<h2><?php _e("Theme Settings", LANG_NAMESPACE); ?></h2>

			<form action="options.php" method="post">
				<?php
					settings_fields('appearance_page_lh_theme_settings');
					do_settings_sections('lh_theme_settings');
				?>
				<p class="submit">
					<input name="Submit" type="submit" class="button-primary" value="<?php esc_attr_e('Save Changes','gb'); ?>" />
				</p>

			</form>
		</div><!-- wrap -->
	<?php
	}

	/**
	 * theme_settings_admin_notices function.
	 *
	 * @access public
	 * @return void
	 */
	public function theme_settings_admin_notices(){
		if($_GET['page'] != "lh_theme_settings"){
			return;
		}

		if($_GET['settings-updated'] == true){
			add_settings_error('lh_theme_settings', 'lh_theme_settings', __("Successfully updated.", LANG_NAMESPACE) , 'updated');
		}

		settings_errors('lh_theme_settings');

	}

}
$lh_theme_settings = new lh_theme_settings();


/**
 * lh_settings_section class.
 */
class lh_settings_section {

	private $args;

	/**
	 * __construct function.
	 *
	 * @access public
	 * @param mixed $args
	 * @return void
	 */
	public function __construct( $args ){
		$defaults = array(
			'id'			=> NULL,
			'title'			=> NULL,
			'page'			=> NULL,
			'description'	=> NULL,
		);
		$args = wp_parse_args( $args, $defaults );

		$this->args = $args;

		$this->register_section();
	}

	/**
	 * register_section function.
	 *
	 * @access private
	 * @param mixed $args
	 * @return void
	 */
	private function register_section(){
		add_settings_section(
				$this->args['id'],
				$this->args['title'],
				array($this, 'output_callback'),
				$this->args['page']
		);
	}

	/**
	 * output_callback function.
	 *
	 * @access public
	 * @return void
	 */
	public function output_callback(){
		?>
			<p><?php echo $this->args['description'] ?></p>
		<?php
	}

}

/**
 * lh_settings_field class.
 */
class lh_settings_field {

	private $args;

	/**
	 * __construct function.
	 *
	 * @access public
	 * @param mixed $args
	 * @return void
	 */
	public function __construct( $args ){
		$defaults = array(
			'id'				=> NULL,
			'title'				=> NULL,
			'page'				=> NULL,
			'section'			=> NULL,
			'description'		=> NULL,
			'type'				=> 'text', // text, textarea, password, checkbox
			'sanitize_callback'	=> NULL,
			'option_group'		=> NULL,
		);

		$this->args = wp_parse_args( $args, $defaults );

		$this->register_field();
	}

	/**
	 * register_field function.
	 *
	 * @access private
	 * @return void
	 */
	private function register_field(){
		add_settings_field(
		 		$this->args['id'],
				'<label for="'.$this->args['id'].'">'.$this->args['title'].'</label>',
				array($this, 'output_callback'),
				$this->args['page'],
				$this->args['section']
		);

		register_setting($this->args['option_group'], $this->args['id'], $this->args['sanatize_callback'] );
	}

	/**
	 * output_callback function.
	 *
	 * @access public
	 * @return void
	 */
	public function output_callback(){
		$t = $this->args['type'];
		if($t == "text"):
		?>
			<fieldset>
				<input type="text" class="all-options" name="<?=$this->args['id']?>" id="<?=$this->args['id']?>" value="<?=get_option($this->args['id'])?>">
				<p class="description">
					<?php echo $this->args['description']; ?>
				</p>
			</fieldset>
		<?php
		elseif($t == "textarea"):
		?>
			<fieldset>
				<textarea class="all-options" name="<?=$this->args['id']?>" id="<?=$this->args['id']?>"><?=get_option($this->args['id'])?></textarea>
				<p class="description">
					<?php echo $this->args['description']; ?>
				</p>
			</fieldset>
		<?php
		elseif($t == "password"):
		?>
			<fieldset>
				<input type="password" class="all-options" name="<?=$this->args['id']?>" id="<?=$this->args['id']?>" autocomplete="off" value="<?=get_option($this->args['id'])?>">
				<p class="description">
					<?php echo $this->args['description']; ?>
				</p>
			</fieldset>
		<?php
		elseif($t == "category"):
		?>
			<fieldset>
				<?php
				$args = array(
					"name"				=> $this->args['id'],
					"id"				=> $this->args['id'],
					"selected"			=> get_option($this->args['id']),
					"show_option_none"	=> __("Not selected", LANG_NAMESPACE),
				);
				wp_dropdown_categories( $args ); ?>
 				<p class="description">
					<?php echo $this->args['description']; ?>
				</p>
			</fieldset>
		<?php
		elseif($t == "callback"):

			call_user_func($this->args['callback'], $this->args);

		endif;
	}

}