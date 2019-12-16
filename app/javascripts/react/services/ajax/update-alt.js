const updateAlt = async (attachmentId, alt) => {
	return await fetch(ajaxurl, {
		method: "POST",
		headers: {
			"Content-Type": "application/json"
		},
		body: JSON.stringify({
			action: "imageseo_update_alt",
			attachmentId,
			alt
		})
	});
};

export default updateAlt;
