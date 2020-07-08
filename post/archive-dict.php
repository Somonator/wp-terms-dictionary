<?php get_header(); ?>

	<section id="primary" class="">
		<main id="main" class="post" role="main">

		<?php if ( have_posts() ) : ?>

			<header class="page-header">
			<h1 class="title-letter"> <?php echo __('Terms starting with', 'terms-dictionary'); ?> "<?php single_cat_title(); ?>"</h1>
			<div class="alphabet">
<?php
$args = array(
  'taxonomy'     => 'dict-letter', 
  'orderby'      => 'name',  
  'show_count'   => 0,       
  'pad_counts'   => 0,       
  'hierarchical' => 1, 
  'hide_empty'   => 0,  
  'title_li'     => ''   
);
?>

<ul>
<?php wp_list_categories( $args ); ?>
</ul>
</div>
			</header>
 <div class="dict1"> 
<ul> 
<?php while (have_posts()) : the_post(); ?> 
<a href="<?php the_permalink() ?>" ><li><a href="<?php the_permalink() ?>"><?php the_post_thumbnail('thumbnail'); ?> </a> 
<a href="<?php the_permalink() ?>" ><?php the_title(); ?></a><br><div class="dict_text"><?php the_excerpt(); ?></div></p></a></li> </a> 
<?php endwhile; ?> 
</ul> 
</div>
			<?php
			
			the_posts_pagination( array(
				'prev_text'          => __( 'Previous page', 'orders-table' ),
				'next_text'          => __( 'Next page', 'orders-table'),
				'before_page_number' => '<span class="meta-nav screen-reader-text">' . __( 'Page', 'orders-table' ) . ' </span>',
			) );

		endif;
		?>

		</main>
	</section>

<?php get_footer(); ?>
