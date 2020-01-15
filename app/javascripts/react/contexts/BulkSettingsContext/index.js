import React, { createContext, useReducer } from "react";
import { find, merge } from "lodash";

function reducer(state, { type, payload }) {
	switch (type) {
		case "UPDATE_OPTIONS":
			return merge(state, payload);
		case "UPDATE_OPTION":
			return {
				...state,
				[payload.key]: payload.value
			};
		case "NEW_OPTIONS":
			return payload;
		default:
			return state;
	}
}

const BulkSettingsContext = createContext(null);

const BulkSettingsContextProvider = ({ children }) => {
	const getInitialState = () => {
		return {
			smallImages: false,
			wantValidateResult: false,
			language: IMAGESEO_DATA.OPTIONS.default_language_ia,
			optimizeAlt: false,
			formatAlt: null,
			formatAltCustom: "",
			altFilter: find(IMAGESEO_DATA.ALT_SPECIFICATION, { id: "ALL" }).id,
			altFill: find(IMAGESEO_DATA.ALT_FILL_TYPE, { id: "FILL_ALL" }).id,
			optimizeFile: false,
			restartBulk: false
		};
	};
	const [state, dispatch] = useReducer(reducer, getInitialState());

	return (
		<BulkSettingsContext.Provider
			value={{
				state,
				dispatch
			}}
		>
			{children}
		</BulkSettingsContext.Provider>
	);
};

export default BulkSettingsContextProvider;
export { BulkSettingsContext };
