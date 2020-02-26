import React from "react";
import styled from "styled-components";

const SCTitle = styled.h2`
	font-size: 20px;
	font-weight: bold;
	color: var(--black-base);
	${props => props.white && `color: var(--white);`}
	${props => props.small && `font-size:18px;`}
`;

function Title({ children, ...rest }) {
	return <SCTitle {...rest}>{children}</SCTitle>;
}

export default React.memo(Title);
