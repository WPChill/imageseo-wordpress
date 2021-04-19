import React from "react";
import ReactDOM from "react-dom";
import "@babel/polyfill/noConflict";
import DeactivateIntent from "./modules/DeactivateIntent";

const element = document.querySelector(
	`.plugins [data-slug="imageseo"] .deactivate`
);

const selector = document.querySelector("#deactivate-intent-imageseo");

if (element && selector) {
	ReactDOM.render(
		<DeactivateIntent />,
		document.querySelector("#deactivate-intent-imageseo")
	);
}
