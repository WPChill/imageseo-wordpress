import { fetcher } from '../utils';
import useSWR from 'swr';
import { useState } from '@wordpress/element';

export const useImageQuery = () => {
	const [initialLoad, setInitialLoad] = useState(true);
	const { data, error, isLoading, mutate } = useSWR(
		'/imageseo/v1/image-query',
		fetcher,
		{
			onError: () => {
				setInitialLoad(false);
			},
			onSuccess: () => {
				setInitialLoad(false);
			},
			revalidateIfStale: false,
			revalidateOnFocus: false,
			revalidateOnReconnect: false,
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
