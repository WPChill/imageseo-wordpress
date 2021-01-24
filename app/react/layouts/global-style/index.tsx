import { createGlobalStyle } from "styled-components";

const GlobalStyle = createGlobalStyle`

	:root {
		--theme-ui-title: #1a202c;
		--theme-ui-text: #1a202c;
		--theme-ui-background: hsl(0, 0%, 100%);
		--theme-ui-primary: #3371ff;
		--theme-ui-secondary: #7790cc;
		--theme-ui-white: #fff;
		--theme-ui-blue: #0070f3;
		--theme-ui-grey: #eaeaea;

		--white: #fff;
		--grey-light: #fafafc;
		--grey-base: #f5f5fa;
		--grey-dark: #dadae0;
		--grey-shady: #a0a0a3;
		--black-light: #0e214d;
		--black-base: #08142e;
		--black-dark: #00081a;
		--red-light: #fab1a0;
		--red-base: #fa7455;
		--red-dark: #402d29;
		--blue-light: #abe0ff;
		--blue:#2b68d9;
		--blue-degraded: #179ae6;
		--blue-base: #179ae5;
		--blue-dark: #0a4566;
		--blue-shady: #08142e;
		--purple-light: #bbbdf2;
		--purple-base: #3139cc;
		--purple-dark: #151959;
		--yellow-light: #ffeaa7;
		--yellow-base: #ffd140;
		--yellow-dark: #4d3f13;
		--blizzard-light: #99ece5;
		--blizzard-base: #04edda;
		--blizzard-dark: #014d46;
		--red-error: #e64c2e;

		--shadow: 0 5px 10px rgba(0, 0, 0, 0.12);
		--shadow-border: 0 0 0 1px rgba(89, 105, 128, 0.1),
			0 1px 3px 0 rgba(89, 105, 128, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.05);
	}
`;

export default GlobalStyle;
