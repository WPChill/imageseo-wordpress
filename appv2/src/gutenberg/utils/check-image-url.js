import apiFetch from '@wordpress/api-fetch';

export const checkImageUrl = async (data) => {
	console.log(data);
	try {
		// First check if the URL returns 404
		const response = await fetch(data.url, {
			method: 'HEAD',
			cache: 'no-store',
		});

		console.log(response?.status);

		// Only make the API call if we get a 404, otherwise the URL is fine
		if (response.status === 404) {
			const apiResponse = await apiFetch({
				path: `imageseo/v1/check-image/`,
				method: 'POST',
				data,
			});

			return apiResponse;
		}

		// If URL is accessible, return empty object (no updates needed)
		return {};
	} catch (err) {
		console.log(err);
		// If fetch fails (network error, CORS, etc), try the API
		// as the URL might have been optimized
		try {
			const apiResponse = await apiFetch({
				path: `imageseo/v1/check-image/`,
				method: 'POST',
				data,
			});

			return apiResponse;
		} catch (apiErr) {
			console.error('Error checking image:', apiErr);
			return { error: apiErr.message };
		}
	}
};
