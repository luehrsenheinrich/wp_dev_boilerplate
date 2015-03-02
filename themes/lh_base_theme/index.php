<?php get_header(); ?>

<div>
	<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

    	<div <?php post_class(); ?>>
        	<h3><?php the_title(); ?></h3>
        	<div><?php the_content(); ?></div>
        </div>

    <?php endwhile; endif; ?>
</div>

<?php get_footer(); ?>