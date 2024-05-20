import { fetcherPost } from '../utils';
import useSWR from 'swr';
import { useState } from '@wordpress/element';
import useSettings from './useSettings';

export const useUser = (apiKey) => {
	const [initialLoad, setInitialLoad] = useState(true);
	const { setOptions, addNotice } = useSettings();
	const { data, error, isLoading, mutate } = useSWR(
		[apiKey ? '/imageseo/v1/validate-api-key' : null, { apiKey }],
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
				}

				setOptions({ allowed: true }, true);
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
