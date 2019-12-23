import React, { useReducer, createContext } from "react";

const initialState = {
	allIds: [],
	bulkActive: false,
	currentProcessId: null,
	attachments: {},
	reports: {}
};

function reducer(state, { type, payload }) {
	switch (type) {
		case "UPDATE_ALL_IDS":
			return {
				...state,
				allIds: payload
			};
		case "START_BULK":
			return {
				...state,
				bulkActive: true
			};
		case "STOP_BULK":
			return {
				...state,
				bulkActive: false
			};
		case "BEGIN_PROCESS":
			return {
				...state,
				currentProcessId: payload
			};
		case "STOP_CURRENT_PROCESS":
			return {
				...state,
				currentProcessId: null
			};
		case "ADD_ATTACHMENT":
			return {
				...state,
				[payload.key]: payload.attachment
			};
		case "UPDATE_ALL_ATTACHMENTS":
			return {
				...state
			};
	}
}

const BulkProcessContext = createContext(null);

const BulkProcessContextProvider = ({ children }) => {
	const [state, dispatch] = useReducer(reducer, initialState);

	return (
		<BulkProcessContext.Provider
			value={{
				state,
				dispatch
			}}
		>
			{children}
		</BulkProcessContext.Provider>
	);
};

export default BulkProcessContextProvider;
export { BulkProcessContext };
