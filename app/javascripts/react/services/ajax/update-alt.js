const previewAlt = async (attachmentId, altTemplate) => {
	const formData = new FormData();

	formData.append("action", "imageseo_preview_optimize_alt");
	formData.append("attachmentId", attachmentId);
	formData.append("altTemplate", altTemplate);

	const response = await fetch(ajaxurl, {
		method: "POST",
		body: formData
	});

	return await response.json();
};

const updateAlt = async (attachmentId, alt) => {
	const formData = new FormData();

	formData.append("action", "imageseo_optimize_alt");
	formData.append("attachmentId", attachmentId);
	formData.append("alt", alt);

	const response = await fetch(ajaxurl, {
		method: "POST",
		body: formData
	});

	return await response.json();
};

export { updateAlt, previewAlt };
