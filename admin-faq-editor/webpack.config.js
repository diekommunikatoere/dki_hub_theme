const path = require("path");
const MiniCssExtractPlugin = require("mini-css-extract-plugin");

module.exports = (env, argv) => {
	const isProduction = argv.mode === "production";

	return {
		entry: "./src/index.tsx",
		output: {
			path: path.resolve(__dirname, "../includes/assets"),
			filename: "js/admin/faq-editor/faq-editor.js",
			clean: false,
		},
		resolve: {
			extensions: [".tsx", ".ts", ".js", ".jsx"],
		},
		module: {
			rules: [
				{
					test: /\.(ts|tsx)$/,
					use: [
						{
							loader: "babel-loader",
							options: {
								presets: ["@babel/preset-react", "@babel/preset-typescript"],
							},
						},
					],
					exclude: /node_modules/,
				},
				{
					test: /\.s[ac]ss$/i,
					use: [isProduction ? MiniCssExtractPlugin.loader : "style-loader", "css-loader", "sass-loader"],
				},
				{
					test: /\.css$/i,
					use: [isProduction ? MiniCssExtractPlugin.loader : "style-loader", "css-loader"],
				},
			],
		},
		plugins: isProduction
			? [
					new MiniCssExtractPlugin({
						filename: "css/modules/admin/faq-editor.css",
					}),
			  ]
			: [],
		devtool: isProduction ? "source-map" : "cheap-module-source-map",
		externals: {
			react: "React",
			"react-dom": "ReactDOM",
			"@wordpress/api-fetch": "wp.apiFetch",
			"@wordpress/i18n": "wp.i18n",
		},
	};
};
