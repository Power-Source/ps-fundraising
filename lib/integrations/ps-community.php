<?php

if ( !defined('ABSPATH') ) {
	exit;
}

function wdf_ps_community_settings() {
	$settings = get_option('wdf_settings');
	$defaults = array(
		'enabled' => '1',
		'show_campaigns' => '1',
		'show_supports' => '1',
		'allow_activity' => '1',
	);

	return wp_parse_args(
		isset($settings['ps_community']) && is_array($settings['ps_community']) ? $settings['ps_community'] : array(),
		$defaults
	);
}

function wdf_ps_community_is_available() {
	$settings = wdf_ps_community_settings();

	return $settings['enabled'] === '1' && function_exists('cpc_get_profile_tabs') && function_exists('cpc_api_insert_activity_post');
}

function wdf_ps_community_can_publish_activity() {
	$settings = wdf_ps_community_settings();

	return wdf_ps_community_is_available() && is_user_logged_in() && $settings['allow_activity'] === '1';
}

function wdf_ps_community_add_profile_tabs($tabs, $user_id, $viewer_id) {
	if ( !wdf_ps_community_is_available() || !is_user_logged_in() ) {
		return $tabs;
	}

	$settings = wdf_ps_community_settings();
	$user_id = absint($user_id);
	$viewer_id = absint($viewer_id);

	if ( $settings['show_campaigns'] === '1' ) {
		$campaigns = get_posts(array(
			'author' => $user_id,
			'fields' => 'ids',
			'numberposts' => 1,
			'post_status' => 'publish',
			'post_type' => 'funder',
		));

		if ( $campaigns ) {
			$tabs['fundraising'] = array(
				'label' => __('Spendenaktionen', 'wdf'),
				'icon' => 'heart',
				'priority' => 30,
			);
		}
	}

	if ( $settings['show_supports'] === '1' && $user_id === $viewer_id ) {
		$tabs['fundraising-supports'] = array(
			'label' => __('Meine Unterstützungen', 'wdf'),
			'icon' => 'money-alt',
			'priority' => 31,
		);
	}

	return $tabs;
}
add_filter('cpc_profile_tabs', 'wdf_ps_community_add_profile_tabs', 20, 3);

function wdf_ps_community_render_profile_tab($html, $active_tab, $user_id, $shortcode_atts) {
	if ( !wdf_ps_community_is_available() || !is_user_logged_in() ) {
		return $html;
	}

	$settings = wdf_ps_community_settings();
	$user_id = absint($user_id);

	if ( $active_tab === 'fundraising' && $settings['show_campaigns'] === '1' ) {
		return wdf_ps_community_render_campaigns($user_id);
	}

	if ( $active_tab === 'fundraising-supports' && $settings['show_supports'] === '1' && $user_id === get_current_user_id() ) {
		return wdf_ps_community_render_supports($user_id);
	}

	return $html;
}
add_filter('cpc_profile_tab_content', 'wdf_ps_community_render_profile_tab', 20, 4);

function wdf_ps_community_render_campaigns($user_id) {
	$campaigns = get_posts(array(
		'author' => $user_id,
		'numberposts' => -1,
		'orderby' => 'date',
		'order' => 'DESC',
		'post_status' => 'publish',
		'post_type' => 'funder',
	));
	$html = '<div class="wdf_ps_community_campaigns">';

	if ( !$campaigns ) {
		return $html . '<p>' . esc_html__('Derzeit sind keine Spendenaktionen vorhanden.', 'wdf') . '</p></div>';
	}

	foreach ( $campaigns as $campaign ) {
		$html .= '<article class="wdf_ps_community_campaign">';
		$html .= '<h3><a href="' . esc_url(get_permalink($campaign)) . '">' . esc_html(get_the_title($campaign)) . '</a></h3>';
		$html .= '<p>' . esc_html(wp_trim_words($campaign->post_excerpt ? $campaign->post_excerpt : $campaign->post_content, 28)) . '</p>';
		$html .= '<p class="wdf_ps_community_campaign_total">' . esc_html(wdf_amount_raised(false, $campaign->ID));
		if ( wdf_has_goal($campaign->ID) ) {
			$html .= ' / ' . esc_html(wdf_goal(false, $campaign->ID));
		}
		$html .= '</p></article>';
	}

	return $html . '</div>';
}

function wdf_ps_community_render_supports($user_id) {
	$supports = get_posts(array(
		'meta_key' => 'wdf_ps_community_supporter_id',
		'meta_value' => $user_id,
		'numberposts' => -1,
		'orderby' => 'date',
		'order' => 'DESC',
		'post_status' => array('wdf_complete', 'wdf_approved', 'wdf_pending'),
		'post_type' => 'donation',
	));
	$html = '<div class="wdf_ps_community_supports">';

	if ( !$supports ) {
		return $html . '<p>' . esc_html__('Du hast bisher keine Unterstützungen abgegeben.', 'wdf') . '</p></div>';
	}

	foreach ( $supports as $support ) {
		$campaign = get_post($support->post_parent);
		$transaction = get_post_meta($support->ID, 'wdf_transaction', true);
		$html .= '<article class="wdf_ps_community_support">';
		$html .= '<h3>' . esc_html($campaign ? get_the_title($campaign) : __('Spendenaktion', 'wdf')) . '</h3>';
		$html .= '<p>' . esc_html(get_the_date('', $support));
		if ( is_array($transaction) && isset($transaction['gross']) ) {
			global $wdf;
			$html .= ' &middot; ' . esc_html($wdf->format_currency('', $transaction['gross']));
		}
		$html .= '</p></article>';
	}

	return $html . '</div>';
}

function wdf_ps_community_maybe_record_activity($pledge, $funder_id) {
	if ( !wdf_ps_community_can_publish_activity() || !$pledge || get_post_status($pledge) !== 'wdf_complete' ) {
		return;
	}

	$user_id = get_current_user_id();
	if ( $user_id !== absint(get_post_meta($pledge->ID, 'wdf_ps_community_supporter_id', true))
		|| get_post_meta($pledge->ID, 'wdf_ps_community_activity_requested', true) !== '1'
		|| get_post_meta($pledge->ID, 'wdf_ps_community_activity_id', true) ) {
		return;
	}

	$campaign_title = get_the_title($funder_id);
	$activity = sprintf(__('hat die Spendenaktion „%s“ unterstützt.', 'wdf'), $campaign_title);
	$activity_id = cpc_api_insert_activity_post(
		$activity,
		$user_id,
		$user_id,
		array(
			'cpc_activity_post_author' => $user_id,
			'cpc_activity_post' => $activity,
		),
		array()
	);

	if ( $activity_id ) {
		update_post_meta($pledge->ID, 'wdf_ps_community_activity_id', absint($activity_id));
	}
}
