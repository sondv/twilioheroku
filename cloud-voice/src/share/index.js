import services from './common/services';
import customer from './customer';

export default angular.module('share', [
	services,
	customer
])
	.name;
