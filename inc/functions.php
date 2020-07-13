<?php
class td_init {
	public function detect_shortcode() {
		$bool = true;
		$wp_query = new WP_Query(array(
			'post_type' => 'page'
		));

		foreach ($wp_query->posts as $post){
			if (has_shortcode($post->post_content, 'terms-dictionary')) {
				$bool = false;
				break;	
			}
		}
		
		$this->create_page($bool);
	}

	public function create_page($bool) {
		if ($bool) {
			$page = array(
				'post_type' => 'page',
				'post_status' => 'publish',
				'post_title' => wp_strip_all_tags(__('Terms Dictionary', 'terms-dictionary')),
				'post_content' => '[terms-dictionary]',
			);

			wp_insert_post($page);
		}
	}
}

class td_register_new_post_type {
	function __construct() {
		add_action('init', array($this, 'post_type_register')); 
		add_filter('post_updated_messages', array($this, 'post_messages'));
		add_filter( 'dict-terms-letter_row_actions', array($this, 'remove_view_link_category'), 10, 2);
	}
	
	public function post_type_register() {
		$labels = array(
			'name' =>  __('Dictionary','terms-dictionary'),
			'singular_name' => __('Terms','terms-dictionary'),
			'add_new' => __('Add term','terms-dictionary'),
			'add_new_item' => __('Add new terms','terms-dictionary'),
			'edit_item' => __('Edit term','terms-dictionary'),
			'new_item' => __('New term','terms-dictionary'),
			'all_items' => __('All terms','terms-dictionary'),
			'view_item' => __('View the term online','terms-dictionary'),
			'search_items' => __('Search terms','terms-dictionary'),
			'not_found' =>  __('Terms not found.','terms-dictionary'),
			'not_found_in_trash' => __('The basket does not have the terms.','terms-dictionary'),
			'menu_name' => __('Dictionary','terms-dictionary')	
		);
		$args = array(
			'labels' => $labels,
			'public' => false,
			'show_ui' => true,
			'menu_icon' => 'dashicons-media-spreadsheet',
			'menu_position' => 3,
			'supports' => array( 'title', 'editor', 'thumbnail')
		);
		register_post_type('dict-terms', $args);
		
		register_taxonomy( 'dict-terms-letter', 'dict-terms', 
			array(
				'hierarchical' => true, 
				'label' => __('All letters','terms-dictionary') 
			) 
		);
	}

	public function post_messages( $messages ) {
		global $post, $post_ID;
		$messages['dict-terms'] = array( 
			0 => '', 
			1 => sprintf(__('Terms updated. <a href="%s">View</a>', 'terms-dictionary'), esc_url(get_permalink($post_ID))),
			2 => __('The parameter is updated.', 'terms-dictionary'),
			3 => __('The parameter is remove.', 'terms-dictionary'),
			4 => __('Terms is updated', 'terms-dictionary'),
			5 => isset($_GET['revision'])?sprintf(__('Terms  restored from the editorial: %s', 'terms-dictionary'), wp_post_revision_title((int)$_GET['revision'], false)):false,
			6 => sprintf(__('Term published on the website. <a href="%s">View</a>', 'terms-dictionary'), esc_url(get_permalink($post_ID))),
			7 => __('Terms saved.','terms-dictionary'),
			8 => sprintf(__('Terms submitted for review. <a target="_blank" href="%s">View</a>', 'terms-dictionary'), esc_url(add_query_arg('preview', 'true', get_permalink($post_ID)))),
			9 => sprintf(__('Scheduled for publication: <strong>%1$s</strong>. <a target="_blank" href="%2$s">View</a>', 'terms-dictionary'), date_i18n(__('M j, Y @ G:i'), strtotime($post->post_date)), esc_url(get_permalink($post_ID))),
			10 => sprintf(__('Draft updated terms.<a target="_blank" href="%s">View</a>', 'terms-dictionary'), esc_url(add_query_arg('preview', 'true', get_permalink($post_ID)))),
		);
	 
		return $messages;
	}

	public function remove_view_link_category($actions, $tag) {
		unset($actions['view']);

		return $actions;
	}
}

class td_manage_post {
	function __construct() {
		add_filter('manage_edit-dict-terms_columns', array($this, 'set_columns_posts'));
		add_action('manage_posts_custom_column', array($this, 'letters_on_columns'));
		add_filter('disable_months_dropdown', array($this, 'remove_filter_date'), 10, 2);
		add_action('admin_menu', array($this, 'remove_post_fields'));
		$this->add_new_size_img();
		add_filter('image_size_names_choose', array($this, 'set_new_size_img'));
		add_action('post_updated', array($this, 'set_letter'), 10, 2);
	}
	
	public function set_columns_posts($columns) {
		$columns = array(
			'title' => __('Title', 'terms-dictionary'),
			'letter' => __('Letter', 'terms-dictionary')
		);
		
		return $columns;
	}
	
	public function letters_on_columns($column) {
		global $post;
		
		if ($column == 'letter') {
			$term = get_the_terms($post->ID, 'dict-terms-letter');
			
			echo $term[0]->name;
		}
	}
	
	public function remove_filter_date($bool, $post_type) {
		if ($post_type == 'dict-terms') {
			$bool = true;
		}
		
		return $bool;
	}
	
	public function remove_post_fields() {
		remove_meta_box('slugdiv' , 'dict-terms' , 'normal');
		remove_meta_box('dict-terms-letterdiv' , 'dict-terms' , 'normal');
	}
	
	public function add_new_size_img() {
		add_image_size('dictionary-thumbnail', 150, 150);
	}
	
	public function set_new_size_img($sizes) {
		return array_merge($sizes, array(
			'dictionary-thumbnail' => __('Dictionary Thumbnail', 'terms-dictionary')
		));
	}
	
	public function set_letter($post_ID, $post) {
		if ($post->post_type == 'dict-terms') {
			$one = mb_substr($post->post_title, 0, 1);
			$set = wp_set_object_terms($post_ID, $one, 'dict-terms-letter');
			wp_update_term($set[0], 'dict-terms-letter', array(
				'name' => mb_strtoupper($one),
			));
		}
		
		return;
	}
} 

class td_includes {
	function __construct() {
		add_action('admin_head', array($this, 'add_views_column_css'));
		add_action('wp_enqueue_scripts', array($this, 'add_styles'));
		add_action('plugins_loaded', array($this, 'lang_load_plugin')); 
	}
	
	public function add_views_column_css(){
		if (get_current_screen()->id == 'edit-dict-terms') {
			echo '<style>.column-letter{width:10%;font-weight:bold!important;text-align:center!important;}</style>';
		}
	}
	
	public function add_styles() {
		wp_register_style('td-styles', plugin_dir_url(dirname(__FILE__)). 'css/td-styles.css');
	}
	
	public function lang_load_plugin() { 
		load_plugin_textdomain('terms-dictionary', false, dirname( plugin_basename( __FILE__ ) ) . '/lang/'); 
	}
}

class td_dislpay_front {
	function __construct() {
		add_shortcode('terms-dictionary', array($this, 'shortcode_show_terms'));
	}
	
	public function shortcode_show_terms() {
		wp_enqueue_style('td-styles');
		
		ob_start();
		require_once('frontend.php'); 
		$content = ob_get_clean();
		
		return $content;
	}
}