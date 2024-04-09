import { fetcherPost } from '../utils';
import useSWR from 'swr';
import { useState } from '@wordpress/element';
import useSettings from './useSettings';

export const useUser = (apiKey) => {
	const [initialLoad, setInitialLoad] = useState(true);
	const { setOptions, addNotice } = useSettings();
	const { data, error, isLoading, mutate } = useSWR(
		['/imageseo/v1/validate-api-key', { apiKey }],
		fetcherPost,
		{
			onError: () => {
				setInitialLoad(false);
			},
			onSuccess: (successData) => {
				setInitialLoad(false);

				if (successData?.message) {
					setOptions({ allowed: false });
					addNotice({
						status: 'error',
						content: successData.message,
					});

					return;
				}
				setOptions({ allowed: true });
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
