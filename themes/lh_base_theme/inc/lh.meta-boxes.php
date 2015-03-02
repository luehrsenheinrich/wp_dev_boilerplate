<?php

// TODO: Refactor this file, use OOP for speed and giggles

/* Fire our meta box setup function on the post editor screen. */
add_action( 'load-post.php', 'lh_post_meta_boxes_setup' );
add_action( 'load-post-new.php', 'lh_post_meta_boxes_setup' );


/**
 * lh_post_meta_boxes_setup function.
 *
 * @access public
 * @return void
 */
function lh_post_meta_boxes_setup() {

	/* Add meta boxes on the 'add_meta_boxes' hook. */
	add_action( 'add_meta_boxes', 'lh_add_post_meta_boxes' );
	add_action( 'save_post', 'lh_box_save', 10, 2 );
}

/**
 * lh_add_post_meta_boxes function.
 *
 * @access public
 * @return void
 */
function lh_add_post_meta_boxes() {
	global $post;
	$page_template = get_post_meta( $post->ID, '_wp_page_template', true );
	$post_format = get_post_format( $post->ID );
	// Define the post types, in which this meta box shall appear

}

///
/// BOXES   ====================================
///




///
/// TOOLS	====================================
///


/**
 * lh_box_save function.
 *
 * @access public
 * @param mixed $post_id
 * @param mixed $post
 * @return void
 */
function lh_box_save( $post_id, $post ) {
	/*
	 * lh_save_post_meta($post_id, $post, 'lh_data_nonce', 'post_value_name', '_meta_value_name');
	 */
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
function lh_save_post_meta( $post_id, $post, $nonce_name, $post_value, $meta_key ) {

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