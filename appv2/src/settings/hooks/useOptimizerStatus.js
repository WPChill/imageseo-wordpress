import { fetcher } from '../utils';
import useSWR from 'swr';
import { useState } from '@wordpress/element';

export const useOptimizerStatus = () => {
	const [initialLoad, setInitialLoad] = useState(true);
	const { data, error, isLoading, mutate } = useSWR(
		'/imageseo/v1/get-bulk-optimizer-status',
		fetcher,
		{
			onError: () => {
				setInitialLoad(false);
			},
			onSuccess: () => {
				setInitialLoad(false);
			},
			refreshInterval: 10000,
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
