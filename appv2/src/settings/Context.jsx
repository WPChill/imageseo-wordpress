import {
	createContext,
	useReducer,
	useEffect,
	useRef,
	useState,
	useCallback,
	useMemo,
} from '@wordpress/element';
import { useDebouncedState } from './hooks/useDebouncedState';
import { saveOptions } from './utils';
import { __ } from '@wordpress/i18n';

const initialState = {
	// eslint-disable-next-line no-undef
	options: typeof imageSeoSettings !== 'undefined' ? imageSeoSettings : {},
	// eslint-disable-next-line no-undef
	global: typeof imageSeoGlobal !== 'undefined' ? imageSeoGlobal : {},
};

const actionTypes = {
	SET_ACTIVE_TAB: 'SET_ACTIVE_TAB',
	SET_OPTIONS: 'SET_OPTIONS',
	OPTIONS_MODIFIED: 'OPTIONS_MODIFIED',
};

const reducer = (state, action) => {
	switch (action.type) {
		case actionTypes.SET_OPTIONS:
			return {
				...state,
				options: { ...state.options, ...action.payload },
			};
		case actionTypes.OPTIONS_MODIFIED:
			return {
				...state,
				optionsModified: action.payload,
			};
		default:
			return state;
	}
};

export const SettingsContext = createContext(initialState);
export const SettingsProvider = ({ children }) => {
	const isInitialMount = useRef(true);
	const [notices, setNotices] = useState([]);
	const [loading, setLoading] = useState(false);
	const [state, dispatch] = useReducer(reducer, initialState);
	const [debouncedOptions, setDebouncedOptions] = useDebouncedState(
		initialState.options,
		500
	);

	const setOptions = useCallback(
		(options, withoutSave = false) => {
			dispatch({ type: actionTypes.SET_OPTIONS, payload: options });
			if (withoutSave) return;
			dispatch({ type: actionTypes.OPTIONS_MODIFIED, payload: true });
			setDebouncedOptions({ ...state.options, ...options });
		},
		[setDebouncedOptions, state.options]
	);

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

	const contextValue = useMemo(
		() => ({
			options: state.options,
			global: state.global,
			loading,
			setOptions,
			addNotice,
			removeNotice,
			notices,
		}),
		[
			addNotice,
			loading,
			notices,
			removeNotice,
			setOptions,
			state.global,
			state.options,
		]
	);

	const debuncedMemo = useMemo(() => debouncedOptions, [debouncedOptions]);

	useEffect(() => {
		if (isInitialMount.current) return;
		if (!state.optionsModified) return;

		saveOptions(`imageseo/v1/settings`, debuncedMemo)
			.then(() => {
				setLoading(false);
				addNotice({
					status: 'info',
					content: __('Options saved', 'imageseo'),
				});
			})
			.catch((e) => {
				console.warn(e);
				addNotice({
					status: 'error',
					content: __('Error saving options', 'imageseo'),
				});
				setLoading(false);
			});
	}, [addNotice, debuncedMemo, state.optionsModified]);

	useEffect(() => {
		if (isInitialMount.current) {
			isInitialMount.current = false;
		}
	}, []);

	return (
		<SettingsContext.Provider value={contextValue}>
			{children}
		</SettingsContext.Provider>
	);
};
