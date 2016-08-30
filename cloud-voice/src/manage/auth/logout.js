export default class AuthLogoutCtrl {
    static $inject = ['$location', 'manageServicesAuth'];

    /**
     * コンストラクタ
     *
     * @param $location
     * @param manageServicesAuth
     */
    constructor($location, manageServicesAuth) {
        manageServicesAuth.logout();
    }

}
