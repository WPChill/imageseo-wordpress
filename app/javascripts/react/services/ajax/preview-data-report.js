const previewDataReport = async (
	attachmentId,
	altTemplate,
	excludeFilenames
) => {
	const formData = new FormData();

	formData.append("action", "imageseo_preview_data_report");
	formData.append("attachmentId", attachmentId);
	formData.append("altTemplate", altTemplate);
	formData.append("excludeFilenames", JSON.stringify(excludeFilenames));

	const response = await fetch(ajaxurl, {
		method: "POST",
		body: formData
	});

	return await response.json();
};

export { previewDataReport };
