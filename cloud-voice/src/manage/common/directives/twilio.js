/**
 * ツイリオ関連
 */
export default class TwilioCtrl {
	static $inject = ['$scope', 'manageServicesTwilio'];

	/**
	 * コンストラクタ
	 */
	constructor($scope, manageServicesTwilio) {
		let self = this;
		this.$scope = $scope;
		this.manageServicesTwilio = manageServicesTwilio;

		// 利用者ID
		this.$scope.$watch('customerId', function(newValue) {
			if (newValue === undefined) {
				return;
			}
			self.customerId = newValue;
		});

		// 電話番号
		this.$scope.$watch('phones', function(newValues) {
			if (newValues === undefined) {
				return;
			}
			self.phones = newValues;
		});
	}
}
