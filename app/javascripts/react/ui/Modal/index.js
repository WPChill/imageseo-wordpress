import React from "react";
import styled from "styled-components";

const SCModalContainer = styled.div`
	position: relative;
	width: 770px;
	margin-bottom: 80px;
	border-radius: 8px;
	box-shadow: 0 6px 12px 0 rgba(0, 8, 26, 0.15);
	background-color: #ffffff;
`;

function Modal({ children, ...rest }) {
	return <SCModalContainer {...rest}>{children}</SCModalContainer>;
}

export default Modal;
