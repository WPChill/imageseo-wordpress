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
};

function reducer(state, { type, payload }) {
	switch (type) {
		case "UPDATE_QUERY_IMAGES":
			return {
				...state,
				allIds: payload.allIds,
				allIdsOptimized: payload.allIdsOptimized,
			};
		default:
			return state;
	}
}

const BulkProcessContext = createContext(null);

const BulkProcessContextProvider = ({ children }) => {
	let initState = {
		...initialState,
	};
	// if (!isNil(IMAGESEO_DATA.CURRENT_PROCESSED)) {
	// 	initState = {
	// 		...initState,
	// 		bulkActive: true,
	// 		allIds: IMAGESEO_DATA.CURRENT_PROCESSED.id_images,
	// 	};
	// } else if (!isNil(IMAGESEO_DATA.LAST_PROCESSED)) {
	// 	initState = {
	// 		...initState,
	// 		allIds: IMAGESEO_DATA.LAST_PROCESSED.id_images,
	// 	};
	// }

	// if (!isNil(IMAGESEO_DATA.IS_FINISH) && IMAGESEO_DATA.IS_FINISH === 1) {
	// 	initState = {
	// 		...initState,
	// 		bulkFinish: true,
	// 		bulkActive: false,
	// 	};
	// }

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
