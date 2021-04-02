import { isNil } from "lodash";
import React, { useContext } from "react";
import { BulkProcessContext } from "../../contexts/BulkProcessContext";
import BulkInProcess from "./components/InProcess";
import BulkLastProcess from "./components/LastProcess";
import BulkPrepare from "./components/Prepare";

const BulkWithProviders = () => {
	const { state } = useContext(BulkProcessContext);

	if (state.bulkActive) {
		return <BulkInProcess />;
	}
	console.log("state", state);
	return (
		<>
			{!isNil(state.lastProcess) && <BulkLastProcess />}
			{isNil(state.lastProcess) && <BulkPrepare />}
		</>
	);
};

export default BulkWithProviders;
