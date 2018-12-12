document.addEventListener('DOMContentLoaded', function() {
	const $ = jQuery

	if (SEOIMAGE_ATTACHMENTS.length === 0) {
		return
	}

	document
		.querySelector('#seoimage-bulk-reports')
		.addEventListener('click', function(e) {
			e.preventDefault()
			document.querySelector('#seoimage-percent-bulk').style.display =
				'block'
			$(this).prop('disabled', true)
			launchReportImages(0)
		})

	function launchReportImages(current) {
		const total = SEOIMAGE_ATTACHMENTS.length

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
						'#seoimage-percent-bulk .seoimage-percent--item'
					)
					el.style.width = `${percent}%`
					el.textContent = `${percent}%`
					launchReportImages(current)
				}
			},
			{
				action: 'seoimage_report_attachment',
				attachment_id: SEOIMAGE_ATTACHMENTS[current]
			}
		)
	}

	function finishReportImages() {
		$('#seoimage-bulk-reports').prop('disabled', false)
	}
})
