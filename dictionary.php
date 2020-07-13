<?php
/*
	Plugin Name: Terms Dictionary
	Description: Plugin to create a small dictionary with automatic grouping by letters.
	Version: 1.3
	Author: Somonator
	Author URI: mailto:somonator@gmail.com
	Text Domain: terms-dictionary
	Domain Path: /lang
*/

/*  
	Copyright 2016  Alexsandr  (email: somonator@gmail.com)

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

require_once('inc/functions.php');

/**
* Functions on init plugin.
*/
$init = new td_init();
register_activation_hook(__FILE__, array($init, 'detect_shortcode')); 

/**
* Add new post type.
*/
new td_register_new_post_type();

/**
* Expanding post for custm post type.
*/
new td_manage_post();

/**
* Includes scripts and styles plugin.
*/
new td_includes();

/**
* Create shortcode for diplay terms dictionary.
*/
new td_dislpay_front();