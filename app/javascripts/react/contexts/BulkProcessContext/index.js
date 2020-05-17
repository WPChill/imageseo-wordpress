import { reduce, isEmpty, memoize, uniq, filter, set, get } from "lodash";
import React, { useReducer, createContext } from "react";
import getFilenameWithoutExtension from "../../helpers/getFilenameWithoutExtension";
import getFilenamePreview from "../../helpers/getFilenamePreview";

const initialState = {
	allIds: [],
	allIdsOptimized: [],
	bulkActive: false,
	bulkFinish: false,
	bulkPause: false,
	attachments: {},
	reports: {},
};

function reducer(state, { type, payload }) {
	switch (type) {
		case "RESTART_BULK":
			return {
				...state,
				...payload,
			};
		case "UPDATE_ALL_IDS":
			return {
				...state,
				allIds: payload,
			};
		case "UPDATE_ALL_IDS_OPTIMIZED":
			return {
				...state,
				allIdsOptimized: payload,
			};
		case "START_BULK":
			return {
				...state,
				bulkActive: true,
				attachments: {},
				reports: {},
			};
		case "PLAY_BULK":
			return {
				...state,
				bulkPause: false,
			};
		case "PAUSE_BULK":
			return {
				...state,
				bulkPause: true,
			};
		case "FINISH_BULK":
			return {
				...state,
				bulkActive: false,
				bulkFinish: true,
			};
		case "ATTACHMENT_NOT_FOUND":
			const {
				[payload]: removeAttachmentId,
				...rest
			} = state.attachments;

			set(
				IMAGESEO_DATA,
				"CURRENT_PROCESSED.count_optimized",
				get(IMAGESEO_DATA, "CURRENT_PROCESSED.count_optimized", 0) + 1
			);
			return {
				...state,
				currentProcess: state.currentProcess + 1,
				attachments: {
					...rest,
				},
				allIds: filter(state.allIds, (itm) => itm !== payload),
			};
		case "ADD_ATTACHMENT":
			return {
				...state,
				attachments: {
					...state.attachments,
					[payload.ID]: payload,
				},
			};
		case "ADD_REPORT":
			return {
				...state,
				reports: {
					...state.reports,
					[payload.ID]: payload,
				},
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
	if (IMAGESEO_DATA.CURRENT_PROCESSED) {
		initState = {
			...initState,
			...IMAGESEO_DATA.CURRENT_PROCESSED.state,
		};
	}
	const [state, dispatch] = useReducer(reducer, initialState);

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
