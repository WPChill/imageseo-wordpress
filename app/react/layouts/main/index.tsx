import React from "react";
import styled from "styled-components";
import {
	useMainContentSelected,
	usePageTitle,
} from "../../contexts/PageContext";
import { MAIN_CONTENT } from "../../constants/app-tabs";
import Overview from "../../modules/overview";
import Bulk from "../../modules/bulk";
import SocialMedia from "../../modules/social";
import Settings from "../../modules/settings";

const SCMain = styled.div`
	width: 100%;
	@media (min-width: 1200px) {
		${(props) =>
			//@ts-ignore
			props.mainContentSelected === MAIN_CONTENT.SOCIAL_CARD
				? `width:98%`
				: `width:70%`}
	}
	background-color: #fff;
	padding: 36px;
	padding-bottom: 20px;
	border-radius: 8px;
`;

function Main() {
	const pageTitle = usePageTitle();
	const mainContentSelected = useMainContentSelected();

	return (
		<SCMain
			className="mx-auto my-4 mb-8"
			//@ts-ignore
			mainContentSelected={mainContentSelected}
		>
			<h2 className="font-bold text-xl border-b pb-4">
				<div dangerouslySetInnerHTML={{ __html: pageTitle }} />
			</h2>

			<div className="py-4">
				{(() => {
					switch (mainContentSelected) {
						case MAIN_CONTENT.OVERVIEW:
							return <Overview />;
						case MAIN_CONTENT.SETTINGS:
							return <Settings />;
						case MAIN_CONTENT.BULK_OPTIMIZATION:
							return <Bulk />;
						case MAIN_CONTENT.SOCIAL_CARD:
							return <SocialMedia />;
						default:
							return null;
					}
				})()}
			</div>
		</SCMain>
	);
}

export default Main;
