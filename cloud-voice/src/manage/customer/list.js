/**
 * 利用者一覧
 */
export default class CustomerListCtrl {
	static $inject = ['$http', '$location', '$mdDialog', 'constant', 'moment'];

	/**
	 * コンストラクタ
	 *
	 * @param $http
	 * @param $location
	 * @param $http
	 * @param constant
	 * @param moment
	 */
	constructor($http, $location, $mdDialog, constant, moment) {
		this.$http = $http;
		this.$location = $location;
		this.$mdDialog = $mdDialog;
		this.constant = constant;
		this.moment = moment;

		this.customers = [];
		this.query = {
			keyword: '',
			day: '',
			page: 1,
		};
		this.showSearch = false;
		this.findRunning = false;

		this.find();
	}

	/**
	 * 検索
	 */
	find(more = null) {
		if (this.findRunning) {
			return;
		}
		this.findRunning = true;

		let self = this;
		var data = {
			params: this.query
		};
		this.$http.get('api/v1/customer/find.json', data)
			.success(function(result) {
				self.findRunning = false;

				if (result === null || result.result === 'ng') {
					self.error = result.error;
					return;
				}
				if (result.customers) {
					self.customers = (more) ? [...self.customers, ...result.customers] : result.customers
					self.current_page = result.current_page;
					self.total_pages = result.total_pages;
				}
			})
			.error(function() {
				self.findRunning = false;
			});
	}

	/**
	 * さらに読み込む
	 */
	more() {
		this.find(1);
	}

	/**
	 * 詳細の表示
	 *
	 * @param $event
	 * @param id
	 */
	showInfo($event, id) {
		let self = this;
		this.$mdDialog.show({
				template: require('./templates/info.html'),
				controller: 'CustomerInfoCtrl',
				controllerAs: 'ctrl',
				targetEvent: $event,
				locals: {
					id: id,
					enableEdit: true,
				},
				clickOutsideToClose: true
			})
			.then(function(next) {
				if (next === 'edit') {
					self.showForm($event, id);
				} else if (next === 'delete') {
					self.showDelete($event, id);
				} else {
					self.find();
				}
			}, function() {});
		return false;
	}

	/**
	 * 登録・編集ダイアログ表示
	 *
	 * @param $event
	 * @param id
	 */
	showForm($event, id) {
		let self = this;
		this.$mdDialog.show({
				template: require('./templates/form.html'),
				controller: 'CustomerFormCtrl',
				controllerAs: 'ctrl',
				targetEvent: $event,
				locals: {
					id: id
				}
			})
			.then(function() {
				self.find();
			}, function() {});
		return false;
	}

	/**
	 * 登録・編集ダイアログ表示
	 *
	 * @param $event
	 * @param id
	 */
	showForma($event, id) {
		console.log(this.showSearch)
		this.showSearch = !this.showSearch;
	}

	/**
	 * 削除ダイアログ表示
	 *
	 * @param $event
	 * @param id
	 */
	showDelete($event, id) {
		let self = this;
		var confirm = this.$mdDialog.confirm()
			.textContent('削除します。よろしいですか?')
			.targetEvent($event)
			.ok('はい')
			.cancel('いいえ');

		this.$mdDialog.show(confirm)
			.then(function() {
				self.delete(id);
			}, function() {});
		return false;
	}

	/**
	 * 削除
	 *
	 * @param id
	 */
	delete(id) {
		let self = this;
		this.$http.post('api/v1/customer/delete.json', {
				id: id
			})
			.success(function(result) {
				if (result === null || result.result === 'ng') {
					self.error = result.error;
					return;
				}
				self.find();
				// angular.forEach(self.customers, function(customer, index) {
				// 	if (customer.id === id.toString()) {
				// 		self.customers.splice(index, 1);
				// 		return false;
				// 	}
				// });
			})
	}
}
