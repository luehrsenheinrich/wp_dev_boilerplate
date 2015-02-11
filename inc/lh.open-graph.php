<?php

class lh_open_graph {

	private $lh_open_graph;

	/**
	 * 	Construct the class
	 */
	public function __construct($args){

		global $post;
		$thumb = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'tn_lg' );

		/**
		 * Define the array of defaults
		 */
		$defaults = array(
			'fb:app_id' 			=> get_option("fb_app_id"),
			'og:type' 				=> "article",
			'og:title' 				=> strip_tags( get_the_title() ),
			'og:image' 				=> $thumb[0],
			'og:description'		=> shorten_text(strip_tags(apply_filters('the_content', $post->post_content)), 255),
			'og:url'				=> get_permalink(),
			'og:site_name'			=> get_bloginfo("name"),
		);
		$args = wp_parse_args( $args, $defaults );

		$this->lh_open_graph = $args;

		add_action("wp_head", array($this, "display_open_graph") );
	}

	/**
	 * Display the open graph data in the header
	 */
	public function display_open_graph(){
		$args = $this->lh_open_graph;

		if(is_array($args)){
			foreach($args as $key => $value) {
				$this->display_meta_tag($key, $value);
			}
		}

	}

	/**
	 * Display Meta Tag
	 */
	private function display_meta_tag($tag_property, $tag_content, $echo = true){

		if($tag_content == "" or $tag_content == NULL){
			return false;
		}

		$tag = '<meta property="'.$tag_property.'" content="'.$tag_content.'" />
';

		if($echo){
			echo $tag;
		} else {
			return $tag;
		}

	}
}

function lh_set_open_graph($args = array()){
	$lh_open_graph = new lh_open_graph($args);
}