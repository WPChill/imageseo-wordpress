import React, { useContext } from "react";
import { find, memoize, get } from "lodash";
import { createContext, useState } from "react";
import APP_TABS, { TABS, MAIN_CONTENT } from "../constants/app-tabs";
import { getApiKey } from "../helpers/getApiKey";

type PageContext = {
	values: {
		tabSelected: number;
		mainContentSelected?: number;
		apiKey?: string;
		pageTitle?: string;
		params?: any;
	};
	actions?: {
		setPageTitle?: Function;
		setTabSelected?: Function;
		setMainContentSelected?: Function;
		setApiKey?: Function;
		setParams?: Function;
	};
};

export const PageContext = createContext<PageContext>({
	values: {
		tabSelected: TABS.OVERVIEW,
	},
});

export const getItemTab = memoize((value) => {
	return find(APP_TABS, { key: value });
});

export const useTabSelected = () => {
	const { values } = useContext(PageContext);

	return getItemTab(values.tabSelected);
};

export const useMainContentSelected = () => {
	const { values } = useContext(PageContext);

	return values.mainContentSelected;
};

export const usePageTitle = () => {
	const { values } = useContext(PageContext);

	return values.pageTitle;
};

export const PageContextProvider = ({ children }) => {
	const [tabSelected, setTabSelected] = useState(TABS.OVERVIEW);
	const [mainContentSelected, setMainContentSelected] = useState(
		MAIN_CONTENT.OVERVIEW
	);

	const [pageTitle, setPageTitle] = useState("");
	const [apiKey, setApiKey] = useState(getApiKey());
	const [params, setParams] = useState(null);

	const setApiKeyProcess = (value) => {
		//@ts-ignore
		IMAGESEO.API_KEY = value;
		setApiKey(value);
	};

	/**
	 * Tab on side navigation
	 */
	const setTabSelectedProcess = (value) => {
		const itemTab = getItemTab(value);
		setPageTitle(itemTab.label);
		setTabSelected(value);
		setMainContentSelected(itemTab.mainContent);
	};

	/**
	 * Main Content
	 */
	const setMainContentSelectedProcess = (value, params = null) => {
		setParams(params);
		setMainContentSelected(value);
	};

	return (
		<PageContext.Provider
			value={{
				values: {
					tabSelected,
					mainContentSelected,
					apiKey,
					pageTitle,
					params,
				},
				actions: {
					setPageTitle,
					setTabSelected: setTabSelectedProcess,
					setMainContentSelected: setMainContentSelectedProcess,
					setApiKey: setApiKeyProcess,
					setParams,
				},
			}}
		>
			{children}
		</PageContext.Provider>
	);
};

export default { PageContext, PageContextProvider };
