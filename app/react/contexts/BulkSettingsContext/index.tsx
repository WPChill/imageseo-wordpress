import React, { createContext, useReducer } from "react";
import { find, merge, get } from "lodash";

function reducer(state, { type, payload }) {
	switch (type) {
		case "UPDATE_OPTIONS":
			return merge(state, payload);
		case "UPDATE_OPTION":
			return {
				...state,
				[payload.key]: payload.value,
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
			wantValidateResult: get(
				//@ts-ignore
				IMAGESEO_DATA,
				"CURRENT_PROCESSED.settings.wantValidateResult",
				false
			),
			language: get(
				//@ts-ignore
				IMAGESEO_DATA,
				"CURRENT_PROCESSED.settings.language",
				//@ts-ignore
				IMAGESEO_DATA.OPTIONS.default_language_ia
			),
			optimizeAlt: get(
				//@ts-ignore
				IMAGESEO_DATA,
				"CURRENT_PROCESSED.settings.optimizeAlt",
				false
			),
			formatAlt: get(
				//@ts-ignore
				IMAGESEO_DATA,
				"CURRENT_PROCESSED.settings.formatAlt",
				null
			),
			formatAltCustom: get(
				//@ts-ignore
				IMAGESEO_DATA,
				"CURRENT_PROCESSED.settings.formatAltCustom",
				""
			),
			//@ts-ignore
			altFilter: find(IMAGESEO_DATA.ALT_SPECIFICATION, { id: "ALL" }).id,
			//@ts-ignore
			altFill: find(IMAGESEO_DATA.ALT_FILL_TYPE, { id: "FILL_ALL" }).id,
			optimizeFile: get(
				//@ts-ignore
				IMAGESEO_DATA,
				"CURRENT_PROCESSED.settings.optimizeFile",
				false
			),
		};
	};
	const [state, dispatch] = useReducer(reducer, getInitialState());

	return (
		<BulkSettingsContext.Provider
			value={{
				state,
				dispatch,
			}}
		>
			{children}
		</BulkSettingsContext.Provider>
	);
};

export default BulkSettingsContextProvider;
export { BulkSettingsContext };
