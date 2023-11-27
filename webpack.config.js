const path = require("path");
const MiniCssExtractPlugin = require("mini-css-extract-plugin");
const CopyWebpackPlugin = require("copy-webpack-plugin");
const webpack = require("webpack");
const env = process.env.NODE_ENV || "development";

module.exports = {
	entry: {
		"media-upload": "./app/javascripts/media-upload.js",
		"generate-social-media": "./app/javascripts/generate-social-media.js",
		"admin-bar": "./app/javascripts/admin-bar.js",
		"functionality": "./app/javascripts/functionality.js",
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
			{
				test: /\.css$/,
				use: [
					{
						loader: MiniCssExtractPlugin.loader,
					},
					"css-loader?url=false",
					"postcss-loader",
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
