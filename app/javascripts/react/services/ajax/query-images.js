const queryImages = async options => {
	return await fetch(ajaxurl, {
		method: "POST",
		headers: {
			"Content-Type": "application/json"
		},
		body: JSON.stringify({
			action: "imageseo_query_images",
			...options
		})
	});
};

export default queryImages;
