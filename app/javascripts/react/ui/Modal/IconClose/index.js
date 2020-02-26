import React from "react";
import styled from "styled-components";

const SCModalIconClose = styled.div`
	position: absolute;
	top: 30px;
	right: 30px;
	width: 28px;
	height: 28px;
	img {
		width: 100% !important;
	}
	&:hover {
		cursor: pointer;
	}
`;

function ModalIconClose({ children, ...rest }) {
	return <SCModalIconClose {...rest}>{children}</SCModalIconClose>;
}

export default ModalIconClose;
