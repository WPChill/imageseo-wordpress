import React, { createContext, useReducer } from "react";
import { merge, get } from "lodash";

function reducer(state, { type, payload }) {
	switch (type) {
		case "UPDATE_OPTIONS":
			return merge(state, payload);
		case "UPDATE_OPTION":
			return {
				...state,
				[payload.key]: payload.value,
			};
		default:
			return state;
	}
}

const SocialSettingsContext = createContext(null);

const SocialSettingsContextProvider = ({ children }) => {
	const getInitialState = () => {
		return {
			layout: get(
				//@ts-ignore
				IMAGESEO_DATA,
				"OPTIONS.social_media_settings.layout",
				"CARD_LEFT"
			),
			textColor: get(
				//@ts-ignore
				IMAGESEO_DATA,
				"OPTIONS.social_media_settings.textColor",
				"#000000"
			),
			contentBackgroundColor: get(
				//@ts-ignore
				IMAGESEO_DATA,
				"OPTIONS.social_media_settings.contentBackgroundColor",
				"#ffffff"
			),
			starColor: get(
				//@ts-ignore
				IMAGESEO_DATA,
				"OPTIONS.social_media_settings.starColor",
				"#F8CA00"
			),
			visibilitySubTitle: get(
				//@ts-ignore
				IMAGESEO_DATA,
				"OPTIONS.social_media_settings.visibilitySubTitle",
				true
			),
			visibilitySubTitleTwo: get(
				//@ts-ignore
				IMAGESEO_DATA,
				"OPTIONS.social_media_settings.visibilitySubTitleTwo",
				true
			),
			visibilityAvatar: get(
				//@ts-ignore
				IMAGESEO_DATA,
				"OPTIONS.social_media_settings.visibilityAvatar",
				true
			),
			visibilityRating: get(
				//@ts-ignore
				IMAGESEO_DATA,
				"OPTIONS.social_media_settings.visibilityRating",
				false
			),
			defaultBgImg: get(
				//@ts-ignore
				IMAGESEO_DATA,
				"OPTIONS.social_media_settings.defaultBgImg",
				//@ts-ignore
				`${IMAGESEO_DATA.URL_DIST}/images/default_image.png`
			),
			textAlignment: get(
				//@ts-ignore
				IMAGESEO_DATA,
				"OPTIONS.social_media_settings.textAlignment",
				"top"
			),
			logoUrl: get(
				//@ts-ignore
				IMAGESEO_DATA,
				"OPTIONS.social_media_settings.logoUrl",
				//@ts-ignore
				`${IMAGESEO_DATA.URL_DIST}/images/default_logo.png`
			),
		};
	};

	const [state, dispatch] = useReducer(reducer, getInitialState());

	return (
		<SocialSettingsContext.Provider
			value={{
				state,
				dispatch,
			}}
		>
			{children}
		</SocialSettingsContext.Provider>
	);
};

export default SocialSettingsContextProvider;
export { SocialSettingsContext };
