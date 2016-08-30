/**
 * サイドナビ
 */
export default class SideNavCtrl {
    static $inject = ['manageServicesAuth', 'constant'];

    /**
     * コンストラクタ
     */
    constructor(manageServicesAuth, constant) {
        this.staff = manageServicesAuth.getLoginStaff();
        this.constant = constant;
    }
}
