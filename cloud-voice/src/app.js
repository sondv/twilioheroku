import angular from 'angular';
import uiRouter from 'angular-ui-router';
import animate from 'angular-animate';
import aria from 'angular-aria';
import material from 'angular-material';
import cookies from 'angular-cookies';

import 'angular-material/angular-material.css';
import 'lf-ng-md-file-input/dist/lf-ng-md-file-input.min.css';
import 'lf-ng-md-file-input/dist/lf-ng-md-file-input.js';
import 'moment/moment.js';
import 'moment/locale/ja.js';
import 'angular-moment/angular-moment.js';
import 'chart.js/src/Chart.js'

import styles from './app.css';
import providerInjector from './app.provider';
import instanceInjector from './app.instance';
import controllers from './common/controllers';
import directives from './common/directives';
import filters from './common/filters';
import services from './common/services';
import manage from './manage';
import share from './share';

angular.module('app', [
    uiRouter,
    animate,
    aria,
    material,
    cookies,
    controllers,
    directives,
    filters,
    services,
    'angularMoment',
	'lfNgMdFileInput',
    manage,
    share
])
    .config(providerInjector)
    .run(instanceInjector);
