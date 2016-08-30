/**
 * 利用者フォーム
 */
export default class CustomerFormCtrl {
	static $inject = ['$http', '$mdDialog', '$scope', 'constant', 'systemService', 'id'];

	/**
	 * コンストラクタ
	 *
	 * @param $http
	 * @param $mdDialog
	 * @param $scope
	 * @param constant
	 * @param systemService
	 * @param id
	 */
	constructor($http, $mdDialog, $scope, constant, systemService, id) {
		this.$http = $http;
		this.$mdDialog = $mdDialog;
		this.$scope = $scope;
		this.constant = constant;
		this.systemService = systemService;

		this.customer = {};
		this.customerId = id;
		this.share = {
			enable: false,
			url: '',
			onChangePass: false,
			onDelete: false
		};

		// 各データ読み込み
		this.loadConst();
		this.loadCustomer();
		this.upload();
	}

	/**
	 * 定数を読み込む
	 */
	loadConst() {
		let self = this;
		this.$http.get('api/v1/customer/const.json')
			.success(function (result) {
				if (result === null || result.result === 'ng') {
					self.error = result.error;
					return;
				}
				if (result.const) {
					self.const = result.const;
				}
			})
			.error(function () {
			});
	}

	/**
	 * 読み込み
	 */
	loadCustomer() {
		if (this.customerId === undefined) {
			return;
		}

		let self = this;
		this.$http.get('api/v1/customer/find_one.json', {
			params: {
				id: this.customerId
			}
		})
			.success(function (result) {
				if (result === null || result.result === 'ng') {
					return;
				}

				self.customer = self.systemService.dateStringToISO8601(result.customer);
				if (result.customer.share_url) {
					self.share.enable = true;
					self.share.url = result.customer.share_url;
				}
			})
			.error(function () {
			});
	}

	/**
	 * キャンセル
	 */
	cancel() {
		this.$mdDialog.cancel();
	}

	/**
	 * 保存
	 */
	save() {
		let self = this;
		let data = {
			customer: this.customer
		};
		this.$http.post('api/v1/customer/save.json', data)
			.success(function (result) {
				if (result === null || result.result === 'ng') {
					self.error = result.error;
					return;
				}

				self.$mdDialog.hide();
			})
			.error(function () {
			});
	}

	/**
	 * 顔写真アップロード
	 */
	upload() {
		let self = this;
		this.$scope.$watch('ctrl.file.length', function (newValue) {
			if (newValue === undefined || newValue === 0) {
				return;
			}

			var data = new FormData();
			angular.forEach(self.file, function (obj) {
				data.append('file', obj.lfFile);
			});
			let config = {
				transformRequest: angular.identity,
				headers: {'Content-Type': undefined}
			};
			self.$http.post('api/v1/customer/upload_photo.json', data, config)
				.success(function (result) {
					if (result === null || result.result === 'ng') {
						self.error = result.error;
						return;
					}

					self.customer.photo = result.photo;
				})
				.error(function () {
				});
		});
	}

	/**
	 * 共有URLを作成
	 */
	createShareURL() {
		if (this.share.url !== '') {
			return;
		}

		let self = this;
		let data = {
			customer_id: this.customerId,
			password: this.share.password
		};
		this.$http.post('api/v1/share/create.json', data)
			.success(function (result) {
				if (result === null || result.result === 'ng') {
					self.error = result.error;
					return;
				}

				self.error = null;
				self.share.url = result.share_url;
			})
			.error(function () {
			});
	}

	/**
	 * 共有URLのパスワードを変更
	 */
	changeShareURLPassword() {
		if (this.share.url === '') {
			return;
		}

		let self = this;
		let data = {
			customer_id: this.customerId,
			password: this.share.password
		};
		this.$http.post('api/v1/share/change_password.json', data)
			.success(function (result) {
				if (result === null || result.result === 'ng') {
					self.error = result.error;
					return;
				}

				self.error = null;
				self.share.onChangePass = false;
			})
			.error(function () {
			});
	}

	/**
	 * 共有URLを削除
	 */
	deleteShareURL() {
		if (this.share.url === '') {
			return;
		}

		let self = this;
		let data = {
			customer_id: this.customerId
		};
		this.$http.post('api/v1/share/delete.json', data)
			.success(function (result) {
				if (result === null || result.result === 'ng') {
					self.error = result.error;
					return;
				}

				self.error = null;
				self.share.onDelete = false;
				self.share.url = '';
			})
			.error(function () {
			});
	}

}
