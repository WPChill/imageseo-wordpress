import React, { useReducer, createContext } from "react";

const initialStateReducer = {
	limit_images: 0,
	user_infos: null,
	default_language_ia: "en"
};

function reducer(state, { type, payload }) {
	switch (type) {
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
