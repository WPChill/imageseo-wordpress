import apiFetch from '@wordpress/api-fetch';

export const fetchAltText = async (data) => {
	try {
		const response = await apiFetch({
			path: `imageseo/v1/generate-alt-text/`,
			method: 'POST',
			data,
		});

		return response;
	} catch (error) {
		console.error('Error calling the api:', error);
	}
};
