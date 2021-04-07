<div class="dictionary">
	<?php
	$page_link = get_permalink();

	$is_simple_permalinks = isset($_GET['page_id']);
	$is_search_page = isset($_GET['terms_s']) && !empty($_GET['terms_s']);
	$is_letter_page = isset($_GET['letter']) && !empty($_GET['letter']);
	$is_pagination_page = isset($_GET['page_terms']) && !empty($_GET['page_terms']);

	$page_id = $is_simple_permalinks ? $_GET['page_id'] : null;
	$search_query = $is_search_page ? $_GET['terms_s'] : '';
	$current_letter = $is_letter_page ? $_GET['letter'] : null;
	$page_terms = $is_pagination_page ? $_GET['page_terms'] : 1;

	$is_show_search = filter_var($atts['show_search'], FILTER_VALIDATE_BOOLEAN);	
	$terms_per_page = $atts['terms_per_page'];

	$args = array( 
		'post_type' => 'dict-terms',
		'orderby' => 'title',	
		'order' => 'ASC',
		'paged' => $page_terms
	);

	if ($terms_per_page) {
		$args['posts_per_page'] = $terms_per_page;
	}

	if ($is_search_page) {
		$args['s'] = $search_query;
	}

	if ($is_letter_page) {
		$args['tax_query'][0][0]['taxonomy'] = 'dict-terms-letter';
		$args['tax_query'][0][0]['field'] = 'term_id';
		$args['tax_query'][0][0]['terms'] = $current_letter;
	}

	$td = new WP_Query($args);



	if ($is_show_search) {
		echo '<form class="terms_search">';

		if ($is_simple_permalinks) {
			echo '<input type="hidden" name="page_id" value="' . $page_id . '">';
		}
		
		echo '<input type="text" name="terms_s" value="' . $search_query . '" required>';
		echo '<input type="submit" value="search">';
		echo '</form>';
	}

	if ($td->have_posts()) {
		echo '<nav class="letters">';
		echo '<a href="' . $page_link . '" class="all-letters">' . __('All terms', 'terms-dictionary') . '</a>';

		if (!$is_search_page) {
			$args = array(
				'taxonomy' => 'dict-terms-letter',
				'order' => 'ASC',
				'hide_empty' => true
			);
			$terms = get_terms($args);

			unset($_GET['terms_s']);
			unset($_GET['page_terms']);
				
			foreach($terms as $term) {
				$current = $is_letter_page && $current_letter == $term->term_id ? 'current' : null;
				$link = '?' . http_build_query(array_merge($_GET, array('letter' => $term->term_id)));
				echo '<a href="' . $link . '" class="letter ' . $current . '">' . $term->name . '</a>';
			}				
		}

		echo '</nav>';

		if ($is_search_page) {
			echo '<p class="search-notice">'. __('Search:', 'terms-dictionary') . ' ' . $search_query .'</p>';
		}

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

				echo str_replace(array('href=""', "href=''"), 'href="."', paginate_links($args));
			echo '</div>';
		}
	} else {
		echo '<h3>' . __('No terms yet ...', 'terms-dictionary') . '</h3>';

		if ($is_search_page) {
			echo '<a href="' . $page_link . '" class="back-to-all">' . __('Back', 'terms-dictionary') . '</a>';
		}
	}

	wp_reset_postdata();
	?>
</div>