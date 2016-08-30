/**
 * スタッフ詳細
 */
export default class StaffInfoCtrl {
	static $inject = ['$http', '$mdDialog', 'id', 'systemService'];

	/**
	 * コンストラクタ
	 *
	 * @param $http
	 * @param $mdDialog
	 * @param id
	 */
	constructor($http, $mdDialog, id, systemService) {
		this.$http = $http;
		this.$mdDialog = $mdDialog;
		this.systemService = systemService;
		if (id) {
			this.load(id);
		}
	}

	/**
	 * 読み込み
	 *
	 * @param id
	 */
	load(id) {
		let self = this;
		this.$http.get('api/v1/staff/find_one.json?' + $.param({
				id: id
			}))
			.success(function(result) {
				if (result === null || result.result === 'ng') {
					return;
				}
				self.staff = result.staff;
			})
			.error(function() {});
	}

	/**
	 * 閉じる
	 */
	close() {
		this.$mdDialog.cancel();
	}

	/**
	 * 登録・編集ダイアログ表示
	 */
	showEdit() {
		this.$mdDialog.hide('edit');
	}

	/**
	 * 削除ダイアログ表示
	 */
	showDelete() {
		this.$mdDialog.hide('delete');
	}

}
