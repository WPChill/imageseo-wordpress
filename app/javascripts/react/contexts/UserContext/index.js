import React, { useReducer, createContext } from "react";

const initialStateReducer = {
	limit_images: 0,
	user_infos: null,
	default_language_ia: "en"
};

function reducer(state, { type, payload }) {
	switch (type) {
		case "DECREASE_BONUS_STOCK_IMAGES":
			return {
				...state,
				user_infos: {
					...state.user_infos,
					bonus_stock_images: state.user_infos.bonus_stock_images - 1
				}
			};
		case "INCREASE_CURRENT_REQUEST_IMAGES":
			return {
				...state,
				user_infos: {
					...state.user_infos,
					current_request_images:
						state.user_infos.current_request_images + 1
				}
			};
		default:
			return state;
	}
}

const UserContext = createContext(null);

const UserContextProvider = ({
	children,
	initialState = initialStateReducer
}) => {
	const [state, dispatch] = useReducer(reducer, initialState);

	return (
		<UserContext.Provider
			value={{
				state,
				dispatch
			}}
		>
			{children}
		</UserContext.Provider>
	);
};

export default UserContextProvider;
export { UserContext };
