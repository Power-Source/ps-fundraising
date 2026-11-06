jQuery(document).ready( function($) {	
	if(WDF && WDF.hook == 'edit.php' && WDF.typenow == 'funder') {
		$('.wdf_goal_progress').each(function() {
			var progress = $(this);
			var goal = parseInt(progress.attr('goal'), 10);
			var total = parseInt(progress.attr('total'), 10);
			var value = goal > 0 ? Math.min(100, Math.round(total * 100 / goal)) : 0;
			progress.attr({role: 'progressbar', 'aria-valuemin': 0, 'aria-valuemax': 100, 'aria-valuenow': value});
			progress.html('<span class="wdf-progress__value"></span>');
			progress.find('.wdf-progress__value').css('width', value + '%');
		});
	}
	if(WDF && WDF.hook == 'edit.php' && WDF.typenow == 'donation') {
		$('#menu-posts-funder, #menu-posts-funder > a').removeClass('wp-not-current-submenu').addClass('wp-has-current-submenu wp-menu-open');
		$('#menu-posts-funder li a[href="edit.php?post_type=donation&post_status=donation_complete"]').addClass('current').parent().addClass('current');
	}
});