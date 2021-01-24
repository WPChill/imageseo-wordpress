import React from "react";
import ReactDOM from "react-dom";
import "@babel/polyfill/noConflict";
import { fetchAdminPost } from "../helpers/fetch";
import { SWRConfig } from "swr";
import Application from "./app";

document.addEventListener("DOMContentLoaded", () => {
	const element = document.querySelector("#js-module-imageseo");

	if (element) {
		ReactDOM.render(
			<SWRConfig
				value={{
					fetcher: fetchAdminPost(),
				}}
			>
				<Application />
			</SWRConfig>,
			element
		);
	}
});
