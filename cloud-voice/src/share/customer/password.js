/**
 * パスワード入力
 */
export default class CustomerPasswordCtrl {
	static $inject = ['$http', '$stateParams', '$cookies', 'constant', 'shareServicesAuth'];

	/**
	 * コンストラクタ
	 *
	 * @param $http
	 * @param $stateParams
	 * @param $cookies
	 * @param constant
	 * @param shareServicesAuth
	 */
	constructor($http, $stateParams, $cookies, constant, shareServicesAuth) {
		this.$http = $http;
		this.$stateParams = $stateParams;
		this.$cookies = $cookies;
		this.constant = constant;
		this.shareServicesAuth = shareServicesAuth;

		this.password = '';

		if (this.$stateParams.hash === undefined) {
			return;
		}
	}

	/**
	 * 認証
	 */
	accept() {
		this.shareServicesAuth.accept(this.$stateParams.hash, this.password);
	}

}
