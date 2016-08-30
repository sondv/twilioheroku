export default class AuthLoginCtrl {
    static $inject = ['$http', '$mdDialog', '$location', 'manageServicesAuth'];

    /**
     * コンストラクタ
     *
     * @param $http
     * @param $mdDialog
     * @param $location
     * @param manageServicesAuth
     */
    constructor($http, $mdDialog, $location, manageServicesAuth) {
        this.$http = $http;
        this.$mdDialog = $mdDialog;
        this.$location = $location;
        this.manageServicesAuth = manageServicesAuth;
    }

    /**
     * ログイン
     */
    login() {
        this.manageServicesAuth.login(this.login_id, this.password);
    }
}
