<?php

class lh_shortcodes {

	public function __construct(){
		// The Button Shortcode
		add_shortcode( 'ecosystem_form', array($this, "ecosystem_form") );
	}

	/**
	 * The shortcode for the ecosystem form.
	 * Usage: [ecosystem_form]
	 *
	 * @access public
	 * @param mixed $atts
	 * @return void
	 */
	public function ecosystem_form($atts){
		$atts = shortcode_atts( array(
			'foo' => 'no foo',
			'baz' => 'default baz'
		), $atts, 'ecosystem_form' );

		return '<ecosystemform class="side_margins"></ecosystemform>';
	}

}
$lh_shortcodes = new lh_shortcodes();