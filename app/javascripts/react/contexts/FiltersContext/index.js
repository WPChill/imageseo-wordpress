import React, { useReducer, createContext } from "react";

const initialState = [[{ meta: "", condition: "", value: "" }]];

function reducer(state, { type, payload }) {
	switch (type) {
		case "ADD_OR_CONDITION":
			return [...state, [{ meta: "", condition: "", value: "" }]];
	}
}

const FiltersContext = createContext(null);

const FiltersContextProvider = ({ children }) => {
	const [state, dispatch] = useReducer(reducer, initialState);

	return (
		<FiltersContext.Provider
			value={{
				state,
				dispatch
			}}
		>
			{children}
		</FiltersContext.Provider>
	);
};

export default FiltersContextProvider;
export { FiltersContext };
