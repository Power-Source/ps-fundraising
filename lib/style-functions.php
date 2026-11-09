<?php

if(!function_exists('wdf_show_stat')) {
	// Shortcode-/Widget-Attribut (yes/no) hat Vorrang vor der globalen Einstellung; Standard ist "yes".
	function wdf_show_stat($key, $args = array(), $settings = null) {
		if(isset($args[$key]) && in_array(strtolower($args[$key]), array('yes', 'no'), true)) {
			return strtolower($args[$key]) === 'yes';
		}
		if(null === $settings) {
			$settings = get_option('wdf_settings');
		}
		return !isset($settings[$key]) || 'no' !== $settings[$key];
	}
}

if(!function_exists('wdf_normalize_style')) {
	function wdf_normalize_style($style) {
		$legacy_styles = array('wdf-basic', 'wdf-dark', 'wdf-fresh', 'wdf-minimal', 'wdf-note', 'wdf-material');

		if(empty($style) || in_array($style, $legacy_styles, true)) {
			return 'wdf-modern';
		}

		return sanitize_html_class($style);
	}
}
