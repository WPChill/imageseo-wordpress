import React from "react";
import styled from "styled-components";

const SCModalContainerTitle = styled.div`
	background-color: var(--grey-light);
	padding: 15px;
	padding-right: 55px;
	text-align: center;
	border-top-right-radius: 8px;
	border-top-left-radius: 8px;
`;

function ModalTitle({ children, ...rest }) {
	return <SCModalContainerTitle {...rest}>{children}</SCModalContainerTitle>;
}

export default ModalTitle;
