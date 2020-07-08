<?php get_header(); ?>
<div id="primary" class="content-area">
<main id="main" class="site-main" role="main">
    <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
    
	<header class="entry-header">
        <h1 class="entry-title post-title"><?php the_title(); ?></h1>
	</header>	
	
		<div class="entry-content">
		   <div class="content-dict"><?php the_content(); ?></div>
	  <div class="miniatura"><?php echo get_the_post_thumbnail(); ?>  </div>
</div>
    <?php endwhile; else: ?>
    <?php endif; ?>
	<?php edit_post_link( __( 'Edit', 'terms-dictionary' ), '<footer class="entry-footer"><span class="edit-link">', '</span></footer><!-- .entry-footer -->' ); ?>
</main>
</div>
<?php get_sidebar(); ?>
<?php get_footer(); ?>