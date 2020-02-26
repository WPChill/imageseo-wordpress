import React from "react";
import styled from "styled-components";

const SCCardUser = styled.div`
	border-radius: 8px;
	border: solid 1px var(--grey-dark);
	background-color: var(--grey-light);
	padding-top: 30px;
	padding-right: 15px;
	padding-left: 15px;
	padding-bottom: 15px;
	display: flex;
	flex-direction: column;
	align-items: center;
	text-align: center;

	${props =>
		props.clickable &&
		`
        transition:all .1s ease-in;
        &:hover{
        cursor:pointer;
        background-color:#fff;
        border-color:#2b68d9;
    }`}
`;

function CardUser({ children, ...rest }) {
	return <SCCardUser {...rest}>{children}</SCCardUser>;
}

export default CardUser;
