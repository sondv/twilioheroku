import formatPhone from './formatPhone';
import formatPostCode from './formatPostCode';
import toAge from './toAge';
import toJpDate from './toJpDate';
import toJpTime from './toJpTime';

export default angular.module('common.filters', [])
	.filter('formatPhone', formatPhone)
	.filter('formatPostCode', formatPostCode)
	.filter('toAge', toAge)
	.filter('toJpDate', toJpDate)
	.filter('toJpTime', toJpTime)
	.name;
