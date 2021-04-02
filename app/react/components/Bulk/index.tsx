import { isNil } from "lodash";
import React, { useContext } from "react";
import { BulkProcessContext } from "../../contexts/BulkProcessContext";
import { AlertSimple, IconsAlert } from "../Alerts/Simple";
import BulkInProcess from "./components/InProcess";
import BulkLastProcess from "./components/LastProcess";
import BulkPrepare from "./components/Prepare";
import ViewDataBulk from "./components/ViewData";

//@ts-ignore
const { __ } = wp.i18n;

const BulkWithProviders = () => {
	const { state } = useContext(BulkProcessContext);

	if (state.bulkActive) {
		return <BulkInProcess />;
	}

	return (
		<>
			{!isNil(state.lastProcess) && (
				<div className="mb-4">
					<BulkLastProcess />
				</div>
			)}

			<div className="mb-4">
				<AlertSimple icon={IconsAlert.INFORMATION} blue>
					<h3 className="text-lg leading-6 font-medium text-gray-900">
						{__("Configuration", "imageseo")}
					</h3>
					<p className="mt-2 max-w-4xl text-sm text-gray-500">
						{__(
							"Prepare the configuration of your bulk optimization",
							"imageseo"
						)}
					</p>
				</AlertSimple>
			</div>
			{isNil(state.currentProcess) && <BulkPrepare />}
		</>
	);
};

export default BulkWithProviders;
