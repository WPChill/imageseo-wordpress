import React, { useContext } from "react";
import { get, isNil } from "lodash";

//@ts-ignore
const { __ } = wp.i18n;

import BulkSettingsContextProvider from "../../contexts/BulkSettingsContext";
import BulkProcessContextProvider, {
	BulkProcessContext,
} from "../../contexts/BulkProcessContext";
import UserContextProvider from "../../contexts/UserContext";

// import BulkPrepare from "./Prepare";
// import BulkInProcess from "./InProcess";
// import BulkLastProcess from "./LastProcess";

function BulkWithProviders() {
	const { state } = useContext(BulkProcessContext);

	// if (state.bulkActive) {
	// 	return <BulkInProcess />;
	// }

	return (
		<>
			Bulk bobby
			{/* {!isNil(state.lastProcess) && <BulkLastProcess />}

			<BulkPrepare /> */}
		</>
	);
}

const Bulk = () => {
	return (
		<BulkSettingsContextProvider>
			<BulkProcessContextProvider>
				<UserContextProvider
					initialState={{
						default_language_ia: get(
							//@ts-ignore
							IMAGESEO_DATA,
							"OPTIONS.default_language_ia",
							null
						),
						//@ts-ignore
						user_infos: get(IMAGESEO_DATA, "USER_INFOS", null),
						//@ts-ignore
						limit_images: get(IMAGESEO_DATA, "LIMIT_IMAGES", null),
					}}
				>
					<BulkWithProviders />
				</UserContextProvider>
			</BulkProcessContextProvider>
		</BulkSettingsContextProvider>
	);
};

export default Bulk;
