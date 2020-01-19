const saveSocialMediaSettings = async options => {
	const formData = new FormData();

	formData.append("action", "imageseo_social_media_settings_save");

	Object.keys(options).map(key => {
		formData.append(key, options[key]);
	});

	const response = await fetch(ajaxurl, {
		method: "POST",
		body: formData
	});

	return await response.json();
};

export { saveSocialMediaSettings };
