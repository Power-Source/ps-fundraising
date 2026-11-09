function wdf_inject_shortcode () {
	
	var iFrame = jQuery('#TB_iframeContent').contents();
	var cont = iFrame.find('.wdf_media_cont.is-active:first');
	var type = cont.attr('id');
	var form = cont.serializeArray();

	switch(type) {
		case 'media_fundraising' :
			var funder_select = iFrame.find('#wdf_funder_select');
			
			var shortcode = '[fundraiser_panel';
		
			jQuery.each(form, function(i,e) { 
				if((e.name == 'show_backers' || e.name == 'show_raised') && e.value == '')
					return;
				shortcode = shortcode + ' '+e.name+'="'+e.value+'"';
			});
			
			shortcode = shortcode + ']';
			if(jQuery('#wp-content-wrap').hasClass('tmce-active')) {
				window.parent.tinyMCE.execCommand("mceInsertContent", true, shortcode);
				window.parent.tb_remove();
			} else {
				window.parent.edInsertContent('',shortcode);
				window.parent.tb_remove();
			}
		break;

		case 'media_pledges' :
			var funder_select = iFrame.find('#wdf_funder_select');
			
			var shortcode = '[pledges_panel';
		
			jQuery.each(form, function(i,e) { 
				shortcode = shortcode + ' '+e.name+'="'+e.value+'"';
			});
			
			shortcode = shortcode + ']';
			if(jQuery('#wp-content-wrap').hasClass('tmce-active')) {
				window.parent.tinyMCE.execCommand("mceInsertContent", true, shortcode);
				window.parent.tb_remove();
			} else {
				window.parent.edInsertContent('',shortcode);
				window.parent.tb_remove();
			}
		break;
		
		case 'media_donate_button' :
			var shortcode = '[donate_button';
			jQuery.each(form, function(i,e) {
				if(e.value != '')
					shortcode = shortcode + ' '+e.name+'="'+e.value+'"';
			});
			shortcode = shortcode + ']';
			if(jQuery('#wp-content-wrap').hasClass('tmce-active')) {
				window.parent.tinyMCE.execCommand("mceInsertContent", true, shortcode);
				window.parent.tb_remove();
			} else {
				window.parent.edInsertContent('',shortcode);
				window.parent.tb_remove();
			}
		break;
		
		case 'media_progress_bar' :
			var shortcode = '[progress_bar';
			jQuery.each(form, function(i,e) {
				if(parseInt(e.value) !== 0)
					shortcode = shortcode + ' '+e.name+'="'+e.value+'"';
			});
			shortcode = shortcode + ']';
			if(jQuery('#wp-content-wrap').hasClass('tmce-active')) {
				window.parent.tinyMCE.execCommand("mceInsertContent", true, shortcode);
				window.parent.tb_remove();
			} else {
				window.parent.edInsertContent('',shortcode);
				window.parent.tb_remove();
			}
		break;		
		
	}
}

function wdf_show_shortcode_loading() {
	var loading = jQuery('#wdf_shortcode_loading');

	if (!loading.length) {
		loading = jQuery('<div id="wdf_shortcode_loading" role="status">Lade Shortcode-Optionen ...</div>');
		loading.css({
			'background': '#fff',
			'border-radius': '3px',
			'box-shadow': '0 1px 5px rgba(0, 0, 0, .35)',
			'font-size': '14px',
			'left': '50%',
			'padding': '14px 20px',
			'position': 'fixed',
			'top': '50%',
			'transform': 'translate(-50%, -50%)',
			'z-index': 100100
		});
		jQuery('body').append(loading);
	}

	var attempts = 0;
	function watchIframe() {
		var iframe = jQuery('#TB_iframeContent');

		if (iframe.length) {
			iframe.one('load', function() {
				loading.remove();
			});
		} else if (attempts < 10) {
			attempts++;
			window.setTimeout(watchIframe, 50);
		}
	}

	window.setTimeout(watchIframe, 0);
}

jQuery(function($) {
	$(document).on('click', '#add_wdf', wdf_show_shortcode_loading);
});

function wdf_input_switch(elm) {
	elm = jQuery(elm);
	var iFrame = jQuery('#TB_iframeContent').contents();
	var rel = elm.attr('rel');
	var val = elm.val();
	
	/*$('select.wdf_toggle').on('change', function(e) {
		var rel = $(this).attr('rel');
		var val = $(this).val();
		//alert(rel + val);
		
		if(rel == 'wdf_panel_single' && val == '1') {
			var elm = $('*[rel="'+rel+'"]').not(this);
			elm.show();
		} else {
			var elm = $('*[rel="'+rel+'"]').not(this);
			elm.hide();
		}
	});*/
}