import React from "react";
import styled from "styled-components";

const SCImage = styled.img`
	border-radius: 6px;
	width: 75px;
`;
const SCOldValue = styled.div`
	margin-top: 5px;
	font-size: 12px;
	font-style: italic;
`;

function BlockTableLineImage(props) {
	return <SCImage {...props} />;
}

function BlockTableLineOldValue(props) {
	return <SCOldValue {...props} />;
}

function BlockTableLineItem({ children }) {
	return <div className="imageseo-block__table-line__item">{children}</div>;
}
function BlockTableLine({ children }) {
	return <div className="imageseo-block__table-line">{children}</div>;
}

export { BlockTableLineItem, BlockTableLineImage, BlockTableLineOldValue };

export default BlockTableLine;
