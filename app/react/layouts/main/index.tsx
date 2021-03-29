import React, { useContext } from "react";
import styled from "styled-components";
import { isNil, isEmpty } from "lodash";
import {
	useMainContentSelected,
	usePageTitle,
	PageContext,
} from "../../contexts/PageContext";
import { MAIN_CONTENT } from "../../constants/app-tabs";
import Overview from "../../modules/overview";

const SCMain = styled.div`
	width: 85%;
	max-width: calc(85% - 60px);
	background-color: #fff;
	padding: 36px;
	padding-bottom: 20px;
	border-radius: 8px;
`;

function Main() {
	const pageTitle = usePageTitle();
	const mainContentSelected = useMainContentSelected();

	return (
		<SCMain>
			<h2 className="font-bold text-xl border-b pb-4">
				<div dangerouslySetInnerHTML={{ __html: pageTitle }} />
			</h2>

			<div className="py-4">
				{(() => {
					switch (mainContentSelected) {
						case MAIN_CONTENT.OVERVIEW:
							return <Overview />;

						default:
							return null;
					}
				})()}
			</div>
		</SCMain>
	);
}

export default Main;
