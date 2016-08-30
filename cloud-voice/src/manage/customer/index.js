import CustomerListCtrl from './list';
import CustomerFormCtrl from './form';
import CustomerInfoCtrl from './info';
import CustomerHearingCtrl from './hearing';
import CustomerConditionCtrl from './condition';

routing.$inject = ['$stateProvider'];

function routing($stateProvider) {
    $stateProvider
        .state('customerList', {
            url: '/customer/',
            template: require('./templates/list.html'),
            controller: 'CustomerListCtrl',
            controllerAs: 'ctrl'
        })
        .state('customerHearing', {
            url: '/customer/hearing/{customerId:int}/{hearingAt:string}',
            template: require('./templates/hearing.html'),
            controller: 'CustomerHearingCtrl',
            controllerAs: 'ctrl'
        })
        .state('customerCondition', {
            url: '/customer/condition/{customerId:int}',
            template: require('./templates/condition.html'),
            controller: 'CustomerConditionCtrl',
            controllerAs: 'ctrl'
        });
}

export default angular.module('app.customer', [])
    .config(routing)
    .controller('CustomerListCtrl', CustomerListCtrl)
    .controller('CustomerFormCtrl', CustomerFormCtrl)
    .controller('CustomerInfoCtrl', CustomerInfoCtrl)
    .controller('CustomerHearingCtrl', CustomerHearingCtrl)
    .controller('CustomerConditionCtrl', CustomerConditionCtrl)
    .name;
