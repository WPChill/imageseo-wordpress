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
			$('span', $(this)).hide()
			$('.imageseo-loading', $(this)).show()
			$('#imageseo-total-already-reports span').text(0)
			launchReportImages(0)
		})

	const getPercent = (current, total) => {
		if (current > total) {
			return 100
		} else {
			return Math.round((current * 100) / total)
		}
	}

	const setPercentLoader = (current, total) => {
		const percent = getPercent(current, total)
		const el = document.querySelector(
			'#imageseo-percent-bulk .imageseo-percent--item'
		)
		el.style.width = `${percent}%`
		el.textContent = `${percent}% (${current}/${total})`
	}

	const ReportItem = ({
		dashicons,
		src,
		alt_generate,
		file = ''
	}) => `<div class="imageseo-reports-body-item">
		<div class="imageseo-reports--status"><span class="dashicons dashicons-${dashicons}"></span></div>
		<div class="imageseo-reports--image"><div class="imageseo-reports--image-itm" style="background-image:url('${file}')"></div></div>
		<div class="imageseo-reports--src">${src}</div>
		<div class="imageseo-reports--alt">${alt_generate}</div>
	</div>`

	function launchReportImages(current) {
		const total = IMAGESEO_ATTACHMENTS.length
		if (current > total) {
			finishReportImages()
			return
		}

		if (typeof IMAGESEO_ATTACHMENTS[current] === 'undefined') {
			current++
			launchReportImages(current)
			return
		}

		$.post(
			{
				url: ajaxurl,
				success: function(res) {
					let txt = `Attachment ID : ${IMAGESEO_ATTACHMENTS[current]}`
					if (res.data && res.data.src) {
						txt = res.data.src
					}
					current++
					setPercentLoader(current, total)
					if (res.success) {
						$(
							'#imageseo-reports-js .imageseo-reports-body'
						).prepend(
							ReportItem({
								...res.data,
								src: txt,
								dashicons: 'yes'
							})
						)
					} else {
						$(
							'#imageseo-reports-js .imageseo-reports-body'
						).prepend(
							ReportItem({
								...res.data,
								src: txt,
								dashicons: 'no'
							})
						)
					}
					$('#imageseo-total-already-reports span').text(current)
					launchReportImages(current)
				},
				error: function(res) {
					$('#imageseo-reports-js .imageseo-reports-body').prepend(
						ReportItem({
							src: `Attachment ID: ${
								IMAGESEO_ATTACHMENTS[current]
							}`,
							alt_generate: '',
							dashicons: 'no'
						})
					)
					current++
					setPercentLoader(current, total)
					$('#imageseo-total-already-reports span').text(current)
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
		$('#imageseo-bulk-reports .imageseo-loading').hide()
		$('#imageseo-bulk-reports span').show()
	}
})
