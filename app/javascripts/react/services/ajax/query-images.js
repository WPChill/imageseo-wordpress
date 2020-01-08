import { get } from "lodash";

const queryImages = async options => {
	const formData = new FormData();

	formData.append("action", "imageseo_query_images");
	formData.append("filters", JSON.stringify(get(options, "filters", {})));

	const response = await fetch(ajaxurl, {
		method: "POST",
		body: formData
	});

	return await response.json();
};

export default queryImages;
