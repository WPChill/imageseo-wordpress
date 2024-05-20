import { useEffect, useState } from '@wordpress/element';
import { fetcher } from '../utils';
import { Spinner } from '@wordpress/components';
import useSWR from 'swr';

export const ErrorLog = () => {
	const [show, setShow] = useState(false);
	const { data, isLoading } = useSWR(
		'/imageseo/v1/optimizer-errors',
		fetcher,
		{
			refreshInterval: 10000,
		}
	);
	const { data:debugData, isLoading:debugLoading } = useSWR(
		'/imageseo/v1/debug-info',
		fetcher,
		{
			refreshInterval: 10000,
		}
	);
	useEffect(() => {
		const debugLog = localStorage.getItem('imageseo_debug_log');
		if (debugLog) {
			setShow(true);
		}
	}, []);

	if (!show) {
		return null;
	}

	if (isLoading) {
		return <Spinner />;
	}

	return (
		<>
			<h4>Error Log</h4>
			<pre>{JSON.stringify(data, null, 2)}</pre>
			<h4>Debug Info</h4>
			<pre>{JSON.stringify(debugData, null, 2)}</pre>
		</>
	);
};
