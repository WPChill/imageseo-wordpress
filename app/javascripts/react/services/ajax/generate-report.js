const generateReport = async attachmentId => {
	return await fetch(ajaxurl, {
		method: "POST",
		headers: {
			"Content-Type": "application/json"
		},
		body: JSON.stringify({
			action: "imageseo_generate_report",
			attachmentId
		})
	});
};

export default generateReport;
