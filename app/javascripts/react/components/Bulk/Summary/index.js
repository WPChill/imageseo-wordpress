import { find } from "lodash";
import React, { useContext } from "react";
import SubTitle from "../../../ui/Block/Subtitle";
import { BulkSettingsContext } from "../../../contexts/BulkSettingsContext";

function BulkSummary() {
	const { state: settings } = useContext(BulkSettingsContext);
	return (
		<>
			<SubTitle>Configuration of the current optimization</SubTitle>
			<ul>
				<li>
					<strong>Manual Validation :</strong>{" "}
					{settings.wantValidateResult ? "Yes" : "No"}
				</li>
				<li>
					<strong>Language :</strong>{" "}
					{
						find(IMAGESEO_DATA.LANGUAGES, {
							code: settings.language
						}).name
					}
				</li>
				<li>
					<strong>Optimize alt :</strong>{" "}
					{settings.optimizeAlt ? "Yes" : "No"}{" "}
					{settings.optimizeAlt && (
						<>
							(Format :{" "}
							{settings.formatAlt === "CUSTOM_FORMAT"
								? settings.formatAltCustom
								: settings.formatAlt}
							)
						</>
					)}
				</li>
			</ul>
		</>
	);
}

export default BulkSummary;
