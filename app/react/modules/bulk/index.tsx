import React, { Suspense } from "react";
import { get, isNil } from "lodash";

//@ts-ignore
const { __ } = wp.i18n;

import BulkSettingsContextProvider from "../../contexts/BulkSettingsContext";
import BulkProcessContextProvider from "../../contexts/BulkProcessContext";
import UserContextProvider from "../../contexts/UserContext";
import BulkWithProviders from "../../components/Bulk";
import useOwner from "../../hooks/useOwner";
import NeedConnectedApi from "../../components/NeedConnectedApi";
import LimitExcedeed from "../../components/Bulk/components/LimitExcedeed";

const BulkWithOwner = () => {
	const owner = useOwner();

	//@ts-ignore
	const limitExcedeed = get(IMAGESEO_DATA, "LIMIT_EXCEDEED", false)
		? true
		: false;

	if (isNil(owner)) {
		return <NeedConnectedApi />;
	}

	if (limitExcedeed) {
		return <LimitExcedeed typeMessage="other" />;
	}

	return <BulkWithProviders />;
};

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
						user_infos: get(IMAGESEO_DATA, "USER", null),
						//@ts-ignore
						limit_images: get(IMAGESEO_DATA, "LIMIT_IMAGES", null),
					}}
				>
					<Suspense
						fallback={
							<>
								<div className="mt-10">
									Data is current loading
								</div>
							</>
						}
					>
						<BulkWithOwner />
					</Suspense>
				</UserContextProvider>
			</BulkProcessContextProvider>
		</BulkSettingsContextProvider>
	);
};

export default Bulk;
