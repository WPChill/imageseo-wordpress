import React from "react";
import styled from "styled-components";

const SCLabelContainer = styled.label`
	margin-top: 15px;
	display: flex;
	align-items: center;
	&:hover {
		cursor: pointer;
	}
`;

function Checkbox({
	children,
	name,
	error = false,
	large = false,
	style = {},
	...rest
}) {
	return (
		<SCLabelContainer htmlFor={name} style={style}>
			<input
				type="checkbox"
				id={name}
				name={name}
				{...rest}
				style={{ marginRight: 10 }}
			/>
			{children}
		</SCLabelContainer>
	);
}

export default Checkbox;
