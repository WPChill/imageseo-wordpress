document.addEventListener('DOMContentLoaded', function() {
	const $ = jQuery

	if (IMAGESEO_ATTACHMENTS.length === 0) {
		return
	}

	document
		.querySelector('#imageseo-bulk-reports')
		.addEventListener('click', function(e) {
			e.preventDefault()
			document.querySelector('#imageseo-percent-bulk').style.display =
				'block'
			$(this).prop('disabled', true)
			launchReportImages(0)
		})

	function launchReportImages(current) {
		const total = IMAGESEO_ATTACHMENTS.length

		if (current > total) {
			finishReportImages()
			return
		}

		$.post(
			{
				url: ajaxurl,
				success: function(data) {
					current++
					let percent
					if (current > total) {
						percent = 100
					} else {
						percent = Math.round((current * 100) / total)
					}

					const el = document.querySelector(
						'#imageseo-percent-bulk .imageseo-percent--item'
					)
					el.style.width = `${percent}%`
					el.textContent = `${percent}%`
					launchReportImages(current)
				}
			},
			{
				action: 'imageseo_report_attachment',
				attachment_id: IMAGESEO_ATTACHMENTS[current]
			}
		)
	}

	function finishReportImages() {
		$('#imageseo-bulk-reports').prop('disabled', false)
	}
})
