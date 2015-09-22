<?php get_header(); ?>

<div class="container">
	<div class="row">
	<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

    	<div <?php post_class("col-xs-12"); ?>>
        	<h3><?php the_title(); ?></h3>
        	<div><?php the_content(); ?></div>
        </div>

    <?php endwhile; endif; ?>
	</div>
</div>

<?php get_footer(); ?>