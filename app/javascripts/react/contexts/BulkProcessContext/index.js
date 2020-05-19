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
	bulkActive: false,
	bulkFinish: false,
	bulkPause: false,
	attachments: {},
	reports: {},
	altPreviews: {},
	filePreviews: {},
};

function reducer(state, { type, payload }) {
	switch (type) {
		case "ADD_ALT_PREVIEW":
			return {
				...state,
				altPreviews: {
					...state.altPreviews,
					[payload.ID]: payload.alt,
				},
			};
		case "ADD_FILENAME_PREVIEW":
			return {
				...state,
				filePreviews: {
					...state.filePreviews,
					[payload.ID]: payload.file,
				},
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
	if (!isNil(IMAGESEO_DATA.CURRENT_PROCESSED)) {
		initState = {
			...initState,
			bulkActive: true,
			allIds: IMAGESEO_DATA.CURRENT_PROCESSED.id_images,
		};
	} else if (!isNil(IMAGESEO_DATA.LAST_PROCESSED)) {
		initState = {
			...initState,
			allIds: IMAGESEO_DATA.LAST_PROCESSED.id_images,
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
