import React from "react";
import styled from "styled-components";

const SCModalContainerContent = styled.div`
	background-color: var(--white);
	padding: 15px 30px;
	border-bottom-left-radius: 8px;
	border-bottom-right-radius: 8px;
`;

function ModalContent({ children }) {
	return <SCModalContainerContent>{children}</SCModalContainerContent>;
}

export default ModalContent;
