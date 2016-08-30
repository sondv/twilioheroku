// 通常のbundleファイル作成：webpack --json | analyze-bundle-size

var path = require('path');
var webpack = require('webpack');

var PROD = JSON.parse(process.env.PROD_ENV || '0');

module.exports = {
	// set the context (optional)
	context: path.join(__dirname, '/src'),
	entry: {
		manage: 'manage.app.js',
		share: 'share.app.js'
	},
	output: {
		path: __dirname + '/dist',
		filename: '[name]/bundle.js'
	},
	// enable loading modules relatively (without the ../../ prefix)
	resolve: {
		root: path.join(__dirname, '/src')
	},
	module: {
		loaders: [
			// load and compile javascript
			{test: /\.js$/, exclude: /node_modules/, loader: "babel", query: {presets: ['es2015', 'stage-1']}},
			// load css and process less
			{test: /\.css$/, loader: "style!css"},
			// load JSON files and HTML
			{test: /\.json$/, loader: "json"},
			{test: /\.html$/, exclude: /node_modules/, loader: "raw"},
			// load fonts(inline base64 URLs for <=8k)
			{test: /\.(ttf|eot|svg|otf)$/, loader: "file"},
			{test: /\.woff(2)?$/, loader: "url?limit=8192&minetype=application/font-woff"},
			// load images (inline base64 URLs for <=8k images)
			{test: /\.(png|jpg)$/, loader: 'url-loader?limit=8192'}
		]
	},
	plugins: [
		new webpack.NoErrorsPlugin(),
		new webpack.ProvidePlugin({
			$: 'jquery',
			jQuery: 'jquery',
			'window.jQuery': 'jquery',
			'windows.jQuery': 'jquery'
		}),
		new webpack.DefinePlugin({
			'process.env.NODE_ENV': JSON.stringify(process.env.NODE_ENV)
		})
	],
	// webpack dev server configuration
	devServer: {
		contentBase: "./src",
		noInfo: false,
		hot: true,
		watchOptions: {
			aggregateTimeout: 300,
			poll: 5000
		},
		port: 3000,
		host: '0.0.0.0',
		// proxy
		proxy: {
			'/manage/**': {
				target: 'http://127.0.0.1:80'
			},
			'/share/**': {
				target: 'http://127.0.0.1:80'
			},
			'/api/**': {
				target: 'http://127.0.0.1:80'
			},
			'/assets/**': {
				target: 'http://127.0.0.1:80'
			},
			'/resource/**': {
				target: 'http://127.0.0.1:80'
			},
			'/md-calendar.svg': {
				target: 'http://127.0.0.1:80'
			}
		}
	},
	// support source maps
	devtool: "#inline-source-map"
};
