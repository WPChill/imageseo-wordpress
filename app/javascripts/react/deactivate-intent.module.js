import React from "react";
import ReactDOM from "react-dom";
import DeactivateIntent from "./modules/DeactivateIntent";

const element = document.querySelector(
	`.plugins [data-slug="imageseo"] .deactivate`
);

if (element) {
	ReactDOM.render(
		<DeactivateIntent />,
		document.querySelector("#deactivate-intent-imageseo")
	);
}
