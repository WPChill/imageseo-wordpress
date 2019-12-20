const path = require("path");
const ExtractTextPlugin = require("extract-text-webpack-plugin");
const CopyWebpackPlugin = require("copy-webpack-plugin");
const webpack = require("webpack");
const env = process.env.NODE_ENV || "development";

module.exports = {
	entry: {
		"media-upload": "./app/javascripts/media-upload.js",
		bulk: "./app/javascripts/react/index.js",
		"bulk-optimization": "./app/javascripts/bulk-optimization.js",
		"api-key": "./app/javascripts/api-key.js",
		register: "./app/javascripts/register.js",
		"admin-bar": "./app/javascripts/admin-bar.js",
		"admin-css": "./app/styles/admin.scss"
	},
	output: {
		path: __dirname + "/dist",
		publicPath: "/dist/"
	},
	mode: env,
	module: {
		rules: [
			{
				test: /\.(js|jsx)$/,
				exclude: /node_modules/,
				use: "babel-loader"
			},
			{
				test: /\.(gif|jpe?g|png)$/,
				loader: "url-loader",
				query: {
					limit: 10000,
					name: "images/[name].[ext]"
				}
			},
			{
				test: /\.scss$/,
				use: ExtractTextPlugin.extract({
					fallback: "style-loader",
					use: [
						{
							loader: "css-loader?url=false"
						},
						{
							loader: "sass-loader"
						},
						{
							loader: "postcss-loader"
						}
					]
				})
			}
		]
	},
	plugins: [
		new ExtractTextPlugin({
			filename: "css/[name].css"
		}),
		new CopyWebpackPlugin([{ from: "app/images", to: "images" }])
	],
	resolve: {
		extensions: [".js", ".jsx"]
	}
};
