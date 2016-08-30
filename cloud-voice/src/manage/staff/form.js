/**
 * スタッフフォーム
 */
export default class StaffFormCtrl {
	static $inject = ['$http', '$mdDialog', 'manageServicesTwilio', 'id'];

	/**
	 * コンストラクタ
	 *
	 * @param $http
	 * @param $mdDialog
	 * @param manageServicesTwilio
	 * @param id
	 */
	constructor($http, $mdDialog, manageServicesTwilio, id) {
		this.$http = $http;
		this.$mdDialog = $mdDialog;
		this.manageServicesTwilio = manageServicesTwilio;

		this.staffId = id;

		this.load();
	}

	/**
	 * キャンセル
	 */
	cancel() {
		this.$mdDialog.cancel();
	}

	/**
	 * 読み込み
	 */
	load() {
		if (this.staffId === undefined) {
			return;
		}

		let self = this;
		this.$http.get('api/v1/staff/find_one.json?' + $.param({id: this.staffId}))
			.success(function (result) {
				if (result === null || result.result === 'ng') {
					return;
				}

				self.staff = result.staff;
			})
			.error(function () {
			});
	}

	/**
	 * 保存
	 */
	save() {
		let self = this;
		this.$http.post('api/v1/staff/save.json', {
			staff: this.staff
		})
			.success(function (result) {
				if (result === null || result.result === 'ng') {
					self.error = result.error;
					return;
				}

				self.manageServicesTwilio.refreshToken();
				self.$mdDialog.hide();
			})
			.error(function () {
			});
	}
}
