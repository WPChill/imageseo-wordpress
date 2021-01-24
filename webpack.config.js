const path = require("path");
const ExtractTextPlugin = require("extract-text-webpack-plugin");
const CopyWebpackPlugin = require("copy-webpack-plugin");
const webpack = require("webpack");
const env = process.env.NODE_ENV || "development";

module.exports = {
	entry: {
		application: "./app/react/modules/index.tsx",
		"media-upload": "./app/javascripts/media-upload.js",
		"deactivate-intent":
			"./app/javascripts/react/deactivate-intent.module.js",
		"generate-social-media": "./app/javascripts/generate-social-media.js",
		"admin-bar": "./app/javascripts/admin-bar.js",
		"admin-css": "./app/styles/admin.scss",
		"admin-global-css": "./app/styles/admin-global.scss",
	},
	output: {
		path: __dirname + "/dist",
		publicPath: "/dist/",
	},
	mode: env,
	module: {
		rules: [
			{
				test: /\.ts(x?)$/,
				exclude: /node_modules/,
				use: [
					{
						loader: "ts-loader",
					},
				],
			},
			{
				test: /\.(js|jsx)$/,
				exclude: /node_modules/,
				use: "babel-loader",
			},
			{
				test: /\.(gif|jpe?g|png)$/,
				loader: "url-loader",
				query: {
					limit: 10000,
					name: "images/[name].[ext]",
				},
			},
			{
				test: /\.scss$/,
				use: ExtractTextPlugin.extract({
					fallback: "style-loader",
					use: [
						{
							loader: "css-loader?url=false",
						},
						{
							loader: "sass-loader",
						},
						{
							loader: "postcss-loader",
						},
					],
				}),
			},
		],
	},
	plugins: [
		new ExtractTextPlugin({
			filename: "css/[name].css",
		}),
		new CopyWebpackPlugin([
			{ from: "app/images", to: "images" },
			{ from: "app/fonts", to: "fonts" },
		]),
	],
	resolve: {
		extensions: [".js", ".jsx", ".ts", ".tsx"],
	},
};
