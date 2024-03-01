import apiFetch from '@wordpress/api-fetch';

export const saveOptions = async (path, options) => {
	try {
		const { user, ...restOfData } = options;
		const response = await apiFetch({
			path,
			method: 'POST',
			data: { ...restOfData },
		});
	} catch (error) {
		console.error('Error saving settings:', error);
	}
};

export const getOptions = async (path) => {
	try {
		const response = await apiFetch({ path });
		return response;
	} catch (error) {
		console.error('Error receiving settings:', error);
	}
};

export const fetcher = async (url) => {
	const r = await apiFetch({ path: url, method: 'GET' });
	return r;
};

export const fetcherPost = async ([url, data]) => {
	const r = await apiFetch({ path: url, method: 'POST', data });
	return r.data;
};
