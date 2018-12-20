jQuery(document).ready(function($) {
	function imageseo_alt_field(attachment) {
		const alt_text = $('#imageseo-alt-' + attachment).val()
		$.post(ajaxurl, {
			action: 'imageseo_media_alt_update',
			post_id: attachment,
			alt: alt_text,
			success: function() {
				setTimeout(() => {
					$(
						'#wrapper-imageseo-' + attachment + ' .imageseo-loading'
					).hide()
					$('#wrapper-imageseo-' + attachment + ' button span').show()
				}, 500)
			}
		})
	}
	$(this)
		.on('keydown', 'input.imageseo-alt-ajax', function(event) {
			if (event.keyCode === 13) {
				$(this).blur()
				return false
			}
		})
		.on('blur', 'input.imageseo-alt-ajax', function() {
			const id = $(this).data('id')
			$('#wrapper-imageseo-' + id + ' button span').hide()
			$('#wrapper-imageseo-' + id + ' .imageseo-loading').show()
			imageseo_alt_field(id)
			return false
		})
	$('.wrapper-imageseo-input-alt button').on('click', function(e) {
		e.preventDefault()
		const id = $(this).data('id')
		$('#wrapper-imageseo-' + id + ' button span').hide()
		$('#wrapper-imageseo-' + id + ' .imageseo-loading').show()
		imageseo_alt_field(id)
	})
})
