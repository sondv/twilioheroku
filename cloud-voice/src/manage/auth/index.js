import AuthLoginCtrl from './login';
import AuthLogoutCtrl from './logout';

routing.$inject = ['$stateProvider'];

function routing($stateProvider) {
    $stateProvider
        .state('loginInput', {
            url: '/auth/login/',
            template: require('./templates/login.html'),
            controller: 'AuthLoginCtrl',
            controllerAs: 'ctrl'
        })
        .state('logout', {
            url: '/auth/logout/',
            controller: 'AuthLogoutCtrl',
            controllerAs: 'ctrl'
        });
}

export default angular.module('app.login', [])
    .config(routing)
    .controller('AuthLoginCtrl', AuthLoginCtrl)
    .controller('AuthLogoutCtrl', AuthLogoutCtrl)
    .name;
