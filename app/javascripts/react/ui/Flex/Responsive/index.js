import * as React from "react";
import styled from "styled-components";
import { isNil } from "lodash";

const SCResponsive = styled.div`
	${props => !isNil(props["hide"]) && `display:none;`}

	@media (min-width: 576px) {
		${props => !isNil(props["hide-xs"]) && `display:none;`}
		${props => !isNil(props["show-xs"]) && `display:block;`}
	}
	@media (min-width: 768px) {
		${props => !isNil(props["hide-sm"]) && `display:none;`}
		${props => !isNil(props["show-sm"]) && `display:block;`}
	}
	@media (min-width: 960px) {
		${props => !isNil(props["hide-md"]) && `display:none;`}
		${props => !isNil(props["show-md"]) && `display:block;`}
	}
	@media (min-width: 1140px) {
		${props => !isNil(props["hide-lg"]) && `display:none;`}
		${props => !isNil(props["show-lg"]) && `display:block;`}
	}
`;

export default SCResponsive;
