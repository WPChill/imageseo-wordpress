jQuery(document).ready(function($) {
	function seoimage_alt_field(attachment) {
		const alt_text = $('#seoimage-alt-' + attachment).val()
		$.post(ajaxurl, {
			action: 'seoimage_media_alt_update',
			post_id: attachment,
			alt: alt_text
		})
	}
	$(this)
		.on('keydown', 'input.seoimage-alt-ajax', function(event) {
			if (event.keyCode === 13) {
				$(this).blur()
				return false
			}
		})
		.on('blur', 'input.seoimage-alt-ajax', function() {
			seoimage_alt_field($(this).data('id'))
			return false
		})
})
