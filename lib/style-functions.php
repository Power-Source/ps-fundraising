<?php

if(!function_exists('wdf_normalize_style')) {
	function wdf_normalize_style($style) {
		$legacy_styles = array('wdf-basic', 'wdf-dark', 'wdf-fresh', 'wdf-minimal', 'wdf-note', 'wdf-material');

		if(empty($style) || in_array($style, $legacy_styles, true)) {
			return 'wdf-modern';
		}

		return sanitize_html_class($style);
	}
}
