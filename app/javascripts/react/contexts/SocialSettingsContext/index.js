import React, { createContext, useReducer } from "react";
import { merge, get } from "lodash";

function reducer(state, { type, payload }) {
	switch (type) {
		case "UPDATE_OPTIONS":
			return merge(state, payload);
		case "UPDATE_OPTION":
			return {
				...state,
				[payload.key]: payload.value
			};
		default:
			return state;
	}
}

const SocialSettingsContext = createContext(null);

const SocialSettingsContextProvider = ({ children }) => {
	const getInitialState = () => {
		return {
			layout: get(IMAGESEO_DATA, "SETTINGS.layout", "CARD_LEFT"),
			textColor: get(IMAGESEO_DATA, "SETTINGS.textColor", "#000000"),
			contentBackgroundColor: get(
				IMAGESEO_DATA,
				"SETTINGS.contentBackgroundColor",
				"#ffffff"
			),
			starColor: get(IMAGESEO_DATA, "SETTINGS.starColor", "#F8CA00"),
			visibilitySubTitle: get(
				IMAGESEO_DATA,
				"SETTINGS.visibilitySubTitle",
				true
			),
			visibilityRating: get(
				IMAGESEO_DATA,
				"SETTINGS.visibilityRating",
				true
			),
			defaultBgImg: get(
				IMAGESEO_DATA,
				"SETTINGS.defaultBgImg",
				`${IMAGESEO_URL_DIST}/images/default_image.png`
			)
		};
	};

	const [state, dispatch] = useReducer(reducer, getInitialState());

	return (
		<SocialSettingsContext.Provider
			value={{
				state,
				dispatch
			}}
		>
			{children}
		</SocialSettingsContext.Provider>
	);
};

export default SocialSettingsContextProvider;
export { SocialSettingsContext };
