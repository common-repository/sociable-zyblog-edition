<?php
/*
Plugin Name: Sociable Zyblog Edition
Plugin URI: http://www.zyblog.de/wordpress-plugins/sociable-zyblog-edition/
Description: Automatically add links on your posts to popular social bookmarking sites.
Version: 2.0.14
Author: Tim Zylinski
Author URI: http://www.zyblog.de

Copyright 2006 Peter Harkins (ph@malaprop.org)
Copyright 2009-2012 Tim Zylinski ( websitecontact [a t ] zylinski DOT de)

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



require_once('php/sociable-sites.php');
require_once('php/sociable-files.php');

function sociable_init_locale(){
	load_plugin_textdomain('sociable', false, dirname( plugin_basename(__FILE__) ) . '/languages');
}

add_filter('init', 'sociable_init_locale');


function sociable_html($display=Array()) {
	global $sociable_known_sites, $wp_query;
	$active_sites = get_option('sociable_active_sites');

	$html = "";

	$imagepath = get_bloginfo('wpurl') . '/wp-content/plugins/sociable-zyblog-edition/images/';

	// if no sites are specified, display all active
	// have to check $active_sites has content because WP
	// won't save an empty array as an option
	if (empty($display) and $active_sites)
		$display = $active_sites;
	// if no sites are active, display nothing
	if (empty($display))
		return "";

	// Load the post's data
	$blogname = urlencode(get_bloginfo('name'));
	$post = $wp_query->post;
	$permalink = urlencode(get_permalink($post->ID));
	$raw_permalink = get_permalink($post->ID);
	$title = urlencode($post->post_title);
	$title = str_replace('+','%20',$title);
	$raw_title = $post->post_title;
	$rss = urlencode(get_bloginfo('ref_url'));

	$html .= "\n<div class=\"sociable\">\n<span class=\"sociable_tagline\">\n";
	$html .= stripslashes(get_option("sociable_tagline"));
	$html .= "\n\t<span>" . __("These icons link to social bookmarking sites where readers can share and discover new web pages.", 'sociable') . "</span>";
	$html .= "\n</span>\n<ul>\n";
	
	foreach($display as $sitename) {
		// if they specify an unknown or inactive site, ignore it
		if (!in_array($sitename, $active_sites))
			continue;

		$site = $sociable_known_sites[$sitename];
		$html .= "\t<li>";

		$url = $site['url'];
		$url = str_replace('RAW_PERMALINK', $raw_permalink, $url);
		$url = str_replace('PERMALINK', $permalink, $url);
		$url = str_replace('RAW_TITLE', $raw_title, $url);
		$url = str_replace('TITLE', $title, $url);
		$url = str_replace('RSS', $rss, $url);
		$url = str_replace('BLOGNAME', $blogname, $url);

		$html .= "<a href=\"$url\" title=\"$sitename\"";
		if (isset($site['description']) && $site['description'])
                    $html .= " onfocus=\"sociable_description_link(this, '{$site['description']}')\"";
                $html .= " rel=\"nofollow\" target=\"_blank\">";
		$html .= "<img src=\"$imagepath{$site['favicon']}\" title=\"$sitename\" alt=\"$sitename\" class=\"sociable-hovers";
                if (isset($site['class']) && $site['class'])
                    $html .= " sociable_{$site['class']}";
                $html .= "\" />";
		$html .= "</a></li>\n";
	}

	$html .= "</ul>\n</div>\n";

	return $html;
}

// Hook the_content to output html if we should display on any page
$sociable_contitionals = get_option('sociable_conditionals');
if (is_array($sociable_contitionals) and in_array(true, $sociable_contitionals)) {
	add_filter('the_content', 'sociable_display_hook');
	add_filter('the_excerpt', 'sociable_display_hook');

	function sociable_display_hook($content='') {
		$conditionals = get_option('sociable_conditionals');
		if ((is_home()     and $conditionals['is_home']) or
		    (is_single()   and $conditionals['is_single']) or
		    (is_page()     and $conditionals['is_page']) or
		    (is_category() and $conditionals['is_category']) or
		    (is_date()     and $conditionals['is_date']) or
		    (is_search()   and $conditionals['is_search']))
			$content .= sociable_html();

		return $content;
	}
}

// Hook wp_head to add css
add_action('wp_head', 'sociable_wp_head');
function sociable_wp_head() {
	if (in_array('Wists', get_option('sociable_active_sites')))
		echo '<script language="JavaScript" type="text/javascript" src="' . get_bloginfo('wpurl') . '/wp-content/plugins/sociable-zyblog-edition/js/wists.js"></script>';

    echo '<script language="JavaScript" type="text/javascript" src="' . get_bloginfo('wpurl') . '/wp-content/plugins/sociable-zyblog-edition/js/description_selection.js"></script>';
	echo '<link rel="stylesheet" type="text/css" media="screen" href="' . get_bloginfo('wpurl') . '/wp-content/plugins/sociable-zyblog-edition/css/sociable.css" />';

}

// Plugin config/data setup
register_activation_hook(__FILE__, 'sociable_activation_hook');

function sociable_activation_hook() {
	return sociable_restore_config(False);
}

// restore built-in defaults, optionally overwriting existing values
function sociable_restore_config($force=False) {
	// Load defaults, taking care not to smash already-set options
	global $sociable_known_sites;

	// active_sites defaults to the Sociable sponsors
	if ($force or !is_array(get_option('sociable_active_sites')))
		update_option('sociable_active_sites', array(
			'MisterWong',
			'Y!GG',
			'Webnews',
			'Digg',
			'del.icio.us',
			'StumbleUpon',
			'Reddit',
		));

	// tagline defaults to a Hitchiker's Guide to the Galaxy reference
	if ($force or !is_string(get_option('sociable_tagline')))
		update_option('sociable_tagline', "<strong>" . __("Share and Enjoy:", 'sociable') . "</strong>");

	// only display on single posts and pages by default
	if ($force or !is_array(get_option('sociable_conditionals')))
		update_option('sociable_conditionals', array(
			'is_home' => False,
			'is_single' => True,
			'is_page' => True,
			'is_category' => False,
			'is_date' => False,
			'is_search' => False,
		));

}


require_once('php/sociable-admin.php');


?>