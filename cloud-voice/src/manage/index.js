import controllers from './common/controllers';
import directives from './common/directives';
import services from './common/services';
import auth from './auth';
import staff from './staff';
import customer from './customer';

export default angular.module('manage', [
	controllers,
	directives,
	services,
	auth,
	staff,
	customer
])
	.name;
