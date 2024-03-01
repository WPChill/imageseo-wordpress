import { fetcherPost } from '../utils';
import useSWR from 'swr';
import { useState } from '@wordpress/element';

export const useUser = (apiKey) => {
	const [initialLoad, setInitialLoad] = useState(true);
	const { data, error, isLoading, mutate } = useSWR(
		['/imageseo/v1/validate-api-key', { apiKey }],
		fetcherPost,
		{
			onError: () => {
				setInitialLoad(false);
			},
			onSuccess: () => {
				setInitialLoad(false);
			},
		}
	);

	return {
		data,
		error,
		isLoading: initialLoad,
		isFetching: isLoading,
		mutate,
	};
};
