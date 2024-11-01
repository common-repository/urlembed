<?php
/*
Plugin Name: UrlEmbed
Plugin URI: http://wordpress.org/plugins/urlembed/
Description: UrlEmbed for WordPress. Embed everything link with responsive widgets.
Author: UrlEmbed
Author URI: https://urlembed.com/wordpress
Version: 1
 */

function urlembed_init() {
	global $wp;

	$wp->public_query_vars = array_diff($wp->public_query_vars, array(
		'embed',
	));

	remove_action('rest_api_init', 'wp_oembed_register_route');

	add_filter('embed_oembed_discover', '__return_false');

	remove_filter('oembed_dataparse', 'wp_filter_oembed_result', 10);

	remove_action('wp_head', 'wp_oembed_add_discovery_links');

	remove_action('wp_head', 'wp_oembed_add_host_js');
	add_filter('tiny_mce_plugins', 'urlembed_tiny_mce_plugin');

	add_filter('rewrite_rules_array', 'urlembed_rewrites');

	remove_filter('pre_oembed_result', 'wp_filter_pre_oembed_result', 10);
}

add_action('init', 'urlembed_init', 9999);

function urlembed_tiny_mce_plugin($plugins) {
	return array_diff($plugins, array('wpembed'));
}

function urlembed_rewrites($rules) {
	foreach ($rules as $rule => $rewrite) {
		if (false !== strpos($rewrite, 'embed=true')) {
			unset($rules[$rule]);
		}
	}

	return $rules;
}

function urlembed_remove_rewrite_rules() {
	add_filter('rewrite_rules_array', 'urlembed_rewrites');
	flush_rewrite_rules();
}

register_activation_hook(__FILE__, 'urlembed_remove_rewrite_rules');

function urlembed_flush_rewrite_rules() {
	remove_filter('rewrite_rules_array', 'urlembed_rewrites');
	flush_rewrite_rules();
}

register_deactivation_hook(__FILE__, 'urlembed_flush_rewrite_rules');

remove_filter('the_content', array($GLOBALS['wp_embed'], 'autoembed'), 8);

function urlembed_enqueue_plugin_scripts($plugin_array) {
	$plugin_array["urlembed_bb"] = plugins_url("index.js", __FILE__);
	return $plugin_array;
}

add_filter("mce_external_plugins", "urlembed_enqueue_plugin_scripts");

function urlembed_register_buttons_editor($buttons) {
	array_push($buttons, "urlembed_bb");
	return $buttons;
}

add_filter("mce_buttons", "urlembed_register_buttons_editor");

function urlembed_content_filter($content) {
	$content = $content . '<script src="//urlembed.com/static/js/script.js"></script>';

	$content = preg_replace(
		'#<p><a href=\"((http://|https://).*?)\">(.*?)</a></p>#is',
		'<p><a href="$1" class="urlembed' . $slim . '">$2</a></p>',
		$content
	);
	$content = preg_replace(
		"#(<p>|\n|\[urlembed\])((http://|https://).*?)(</p>|\n|\[\/urlembed\])#is",
		'<p><a href="$2" class="urlembed' . $slim . '">$2</a></p>',
		$content
	);

	$slim = '';
	if (false) {
		$slim = ' slim" style="max-height: 160px;';
	}

	return $content;
}

add_filter('the_content', 'urlembed_content_filter');

function urlembed_shortcode_func($atts, $content = "") {
	return "content = $content";
}
add_shortcode('urlembed', 'urlembed_shortcode_func');
