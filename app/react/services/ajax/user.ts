export const register = async (data) => {
	const formData = new FormData();

	formData.append("action", "imageseo_register");
	formData.append(
		"_wpnonce",
		document
			.querySelector("#_nonce_imageseo_register")
			.getAttribute("value")
	);
	formData.append("firstname", data.firstname);
	formData.append("lastname", data.lastname);
	formData.append("email", data.email);
	formData.append("password", data.password);
	if (data.newsletters) {
		formData.append("newsletters", data.newsletters);
	}

	//@ts-ignore
	const response = await fetch(IMAGESEO_DATA.ADMIN_AJAX, {
		method: "POST",
		body: formData,
	});

	return await response.json();
};

export const validateApiKey = async (apiKey) => {
	const formData = new FormData();

	formData.append("action", "imageseo_valid_api_key");
	formData.append(
		"_wpnonce",
		document
			.querySelector("#_nonce_imageseo_valid_api_key")
			.getAttribute("value")
	);
	formData.append("api_key", apiKey);

	//@ts-ignore
	const response = await fetch(IMAGESEO_DATA.ADMIN_AJAX, {
		method: "POST",
		body: formData,
	});

	return await response.json();
};

export const login = async (email, password) => {
	const formData = new FormData();

	formData.append("action", "imageseo_login");
	formData.append(
		"_wpnonce",
		document.querySelector("#_nonce_imageseo_login").getAttribute("value")
	);
	formData.append("email", email);
	formData.append("password", password);

	//@ts-ignore
	const response = await fetch(IMAGESEO_DATA.ADMIN_AJAX, {
		method: "POST",
		body: formData,
	});

	return await response.json();
};
