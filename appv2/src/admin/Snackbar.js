import { SnackbarList } from '@wordpress/components';
import { eventBus } from './EventBus';
import { useEffect, useState, useCallback } from '@wordpress/element';

export function Snackbar() {
	const [notices, setNotices] = useState([]);

	const addNotice = useCallback((notice) => {
		const newNotice = {
			id: new Date().getTime(),
			...notice,
			content: notice?.content || '',
			politeness: notice?.politeness || 'polite',
			actions: notice?.actions || [],
			explicitDismiss: notice?.explicitDismiss || false,
		};
		setNotices((prev) => [...prev, newNotice]);
	}, []);
	const removeNotice = useCallback((id) => {
		setNotices((prev) => prev.filter((n) => n.id !== id));
	}, []);

	useEffect(() => {
		console.log('Snackbar mounted');
		eventBus.subscribe('snackbar', addNotice);

		return () => {
			eventBus.unsubscribe('snackbar', addNotice);
		};
	}, [addNotice]);

	return <SnackbarList notices={notices} onRemove={removeNotice} />;
}
