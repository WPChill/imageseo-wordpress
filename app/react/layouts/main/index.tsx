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

const SCMainHeader = styled.header`
	display: flex;
	align-items-center;
	padding-bottom: 32px;
	border-bottom: 1px solid var(--theme-ui-grey);
	h2 {
		font-size: 24px;
		color:var(--black-dark);
		margin:0;
	}
`;

function Main() {
	const pageTitle = usePageTitle();
	const mainContentSelected = useMainContentSelected();

	return (
		<SCMain>
			<SCMainHeader>
				<h2>
					<div dangerouslySetInnerHTML={{ __html: pageTitle }} />
				</h2>
			</SCMainHeader>

			{(() => {
				switch (mainContentSelected) {
					case MAIN_CONTENT.OVERVIEW:
						return <Overview />;

					default:
						return null;
				}
			})()}
		</SCMain>
	);
}

export default Main;
