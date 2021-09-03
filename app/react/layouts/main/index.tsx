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
import getLinkImage from "../../helpers/getLinkImage";

const SCMain = styled.div`
	width: 98%;
	@media (min-width: 1200px) {
		${(props) =>
			//@ts-ignore
			props.mainContentSelected === MAIN_CONTENT.SOCIAL_CARD
				? `width:98%`
				: `width:78%`}
	}
	@media (min-width: 1400px) {
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

//@ts-ignore
const { __ } = wp.i18n;

function Main() {
	const pageTitle = usePageTitle();
	const mainContentSelected = useMainContentSelected();

	return (
		<SCMain
			className="mx-auto my-4 mb-8"
			//@ts-ignore
			mainContentSelected={mainContentSelected}
		>
			<div className="border-b pb-4">
				<div className="flex items-center">
					<img
						src={getLinkImage("logo-blue.svg")}
						style={{ width: 148 }}
					/>
					<h2 className="font-bold text-xl flex items-center">
						<span className="mx-4">|</span>{" "}
						<div dangerouslySetInnerHTML={{ __html: pageTitle }} />
					</h2>
				</div>
				<p className="mt-4">
					<strong>
						{__(
							"SEO Fact : More than 20% of Google traffic comes from image searches. We use AI to automatically optimize your images for SEO.",
							"imageseo"
						)}
					</strong>
				</p>
			</div>

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
