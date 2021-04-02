import React from "react";
import { AlertSimple, IconsAlert } from "../Alerts/Simple";

//@ts-ignore
const { __ } = wp.i18n;

const SeoFact = () => (
	<AlertSimple icon={IconsAlert.INFORMATION} blue>
		<p className="imageseo-mb-0">
			<strong>
				{__(
					"SEO Fact : More than 20% of Google Organic Traffic comes from image searches",
					"imageseo"
				)}
			</strong>
		</p>
		<p>
			{__(
				"Start optimizing your image alt texts and names and grow your traffic!",
				"imageseo"
			)}
		</p>
	</AlertSimple>
);

export default SeoFact;
