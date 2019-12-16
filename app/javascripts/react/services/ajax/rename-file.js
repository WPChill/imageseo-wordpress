const renameFile = async (attachmentId, nameFile) => {
	return await fetch(ajaxurl, {
		method: "POST",
		headers: {
			"Content-Type": "application/json"
		},
		body: JSON.stringify({
			action: "imageseo_rename_file",
			attachmentId,
			nameFile
		})
	});
};

export default renameFile;
