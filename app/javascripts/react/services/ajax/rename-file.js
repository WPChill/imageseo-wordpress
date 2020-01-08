const previewFilename = async (attachmentId, excludeFilenames) => {
	const formData = new FormData();

	formData.append("action", "imageseo_preview_optimize_filename");
	formData.append("attachmentId", attachmentId);
	formData.append("excludeFilenames", JSON.stringify(excludeFilenames));

	const response = await fetch(ajaxurl, {
		method: "POST",
		body: formData
	});

	return await response.json();
};

const renameFilename = async (attachmentId, filename) => {
	const formData = new FormData();

	formData.append("action", "imageseo_optimize_filename");
	formData.append("attachmentId", attachmentId);
	formData.append("filename", filename);

	const response = await fetch(ajaxurl, {
		method: "POST",
		body: formData
	});

	return await response.json();
};

export { previewFilename, renameFilename };
