/**
 * 受電フォーム
 */
export default class IncomingFormCtrl {
	static $inject = ['$http', '$mdDialog', '$scope', 'constant', 'incomingPhoneNumber'];

	/**
	 * コンストラクタ
	 *
	 * @param $http
	 * @param $mdDialog
	 * @param $scope
	 * @param constant
	 * @param incomingPhoneNumber
	 */
	constructor($http, $mdDialog, $scope, constant, incomingPhoneNumber) {
		this.$http = $http;
		this.$mdDialog = $mdDialog;
		this.$scope = $scope;
		this.constant = constant;
		this.incomingPhoneNumber = incomingPhoneNumber;

		this.incomingCustomerInfo();
	}

	/**
	 * 利用者登録されているか
	 *
	 * @type {boolean}
	 */
	is_registered = false;

	/**
	 * 利用者情報
	 *
	 * @type {null}
	 */
	customer = null;

	/**
	 * 電話に出る
	 *
	 * @param result
	 */
	acceptIncoming(result) {
		this.$mdDialog.hide(result);
	}

	/**
	 * 拒否する
	 */
	cancel() {
		this.$mdDialog.cancel();
	}

	/**
	 * 発信者情報を登録されている内容で表示する
	 */
	incomingCustomerInfo() {
		let self = this;
		this.$http.get('api/v1/customer/find_one_by_twilio_number.json', {
			params: {
				twilio_number: this.incomingPhoneNumber
			}
		})
			.success(function (result) {
				self.is_registered = result.is_registered;
				self.customer = result.customer;
			})
			.error(function () {
			});
	}
}
