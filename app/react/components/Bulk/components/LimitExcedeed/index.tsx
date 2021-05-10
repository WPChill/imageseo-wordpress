import React from "react";
import { AlertSimple, IconsAlert } from "../../../Alerts/Simple";

//@ts-ignore
const { __ } = wp.i18n;

function LimitExcedeed({ typeMessage = "resume" }) {
	return (
		<AlertSimple icon={IconsAlert.EXCLAMATION} yellow>
			<p className="text-sm font-bold">
				{typeMessage === "resume" &&
					__("You need more credits to resume it.", "imageseo")}
				{typeMessage === "other" &&
					__(
						"You must have credits available to use bulk optimization.",
						"imageseo"
					)}
			</p>
			<a
				href="https://app.imageseo.io/plan"
				target="_blank"
				className="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-yellow-600 hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 mt-4"
			>
				{__("Get more credits", "imageseo")}
			</a>
		</AlertSimple>
	);
}

export default LimitExcedeed;
