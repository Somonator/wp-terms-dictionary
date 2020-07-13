<div class="dictionary">
	<?php
	$args = array( 
		'post_type' => 'dict-terms',
		'orderby' => 'title',	
		'order' => 'ASC',
		'paged' => isset($_GET['page_terms']) ? $_GET['page_terms'] : 1
	);

	if (isset($_GET['letter']) && !empty($_GET['letter'])) {
		$args['tax_query'][0][0]['taxonomy'] = 'dict-terms-letter';
		$args['tax_query'][0][0]['field'] = 'term_id';
		$args['tax_query'][0][0]['terms'] = $_GET['letter'];
	}

	$td = new WP_Query($args);	

	if ($td->have_posts()) {
		echo '<nav class="letters">';
		echo '<a href="." class="all-letters">' . __('All terms', 'terms-dictionary') . '</a>';

			$args = array(
				'taxonomy' => 'dict-terms-letter',
				'order' => 'ASC',
				'hide_empty' => true
			);
			$terms = get_terms($args);
				
			foreach( $terms as $term ) {
				$current = isset($_GET['letter']) && $_GET['letter'] == $term->term_id ? 'current' : null;
				echo '<a href="?letter=' . $term->term_id . '" class="letter ' . $current . '">' . $term->name . '</a>';
			}
		echo '</nav>';

		echo '<div class="terms">';
			while ($td->have_posts()):$td->the_post();
				echo '<p class="term">';

				if (get_the_post_thumbnail()) {
					echo the_post_thumbnail('dictionary-thumbnail');
				}

				echo '<strong>' . get_the_title() . '</strong>' . ' - ' . get_the_content();
				echo '</p>';
			endwhile;
		echo '</div>';
		
		if ($td->max_num_pages > 1) {
			echo '<div class="pagination">';
				$args = array(
					'base' => '%_%',
					'format' => '?page_terms=%#%',
					'total' => $td->max_num_pages,
					'current' => $td->query['paged'],
					'prev_next' => false
				); 

				echo str_replace( array('href=""', "href=''"), 'href="."', paginate_links($args));
			echo '</div>';
		}
	} else {
		echo '<h3>' . __('No terms yet ...', 'terms-dictionary') . '</h3>';
	}
	?>
</div>