import React from "react";
import { PageContextProvider } from "../contexts/PageContext";
import SideNavigation from "../layouts/side-navigation";
import Main from "../layouts/main";
import GlobalStyle from "../layouts/global-style";

const Application = () => {
	return (
		<PageContextProvider>
			<GlobalStyle />
			<SideNavigation />
			<Main />
		</PageContextProvider>
	);
};

export default Application;
