import React, { createContext, useState } from "react";

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
	const [altFilter, setAltFilter] = useState(null);
	const [altFill, setAltFill] = useState(null);

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
