import React, { useContext } from "react";
import SubTitle from "../../../ui/Block/Subtitle";
import { BulkSettingsContext } from "../../../contexts/BulkSettingsContext";

function BulkSummary() {
	const { settings } = useContext(BulkSettingsContext);
	return (
		<>
			<SubTitle>Configuration of the current optimization</SubTitle>
		</>
	);
}

export default BulkSummary;
