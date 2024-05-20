import { useEffect, useRef, useState, useCallback } from '@wordpress/element';

export const useDebouncedState = (
	defaultValue,
	wait,
	options = { leading: false }
) => {
	const [value, setValue] = useState(defaultValue);
	const timeoutRef = useRef(null);
	const leadingRef = useRef(true);

	const clearTimeout = () => window.clearTimeout(timeoutRef.current);
	useEffect(() => clearTimeout, []);

	const debouncedSetValue = useCallback(
		(newValue) => {
			clearTimeout();
			if (leadingRef.current && options.leading) {
				setValue(newValue);
			} else {
				timeoutRef.current = window.setTimeout(() => {
					leadingRef.current = true;
					setValue(newValue);
				}, wait);
			}
			leadingRef.current = false;
		},
		[options.leading, wait]
	);

	return [value, debouncedSetValue];
};
