import { reduce, isEmpty, memoize, uniq, omit } from "lodash";
import React, { useReducer, createContext } from "react";
import getFilenameWithoutExtension from "../../helpers/getFilenameWithoutExtension";
import getFilenamePreview from "../../helpers/getFilenamePreview";

const initialState = {
	allIds: [],
	allIdsOptimized: [],
	bulkActive: false,
	bulkFinish: false,
	bulkPause: false,
	currentProcess: null,
	attachments: {},
	reports: {},
	altPreviews: {},
	filePreviews: {}
};

function reducer(state, { type, payload }) {
	switch (type) {
		case "RESTART_BULK":
			return {
				...state,
				...payload
			};
		case "ADD_ALT_PREVIEW":
			return {
				...state,
				altPreviews: {
					...state.altPreviews,
					[payload.ID]: payload.alt
				}
			};
		case "ADD_FILENAME_PREVIEW":
			return {
				...state,
				filePreviews: {
					...state.filePreviews,
					[payload.ID]: payload.file
				}
			};
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
				bulkActive: true,
				attachments: {},
				reports: {}
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
		case "FINISH_BULK":
			return {
				...state,
				bulkActive: false,
				bulkFinish: true,
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
		case "ATTACHMENT_NOT_FOUND":
			const {
				[payload]: removeAttachmentId,
				...rest
			} = state.attachments;
			console.log("Rest : ", rest);
			return {
				...state,
				currentProcess: state.currentProcess + 1,
				attachments: {
					...rest
				}
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
	let initState = {
		...initialState
	};
	if (IMAGESEO_DATA.CURRENT_PROCESSED) {
		initState = {
			...initState,
			...IMAGESEO_DATA.CURRENT_PROCESSED.state
		};
	}
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

const getAllFilenamesPreviewMemoize = memoize(state => {
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
	getAllFilenamesPreview: getAllFilenamesPreviewMemoize
};

export default BulkProcessContextProvider;
export { BulkProcessContext, selectors };
