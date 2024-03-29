import {
	createContext,
	useReducer,
	useEffect,
	useRef,
	useState,
	useCallback,
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
};

const reducer = (state, action) => {
	switch (action.type) {
		case actionTypes.SET_OPTIONS:
			return {
				...state,
				options: { ...state.options, ...action.payload },
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
			setDebouncedOptions({ ...state.options, ...options });
		},
		[setDebouncedOptions, state.options]
	);

	const getInitialValues = useCallback(async () => {
		setLoading(true);

		setLoading(false);
	}, [setLoading]);

	const addNotice = (notice) => {
		const newNotice = {
			id: new Date().getTime(),
			...notice,
			content: notice?.content || '',
			politeness: notice?.politeness || 'polite',
			actions: notice?.actions || [],
			explicitDismiss: notice?.explicitDismiss || false,
		};
		setNotices((prev) => [...prev, newNotice]);
	};

	const removeNotice = (id) => {
		setNotices((prev) => prev.filter((n) => n.id !== id));
	};

	const contextValue = {
		options: state.options,
		global: state.global,
		loading,
		setOptions,
		addNotice,
		removeNotice,
		notices,
	};

	useEffect(() => {
		if (isInitialMount.current) {
			isInitialMount.current = false;
			return;
		}

		saveOptions(`imageseo/v1/settings`, debouncedOptions)
			.then(() => setLoading(false))
			.catch((e) => {
				console.warn(e);
				addNotice({
					status: 'error',
					content: __('Error saving options', 'imageseo'),
				});
			});
	}, [debouncedOptions, getInitialValues]);
	return (
		<SettingsContext.Provider value={contextValue}>
			{children}
		</SettingsContext.Provider>
	);
};
