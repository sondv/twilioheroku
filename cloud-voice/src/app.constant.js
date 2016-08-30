var config = {};
switch (process.env.NODE_ENV) {
	case 'production':
		config = {
			assetsBasePath: 'https://assets.joincallwell.com/',
			resourceBasePath: 'https://resource.joincallwell.com/'
		}
		break;
	case 'development':
	default:
		config = {
			assetsBasePath: '/assets/',
			resourceBasePath: '/resource/'
		}
		break;
}

export default config;
