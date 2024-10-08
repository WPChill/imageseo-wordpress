document.addEventListener('DOMContentLoaded', function() {
	const $ = jQuery

	let totalAlts = 0
	let totalImages = 0

	$('body img').each((i, el) => {
		if ($(el).parents('#wpadminbar').length > 0) {
			return
		}

		totalImages++
		if ($(el).attr('alt') && $(el).attr('alt').length > 0) {
			totalAlts++
		}
	})

	const percent = ( 0 !== totalAlts && 0 !== totalImages ) ?  Math.round((totalAlts * 100) / totalImages) : 0;
	let color = 'red'
	if (percent > 40 && percent < 70) {
		color = 'orange'
	} else if (percent >= 70) {
		color = 'green'
	}

	$('#wp-admin-bar-imageseo-loading-alts').html(
		`${
			i18nImageSeo.alternative_text
		} : ${totalAlts} / ${totalImages} ( <span style='color:${color}'>${percent}% </span>)`
	)
})
