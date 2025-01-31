import { createReduxStore, register } from '@wordpress/data';

const DEFAULT_STATE = {
	settings: {
		overwriteExisting: false,
		includeNonLibrary: false,
	},
	isUpdating: false,
	error: null,
};

const store = createReduxStore('imageseo', {
	reducer(state = DEFAULT_STATE, action) {
		switch (action.type) {
			case 'UPDATE_SETTINGS':
				return {
					...state,
					settings: {
						...state.settings,
						...action.settings,
					},
				};
			case 'SET_UPDATING':
				return {
					...state,
					isUpdating: action.isUpdating,
				};
			case 'SET_ERROR':
				return {
					...state,
					error: action.error,
				};
			default:
				return state;
		}
	},
	actions: {
		updateSettings(settings) {
			return {
				type: 'UPDATE_SETTINGS',
				settings,
			};
		},
		setUpdating(isUpdating) {
			return {
				type: 'SET_UPDATING',
				isUpdating,
			};
		},
		setError(error) {
			return {
				type: 'SET_ERROR',
				error,
			};
		},
	},
	selectors: {
		getSettings(state) {
			return state.settings;
		},
		isUpdating(state) {
			return state.isUpdating;
		},
		getError(state) {
			return state.error;
		},
	},
});

register(store);
