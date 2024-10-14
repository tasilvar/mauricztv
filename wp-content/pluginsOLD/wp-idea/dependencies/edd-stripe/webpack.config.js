/**
 * WordPress dependencies
 */
const defaultConfig = require( '@wordpress/scripts/config/webpack.config.js' );

/**
 * External dependencies
 */
const webpack = require( 'webpack' );
const MiniCssExtractPlugin = require( 'mini-css-extract-plugin' );
const FixStyleOnlyEntriesPlugin = require( 'webpack-fix-style-only-entries' );
const OptimizeCssAssetsPlugin = require( 'optimize-css-assets-webpack-plugin' );

module.exports = {
	...defaultConfig,
	devtool: 'source-map',
	// Override externals so dependencies can be packaged with the assets
	// because the minimum WordPress version is still 4.9.
	externals: {
		jquery: 'jQuery',
		$: 'jQuery',
	},
	resolve: {
		...defaultConfig.resolve,
		modules: [
			`${ __dirname }/assets/js/src`,
			'node_modules',
		],
	},
	entry: {
		// JS.
		app: './assets/js/src/frontend',
		admin: './assets/js/src/admin/index.js',
		notices: './assets/js/src/admin/notices.js', // Separately to be enqueued on all pages.

		// CSS
		style: './assets/css/src/style.css',
		'admin-style': './assets/css/src/admin.css',
	},
	module: {
		rules: [
			...defaultConfig.module.rules,
			{
				test: /\.css$/,
				use: [
					MiniCssExtractPlugin.loader,
					{
						loader: 'css-loader',
						options: {
							importLoaders: 1,
						},
					},
					{
						loader: 'postcss-loader',
						options: {
							plugins: () => [
								require( 'autoprefixer' ),
							],
						},
					},
				],
			},
		],
	},
	output: {
		filename: 'assets/js/build/[name].min.js',
		path: __dirname,
	},
	plugins: [
		new FixStyleOnlyEntriesPlugin(),
		new OptimizeCssAssetsPlugin(),
		new webpack.ProvidePlugin( {
			'Promise.default': 'promise-polyfill',
			$: 'jquery',
		} ),
		new MiniCssExtractPlugin( {
			filename: 'assets/css/build/[name].min.css',
		} ),
	],
};
