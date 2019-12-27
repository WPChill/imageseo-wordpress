import React, { createContext, useState } from "react";
import { find } from "lodash";

const BulkSettingsContext = createContext(null);

const BulkSettingsContextProvider = ({ children }) => {
	// Global settings
	const [smallImages, setSmallImages] = useState(false);
	const [wantValidateResult, setWantValidateResult] = useState(false);
	const [language, setLanguage] = useState(
		IMAGESEO_DATA.OPTIONS.default_language_ia
	);

	// Alt settings
	const [optimizeAlt, setOptimizeAlt] = useState(false);
	const [formatAlt, setAltFormat] = useState(null);
	const [altFilter, setAltFilter] = useState(
		find(IMAGESEO_DATA.ALT_SPECIFICATION, { id: "ALL" }).id
	);
	const [altFill, setAltFill] = useState(
		find(IMAGESEO_DATA.ALT_FILL_TYPE, { id: "FILL_ALL" }).id
	);

	// File settings
	const [optimizeFile, setOptimizeFile] = useState(false);

	return (
		<BulkSettingsContext.Provider
			value={{
				settings: {
					smallImages,
					wantValidateResult,
					language,
					optimizeAlt,
					formatAlt,
					altFilter,
					altFill,
					optimizeFile
				},
				actions: {
					setSmallImages,
					setWantValidateResult,
					setLanguage,
					setOptimizeAlt,
					setAltFormat,
					setAltFilter,
					setAltFill,
					setOptimizeFile
				}
			}}
		>
			{children}
		</BulkSettingsContext.Provider>
	);
};

export default BulkSettingsContextProvider;
export { BulkSettingsContext };
