import auth from './auth';

export default angular.module('share.common.services', [])
	.factory('shareServicesAuth', auth)
	.name;
