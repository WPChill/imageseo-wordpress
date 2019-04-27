document.addEventListener('DOMContentLoaded', function() {
	const $ = jQuery
	let _execution = false
	if (IMAGESEO_ATTACHMENTS.length === 0) {
		return
	}

	document
		.querySelector('#imageseo-bulk-reports--stop')
		.addEventListener('click', function(e) {
			e.preventDefault()
			$(this).html('Current shutdown ...')
			_execution = false
		})

	document
		.querySelector('#imageseo-bulk-reports--start')
		.addEventListener('click', function(e) {
			e.preventDefault()
			document.querySelector('#imageseo-percent-bulk').style.display =
				'block'

			$(this).prop('disabled', true)
			$('#option-update-alt').prop('disabled', true)
			$('#option-update-alt-not-empty').prop('disabled', true)
			$('#option-rename-file').prop('disabled', true)
			$('span', $(this)).hide()
			$('.imageseo-loading', $(this)).show()
			$('#imageseo-reports-js .imageseo-reports-body').html('')
			$('#imageseo-bulk-reports--stop').prop('disabled', false)

			const val = $("input[name='method']:checked").val()
			let total, start, add

			if (val === 'new') {
				total = IMAGESEO_ATTACHMENTS.length
				start = 0
				add = 0
			} else {
				total =
					IMAGESEO_ATTACHMENTS.length - (IMAGESEO_CURRENT_PROCESS + 1)
				start = IMAGESEO_CURRENT_PROCESS
				add = 1
			}

			_execution = true
			launchReportImages(start, 0, total, add)
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
		current_name_file,
		name_file,
		current_alt,
		alt_generate,
		file = ''
	}) => `<div class="imageseo-reports-body-item">
		<div class="imageseo-reports--status"><span class="dashicons dashicons-${dashicons}"></span></div>
		<div class="imageseo-reports--image"><div class="imageseo-reports--image-itm" style="background-image:url('${file}')"></div></div>
		<div class="imageseo-reports--src">Current name file : ${current_name_file}<hr /> <strong>ImageSEO AI suggestion</strong> : ${name_file}</div>
		<div class="imageseo-reports--alt">Current alt : ${current_alt} <hr />  <strong>ImageSEO AI suggestion</strong> : ${alt_generate}</div>
	</div>`

	function launchReportImages(start, current, total, add = 0) {
		const index = start + current + add

		if (current > total || !_execution) {
			finishReportImages()
			return
		}

		if (typeof IMAGESEO_ATTACHMENTS[index] === 'undefined') {
			current++
			launchReportImages(start, current, total, add)
			return
		}
		const updateAlt = $('#option-update-alt').is(':checked')
		const updateAltNotEmpty = $('#option-update-alt-not-empty').is(
			':checked'
		)
		const renameFile = $('#option-rename-file').is(':checked')

		const _errorReportAttachment = res => {
			$('#imageseo-reports-js .imageseo-reports-body').prepend(
				ReportItem({
					src: `Attachment ID: ${IMAGESEO_ATTACHMENTS[index]}`,
					name_file: '',
					alt_generate: '',
					dashicons: 'no'
				})
			)
			current++
			setPercentLoader(current, total)
			launchReportImages(start, current, total, add)
		}

		const _successReportAttachment = res => {
			IMAGESEO_CURRENT_PROCESS = current + 1
			let txt = `Attachment ID : ${IMAGESEO_ATTACHMENTS[index]}`
			if (res.data && res.data.src) {
				txt = res.data.src
			}
			current++
			setPercentLoader(current, total)
			console.log(res.data)
			if (res.success) {
				$('#imageseo-reports-js .imageseo-reports-body').prepend(
					ReportItem({
						...res.data,
						src: txt,
						dashicons: 'yes'
					})
				)
			} else {
				$('#imageseo-reports-js .imageseo-reports-body').prepend(
					ReportItem({
						...res.data,
						src: txt,
						dashicons: 'no'
					})
				)
			}
			launchReportImages(start, current, total, add)
		}

		$.post(
			{
				url: ajaxurl,
				success: _successReportAttachment,
				error: _errorReportAttachment
			},
			{
				action: 'imageseo_report_attachment',
				update_alt: updateAlt,
				update_alt_not_empty: updateAltNotEmpty,
				rename_file: renameFile,
				total: IMAGESEO_ATTACHMENTS.length,
				current: index,
				attachment_id: IMAGESEO_ATTACHMENTS[index]
			}
		)
	}

	function finishReportImages() {
		_execution = false
		$('#imageseo-bulk-reports--start').prop('disabled', false)
		$('#imageseo-bulk-reports--start .imageseo-loading').hide()
		$('#imageseo-bulk-reports--start span').show()

		$('#option-update-alt').prop('disabled', false)
		$('#option-update-alt-not-empty').prop('disabled', false)
		$('#option-rename-file').prop('disabled', false)
		$('#imageseo-bulk-reports--stop')
			.prop('disabled', true)
			.html('Stop')
	}
})
