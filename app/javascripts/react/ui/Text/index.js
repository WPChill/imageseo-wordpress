import React from "react";
import styled from "styled-components";

const SCText = styled.p`
    margin: 0;
    line-height: 1.5;
    color: var(--black-base);
    font-size: 13px;
    ${props => props.error && `color:var(--red-error);`}
    ${props => props.light && `color:#6a6b6e;`}
    ${props => props.blue && `color:#2b68d9;`}
    ${props => props.white && `color:var(--white);`}
    ${props => props.bold && `font-weight:bold;`}
    ${props =>
		props.withIcon &&
		`display:flex; align-items:center; img {
        margin-right:5px;
    }`}
`;

function Text({ children, ...rest }) {
	return <SCText {...rest}>{children}</SCText>;
}

export default React.memo(Text);
