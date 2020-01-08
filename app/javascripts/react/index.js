import React from "react";
import ReactDOM from "react-dom";
import Bulk from "./modules/Bulk";
import { ThemeProvider } from "styled-components";

const theme = {
	col: 24,
	gutter: 4
};

document.addEventListener("DOMContentLoaded", () => {
	const element = document.querySelector("#js-module-optimization");

	ReactDOM.render(
		<ThemeProvider theme={theme}>
			<Bulk />
		</ThemeProvider>,
		element
	);
});
