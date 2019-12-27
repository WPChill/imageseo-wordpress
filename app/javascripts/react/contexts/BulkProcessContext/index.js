import React, { useReducer, createContext } from "react";

const initialState = {
	allIds: [],
	allIdsOptimized: [],
	bulkActive: false,
	bulkPause: false,
	currentProcess: null,
	attachments: {},
	reports: {}
};

function reducer(state, { type, payload }) {
	console.group();
	console.warn("Type: ", type);
	console.info("Payload: ", payload);
	console.groupEnd();
	switch (type) {
		case "UPDATE_ALL_IDS":
			return {
				...state,
				allIds: payload
			};
		case "UPDATE_ALL_IDS_OPTIMIZED":
			return {
				...state,
				allIdsOptimized: payload
			};
		case "START_BULK":
			return {
				...state,
				bulkActive: true
			};
		case "PLAY_BULK":
			return {
				...state,
				bulkPause: false
			};
		case "PAUSE_BULK":
			return {
				...state,
				bulkPause: true
			};
		case "STOP_BULK":
			return {
				...state,
				bulkActive: false,
				currentProcess: false
			};
		case "NEW_PROCESS":
			return {
				...state,
				currentProcess: payload
			};
		case "STOP_CURRENT_PROCESS":
			return {
				...state,
				currentProcess: null
			};
		case "ADD_ATTACHMENT":
			return {
				...state,
				attachments: {
					...state.attachments,
					[payload.ID]: payload
				}
			};
		case "ADD_REPORT":
			return {
				...state,
				reports: {
					...state.reports,
					[payload.ID]: payload
				}
			};
		default:
			return state;
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
