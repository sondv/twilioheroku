/**
 * 聞き取り
 */
export default class CustomerHearingCtrl {
	static $inject = ['$http', '$stateParams', '$location', 'constant', 'moment', 'systemService', 'shareServicesAuth'];

	/**
	 * コンストラクタ
	 *
	 * @param $http
	 * @param $stateParams
	 * @param $location
	 * @param constant
	 * @param moment
	 * @param systemService
	 * @param shareServicesAuth
	 */
	constructor($http, $stateParams, $location, constant, moment, systemService, shareServicesAuth) {
		this.$http = $http;
		this.$stateParams = $stateParams;
		this.$location = $location;
		this.constant = constant;
		this.moment = moment;
		this.systemService = systemService;
		this.shareServicesAuth = shareServicesAuth;

		this.customer = null;
		this.hearings = null;

		// URL受け入れ確認
		if (this.shareServicesAuth.isAccept(this.$stateParams.hash)) {
			this.load();
		}
	}

	/**
	 * 読み込み
	 */
	load() {
		if (this.$stateParams.hash === undefined) {
			return;
		}

		this.loadCustomer();
		this.loadHearings();
	}

	/**
     * 利用者の取得
     */
    loadCustomer() {
        let self = this;
		this.$http.get('api/v1/share/customer.json', {
				params: {
					hash: this.$stateParams.hash,
				}
			})
			.success(function(result) {
				if (result == null || result.result === 'ng') {
					self.$location.path('/customer/password/' + self.$stateParams.hash);
					return;
				}

				self.customer = result.customer;
			})
			.error(function() {});
    }

	/**
     * 聞き取り情報の取得
     */
    loadHearings() {
        let self = this;
		this.$http.get('api/v1/share/hearings.json', {
				params: {
					hash: this.$stateParams.hash,
				}
			})
			.success(function(result) {
				if (result == null || result.result === 'ng') {
					self.$location.path('/customer/password/' + self.$stateParams.hash);
					return;
				}

				self.hearings = result.hearings;
			})
			.error(function() {});
    }

}
