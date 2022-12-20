const saveSocialMediaSettings = async options => {
	const formData = new FormData();

	formData.append("action", "imageseo_social_media_settings_save");
	formData.append(
		"_wpnonce",
		document
			.querySelector("#_nonce_imageseo_social_media_settings_save")
			.getAttribute("value")
	);

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
