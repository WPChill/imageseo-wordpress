import {
	reduce,
	isEmpty,
	memoize,
	uniq,
	filter,
	set,
	get,
	isNil,
} from "lodash";
import React, { useReducer, createContext } from "react";
import getFilenameWithoutExtension from "../../helpers/getFilenameWithoutExtension";
import getFilenamePreview from "../../helpers/getFilenamePreview";

const initialState = {
	allIds: [],
	allIdsOptimized: [],
	currentProcess: null,
	finishProcess: null,
	lastProcess: null,
	bulkActive: false,
	bulkIsFinish: false,
};

function reducer(state, { type, payload }) {
	switch (type) {
		case "UPDATE_QUERY_IMAGES":
			return {
				...state,
				allIds: payload.allIds,
				allIdsOptimized: payload.allIdsOptimized,
			};
		case "START_CURRENT_PROCESS":
			return {
				...state,
				bulkActive: true,
				currentProcess: payload,
				lastProcess: null,
			};
		case "STOP_CURRENT_PROCESS":
			return {
				...state,
				bulkActive: false,
				currentProcess: null,
				lastProcess: payload,
			};
		case "UPDATE_CURRENT_PROCESS":
			return {
				...state,
				currentProcess: payload,
			};
		case "FINISH_CURRENT_PROCESS":
			return {
				...state,
				bulkIsFinish: true,
				finishProcess: payload,
			};
		default:
			return state;
	}
}

const BulkProcessContext = createContext(null);

const BulkProcessContextProvider = ({ children }) => {
	let initState = {
		...initialState,
		currentProcess: get(IMAGESEO_DATA, "CURRENT_PROCESSED", null),
		lastProcess: get(IMAGESEO_DATA, "LAST_PROCESSED", null),
	};
	if (!isNil(initState.currentProcess)) {
		initState = {
			...initState,
			bulkActive: true,
		};
	}

	const [state, dispatch] = useReducer(reducer, initState);
	return (
		<BulkProcessContext.Provider
			value={{
				state,
				dispatch,
			}}
		>
			{children}
		</BulkProcessContext.Provider>
	);
};

const getAllFilenamesPreviewMemoize = memoize((state) => {
	const filenames = reduce(
		Object.values(state.filePreviews),
		(result, value, key) => {
			const filename = getFilenameWithoutExtension(
				getFilenamePreview(value)
			);
			if (!isEmpty(filename)) {
				result.push(filename);
			}
			return result;
		},
		[]
	);

	return uniq(filenames);
});

const selectors = {
	getAllFilenamesPreview: getAllFilenamesPreviewMemoize,
};

export default BulkProcessContextProvider;
export { BulkProcessContext, selectors };
