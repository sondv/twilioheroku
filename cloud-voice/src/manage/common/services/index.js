import auth from './auth';
import twilio from './twilio';

export default angular.module('manage.common.services', [])
	.factory('manageServicesAuth', auth)
	.service('manageServicesTwilio', twilio)
	.name;
