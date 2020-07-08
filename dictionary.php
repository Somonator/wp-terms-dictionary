<?php
/*
Plugin Name: Terms Dictionary
Plugin URI: none
Description: Plugin to create a Dictionary.
Version: 1.0
Author: Somonator
Author URI: none
*/

/*  Copyright 2016  Alexsandr  (email: somonator@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/



add_action( 'init', 'dict_terms_new_post_type_register' ); 
function dict_terms_new_post_type_register() {
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
		'public' => true, 
		'show_ui' => true,
		'has_archive' => true, 
		'menu_icon' => 'dashicons-media-spreadsheet',
		'menu_position' => 3,
		'supports' => array( 'title',  'editor',  'thumbnail')
	);
	register_post_type('dict', $args);
    register_taxonomy( 'dict-letter', 'dict', 
						array( 'hierarchical' => true, 
						'label' => __('All letters','terms-dictionary') 
						) 
					);
}
add_filter( 'post_updated_messages', 'post_messages_dict' );
function post_messages_dict( $messages ) {
	global $post, $post_ID;
	$messages['dict'] = array( 
		0 => '', 
		1 => sprintf(__('Terms updated. <a href="%s">View</a>','terms-dictionary'), esc_url( get_permalink($post_ID) ) ),
		2 => __('The parameter is updated.','terms-dictionary'),
		3 => __('The parameter is remove.','terms-dictionary'),
		4 => __('Terms is updated','terms-dictionary'),
		5 => isset($_GET['revision']) ? sprintf( __('Terms  restored from the editorial: %s','terms-dictionary'), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
		6 => sprintf( __('Term published on the website. <a href="%s">View</a>','terms-dictionary'), esc_url( get_permalink($post_ID) ) ),
		7 => __('Terms saved.','terms-dictionary'),
		8 => sprintf( __('Terms submitted for review. <a target="_blank" href="%s">View</a>','terms-dictionary'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
		9 => sprintf( __('Scheduled for publication: <strong>%1$s</strong>. <a target="_blank" href="%2$s">View</a>','terms-dictionary'), date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ), esc_url( get_permalink($post_ID) ) ),
		10 => sprintf( __('Draft updated terms.<a target="_blank" href="%s">View</a>','terms-dictionary'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
	);
 
	return $messages;
}

add_filter( 'template_include', 'include_template_file_sigle_and_archive_dictionary', 1 );
function include_template_file_sigle_and_archive_dictionary( $template_path ) {
    if ( get_post_type() == 'dict' ) {
        if ( is_single() ) {
            if ( $theme_file = locate_template( array ( 'dict-single.php' ) ) ) {
                $template_path = $theme_file;
            } else {
                $template_path = plugin_dir_path(__FILE__) . '/post/dict-single.php';
            }
        }
		       if ( is_archive() ) {
            if ( $theme_file = locate_template( array ( 'archive-dict.php' ) ) ) {
                $template_path = $theme_file;
            } else {
                $template_path = plugin_dir_path(__FILE__) . '/post/archive-dict.php';
            }
        }
    }
    return $template_path;
}

function add_styls_dictionary_wp() {
	wp_enqueue_style( 'style-dict', plugin_dir_url(__FILE__ ). 'css/style.css' );
}
add_action( 'wp_enqueue_scripts', 'add_styls_dictionary_wp' );

add_action( 'plugins_loaded', 'lang_load_plugin_terms_dictionary' ); 

function lang_load_plugin_terms_dictionary() { 
load_plugin_textdomain( 'terms-dictionary', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' ); 
}
?>