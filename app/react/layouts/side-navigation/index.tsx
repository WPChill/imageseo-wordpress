import React, { useContext } from "react";
import styled from "styled-components";
import TABS from "../../constants/app-tabs";
import { PageContext, useTabSelected } from "../../contexts/PageContext";
import getLinkImage from "../../helpers/getLinkImage";

interface Props {
	readonly isActive?: boolean;
}

const SCTab = styled.div<Props>`
	display: flex;
	align-items: flex-start;
	padding: 16px;
	justify-content: center;
	@media (min-width: 1200px) {
		padding-right: 10px;
		justify-content: flex-start;
	}
	border-left: 2px solid transparent;
	${({ isActive }) =>
		isActive &&
		`border-left-color: var(--blue);
		background-color:#fff;
	cursor: pointer;`}

	border-bottom: 1px solid #f1f1f1;
	&:first-child {
		border-top: 1px solid #f1f1f1;
	}
	transition: all 0.2s ease-in;
	&:hover {
		cursor: pointer;
	}
`;

const SCTabIndicator = styled.div<Props>`
	width: 24px;
	height: 24px;
	margin: 9px 16px 7px 0;
	border-radius: 12px;
	background-color: var(--grey-dark);
	${({ isActive }) => isActive && `background-color: var(--blue);`}
`;

const SCTabContainer = styled.div`
	@media (min-width: 1200px) {
		min-width: 250px;
		width: 15%;
	}
`;

const SCTabContent = styled.div`
	display: none;
	margin-left: 16px;
	@media (min-width: 1200px) {
		display: block;
	}
`;

const SCTabIcon = styled.img`
	@media (min-width: 1200px) {
		margin-right: 15px;
	}
`;

function SideNavigation() {
	const { actions } = useContext(PageContext);
	const tabSelected = useTabSelected();

	return (
		<SCTabContainer>
			<div
				className="flex items-center justify-center"
				style={{
					paddingTop: 16,
					paddingBottom: 16,
				}}
			>
				<img
					src={getLinkImage("logo-blue.svg")}
					style={{ width: 148 }}
				/>
			</div>
			{TABS.map((item) => {
				const isActive = item.key === tabSelected.key;
				return (
					<SCTab
						key={`tab_${item.key}`}
						isActive={isActive}
						onClick={() => actions.setTabSelected(item.key)}
					>
						<SCTabIcon src={getLinkImage(item.icon)} width={20} />
						<SCTabContent>
							<p
								style={{
									margin: 0,
									fontSize: 14,
									fontWeight: "bold",
									textTransform: "uppercase",
									color: isActive ? `var(--blue)` : `#00081a`,
								}}
							>
								{item.label}
							</p>
							<p
								className="mt-5"
								style={{
									margin: 0,
									marginTop: 2,
									color: "#4c525e",
								}}
							>
								{item.description}
							</p>
						</SCTabContent>
					</SCTab>
				);
			})}
		</SCTabContainer>
	);
}

export default SideNavigation;
