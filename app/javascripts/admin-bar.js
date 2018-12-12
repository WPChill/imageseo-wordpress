document.addEventListener('DOMContentLoaded', function() {
	const $ = jQuery

	let totalAlts = 0
	let totalImages = $('img').length

	$('body img').each((i, el) => {
		if ($(el).attr('alt') && $(el).attr('alt').length > 0) {
			totalAlts++
		}
	})

	const percent = Math.round((totalAlts * 100) / totalImages)
	let color = 'red'
	if (percent > 40 && percent < 70) {
		color = 'orange'
	} else if (percent >= 70) {
		color = 'green'
	}

	$('#wp-admin-bar-seoimage-loading-alts').html(
		`${
			i18nSeoImage.alternative_text
		} : ${totalAlts} / ${totalImages} ( <span style='color:${color}'>${percent}% </span>)`
	)
})
