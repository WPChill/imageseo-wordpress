import React from "react";
import { AlertSimple, IconsAlert } from "../Alerts/Simple";

const NeedConnectedApi = () => {
	return (
		<AlertSimple icon={IconsAlert.INFORMATION} blue>
			<p>You need to connect your API Key to access the bulk</p>
		</AlertSimple>
	);
};

export default NeedConnectedApi;
