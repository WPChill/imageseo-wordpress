const getAttachement = async attachmentId => {
	const formData = new FormData();

	formData.append("action", "imageseo_get_attachment");
	formData.append("attachmentId", attachmentId);

	const response = await fetch(ajaxurl, {
		method: "POST",
		body: formData
	});

	return await response.json();
};

export default getAttachement;
