import React, { createContext, useReducer } from "react";
import { merge } from "lodash";

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
			layout: "CARD_LEFT",
			textColor: "#000000",
			contentBackgroundColor: "#ffffff",
			backgroundImage: null
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
