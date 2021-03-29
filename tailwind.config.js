const colors = require("tailwindcss/colors");
module.exports = {
	purge: [
		"./app/react/**/*.js",
		"./app/react/**/*.ts",
		"./app/react/**/*.tsx",
	],
	theme: {
		extend: {
			colors: {
				"light-blue": colors.lightBlue,
				teal: colors.teal,
				cyan: colors.cyan,
				rose: colors.rose,
				orange: colors.orange,
				blue: colors.blue,
				pink: colors.pink,
			},
		},
	},
	variants: {
		extend: {},
	},
	plugins: [require("@tailwindcss/forms")],
};
