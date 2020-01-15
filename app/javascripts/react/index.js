import React from "react";
import ReactDOM from "react-dom";
import Bulk from "./modules/Bulk";
import { ThemeProvider } from "styled-components";
import SocialMedia from "./modules/SocialMedia";

const theme = {
	col: 24,
	gutter: 4
};

document.addEventListener("DOMContentLoaded", () => {
	const element = document.querySelector("#js-module-optimization");

	if (element) {
		ReactDOM.render(
			<ThemeProvider theme={theme}>
				<Bulk />
			</ThemeProvider>,
			element
		);
	}

	const elementSocialMedia = document.querySelector(
		"#js-module-social-media"
	);

	if (elementSocialMedia) {
		ReactDOM.render(
			<ThemeProvider theme={theme}>
				<SocialMedia />
			</ThemeProvider>,
			elementSocialMedia
		);
	}
});
