import apiFetch from '@wordpress/api-fetch';

export const apiCall = async (data) => {
	try {
		const response = await apiFetch({
			path: `imageseo/v1/optimize-image/`,
			method: 'POST',
			data,
		});

		return response;
	} catch (error) {
		console.error('Error calling the api:', error);
	}
};
