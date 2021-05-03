import React from "react";
import ReactDOM from "react-dom";
import "@babel/polyfill/noConflict";
import { fetchAdminPost } from "../helpers/fetch";
import { SWRConfig } from "swr";
import { createGlobalStyle } from "styled-components";
import WizardWindow from "./wizard-window";
import { PageContextProvider } from "../contexts/PageContext";

const GlobalStyle = createGlobalStyle`
    #wpadminbar,
	#adminmenuwrap{
		display:none;
	}
	.swal2-container{
		z-index:9999 !important;
	}
`;

document.addEventListener("DOMContentLoaded", () => {
	const element = document.querySelector("#js-module-imageseo-wizard");

	if (element) {
		ReactDOM.render(
			<SWRConfig
				value={{
					fetcher: fetchAdminPost(),
				}}
			>
				<PageContextProvider>
					<GlobalStyle />
					<div
						className="fixed top-0 left-0 bg-gray-50 w-full h-full overflow-y-scroll"
						style={{
							zIndex: 9998,
						}}
					>
						<WizardWindow />
					</div>
				</PageContextProvider>
			</SWRConfig>,
			element
		);
	}
});
