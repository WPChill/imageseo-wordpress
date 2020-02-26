import React from "react";
import styled from "styled-components";

const SCModalOverlay = styled.div`
	position: fixed;
	background-color: rgba(0, 8, 26, 0.3);
	top: 0;
	left: 0;
	z-index: 90;
	width: 100%;
	height: 100%;
	display: flex;
	padding-top: 80px;
	justify-content: center;
	overflow-y: scroll;
`;

function ModalOverlay({ children }) {
	return (
		<SCModalOverlay>
			{children}
			<style>{`
            body {
                 overflow: hidden; 
            }
            `}</style>
		</SCModalOverlay>
	);
}

export default ModalOverlay;
