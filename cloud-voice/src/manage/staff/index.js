import StaffListCtrl from './list';
import StaffInfoCtrl from './info';
import StaffFormCtrl from './form';

routing.$inject = ['$stateProvider'];

function routing($stateProvider) {
    $stateProvider
        .state('staffList', {
            url: '/staff/',
            template: require('./templates/list.html'),
            controller: 'StaffListCtrl',
            controllerAs: 'ctrl'
        });
}

export default angular.module('app.staff', [])
    .config(routing)
    .controller('StaffListCtrl', StaffListCtrl)
    .controller('StaffInfoCtrl', StaffInfoCtrl)
    .controller('StaffFormCtrl', StaffFormCtrl)
    .name;
