const generateReport = async (attachmentId, language) => {
	const formData = new FormData();

	formData.append("action", "imageseo_generate_report");
	formData.append("attachmentId", attachmentId);
	formData.append("language", language);

	const response = await fetch(ajaxurl, {
		method: "POST",
		body: formData
	});

	return await response.json();
};

export default generateReport;
