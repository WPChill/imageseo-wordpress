const path = require("path");
const MiniCssExtractPlugin = require("mini-css-extract-plugin");
const CopyWebpackPlugin = require("copy-webpack-plugin");
const webpack = require("webpack");
const env = process.env.NODE_ENV || "development";

module.exports = {
	entry: {
		bulk: "./app/javascripts/react/index.js",
		"media-upload": "./app/javascripts/media-upload.js",
		"deactivate-intent":
			"./app/javascripts/react/deactivate-intent.module.js",
		"generate-social-media": "./app/javascripts/generate-social-media.js",
		"api-key": "./app/javascripts/api-key.js",
		register: "./app/javascripts/register.js",
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
				test: /\.(js|jsx)$/,
				exclude: /node_modules/,
				use: "babel-loader",
			},
			{
				test: /\.(gif|jpe?g|png)$/,
				loader: "url-loader",
			},
			{
				test: /\.(eot|woff|woff2|svg|ttf)([\?]?.*)$/,
				use: [
					{
						loader: "url-loader?limit=100000",
					},
				],
			},
			{
				test: /\.scss$/,
				use: [
					{
						loader: "file-loader",
						options: {
							name: "css/[name].css",
						},
					},
					{
						loader: "extract-loader",
					},
					{
						loader: "css-loader",
					},

					{
						loader: "resolve-url-loader",
					},
					{
						loader: "sass-loader",
					},
				],
			},
		],
	},
	plugins: [
		new webpack.DefinePlugin({
			NODE_ENV: env,
		}),
		new MiniCssExtractPlugin({
			filename: "css/[name].css",
		}),
		new CopyWebpackPlugin({
			patterns: [
				{ from: "app/images", to: "images" },
				{ from: "app/fonts", to: "fonts" },
			],
		}),
	],
	resolve: {
		extensions: [".js", ".jsx", ".ts", ".tsx"],
	},
};
