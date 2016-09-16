<?php
/*
 * Luehrsen // Heinrich - Wordpress Theme Functions
 *
 * A useful collection of great functions of daily usage in wordpress theme development.
 *
 */

class lhThemeCustomizer {

	function __construct() {
		$this->action_dispatcher();
		$this->filter_dispatcher();
	}

	function action_dispatcher(){
		add_action( 'customize_register', array( $this, 'customize_register' ) );
		add_action( 'customize_preview_init', array($this, 'customize_preview_js') );

	}

	function filter_dispatcher(){

	}
	
	public function customize_register($wp_customize){
		$wp_customize->remove_section('colors');

		/////////////
		/// SECTIONS
		/////////////
		// The HEADER section
		$wp_customize->add_section( 'lh_header' , array(
			'title'	  => __( 'Header', LANG_NAMESPACE ),
			'priority'   => 30,
		) );


		/////////////
		/// SETTINGS
		/////////////
		// The Header Logo Setting
		$wp_customize->add_setting("header_logo", array(
			'default'		=> '',
			'capability'	 => 'edit_theme_options',
			'type'		   => 'option',
		));
		$wp_customize->add_control(
				new WP_Customize_Image_Control(
				$wp_customize,
				'header_logo',
				array(
					'label'	  	=> __("Header Logo", LANG_NAMESPACE),
					'section'		=> 'lh_header',
					'settings'   	=> 'header_logo',
				)
			)
		);
	}	

	/**
	 * Binds JS handlers to make Theme Customizer preview reload changes asynchronously.
	 */
	public function customize_preview_js() {
		wp_enqueue_script( 'customizer', get_template_directory_uri() . '/js/customizer.js', array( 'customize-preview' ), '1', true );
	}
}
$lh_Theme_Customizer = new lhThemeCustomizer();
