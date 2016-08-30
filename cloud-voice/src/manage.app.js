import uiRouter from 'angular-ui-router';
import cookies from 'angular-cookies';

import 'angular-material/angular-material.min.css';
import 'lf-ng-md-file-input/dist/lf-ng-md-file-input.min.css';
import 'lf-ng-md-file-input/dist/lf-ng-md-file-input.js';

import constant from './app.constant';
import providerInjector from './app.provider';
import instanceInjector from './manage.app.instance';
import directives from './common/directives';
import filters from './common/filters';
import services from './common/services';
import manage from './manage';

angular.module('app', [
	uiRouter,
	cookies,
	directives,
	filters,
	services,
	'angularMoment',
	'lfNgMdFileInput',
	manage
])
	.constant('constant', constant)
	.config(providerInjector)
	.run(instanceInjector);
