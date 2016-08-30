import ShareCustomerPasswordCtrl from './password';
import ShareCustomerTopCtrl from './top';
import ShareCustomerHearingCtrl from './hearing';

routing.$inject = ['$stateProvider'];

function routing($stateProvider) {
    $stateProvider
        .state('shareCustomerPassword', {
            url: '/customer/password/:hash',
            template: require('./templates/password.html'),
            controller: 'ShareCustomerPasswordCtrl',
            controllerAs: 'ctrl'
        })
		.state('shareCustomerTopCtrl', {
            url: '/customer/top/:hash',
            template: require('./templates/top.html'),
            controller: 'ShareCustomerTopCtrl',
            controllerAs: 'ctrl'
        })
		.state('shareCustomerHearing', {
            url: '/customer/hearing/:hash/:date',
            template: require('./templates/hearing.html'),
            controller: 'ShareCustomerHearingCtrl',
            controllerAs: 'ctrl'
        })
}

export default angular.module('app.share.customer', [])
    .config(routing)
    .controller('ShareCustomerPasswordCtrl', ShareCustomerPasswordCtrl)
	.controller('ShareCustomerTopCtrl', ShareCustomerTopCtrl)
	.controller('ShareCustomerHearingCtrl', ShareCustomerHearingCtrl)
    .name;
