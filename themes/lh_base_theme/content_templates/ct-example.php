<?php
/**
 * Content Name: Example
 * Description: A general purpose template to display text.
 * Thumbnail: img/ct-icons/example.png
 */

global $post, $current_index, $query, $_longpage_elements;

?>

<div <? post_class("ct-wrapper ct-example clearfix"); ?> id="<?=$post->post_name?>">
	<article class="container">
		<div class="row">
			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 content-block">
				<div class="the_title">
					<h2><?php the_title(); ?></h2>
				</div>
				<div class="the_content">
					<?php the_content(); ?>
				</div>
			</div>
		</div>
	</article>
	<?php //echo get_lh_longpage_navi($current_index, $_longpage_elements); ?>
</div>