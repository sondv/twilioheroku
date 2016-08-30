/**
 * 利用者聞き取りフォーム
 */
export default class CustomerHearingCtrl {
	static $inject = ['$http', '$q', '$stateParams', '$mdToast', '$scope', '$location', '$document', '$mdDialog', 'constant', 'moment', 'systemService', 'manageServicesTwilio'];

	/**
	 * コンストラクタ
	 *
	 * @param $http
	 * @param $q
	 * @param $stateParams
	 * @param $mdToast
	 * @param $scope
	 * @param $location
	 * @param $document
	 * @param $mdDialog
	 * @param constant
	 * @param moment
	 * @param systemService
	 * @param manageServicesTwilio
	 */
	constructor($http, $q, $stateParams, $mdToast, $scope, $location, $document, $mdDialog, constant, moment, systemService, manageServicesTwilio) {
		this.$http = $http;
		this.$q = $q;
		this.$stateParams = $stateParams;
		this.$mdToast = $mdToast;
		this.$scope = $scope;
		this.$location = $location;
		this.$document = $document;
		this.$mdDialog = $mdDialog;
		this.constant = constant;
		this.moment = moment;
		this.systemService = systemService;
		this.manageServicesTwilio = manageServicesTwilio;

		this.hearingDefault = {
			question_1: 5,
			question_2: 3,
			question_3: [1, 2, 3],
			question_4: 4,
			question_5: [],
			question_6: 3,
			question_7: 3,
			question_8: 3,
			question_9: 3,
			question_10: [],
			question_note_01: '',
			question_note_02: '',
			question_note_03: '',
			question_note_04: '',
			question_note_05: '',
			body: {
				height: 0,
				weight: 0,
				temperature: 0,
				blood: {
					pressure: {
						systolic: 0,
						diastolic: 0
					}
				}
			},
			impression_1: 3,
			impression_note_01: ''
		};
		this.hearing = this.hearingDefault;
		this.meals = {};
		// this.meals.search.breakfast = '';
		// this.meals.search.lunch = '';
		// this.meals.search.dinner = '';
		// this.meals.search.between = '';
		// this.meals.breakfast = {};
		// this.meals.lunch = {};
		// this.meals.dinner = {};
		// this.meals.between = {};

		this.customer = {};
		this.phones = [];

		// 聞き取り日が未設定の場合は、本日とする
		if (this.$stateParams.hearingAt === '') {
			this.changeLocationPath();
		} else {
			this.load();
		}
	}

	/**
	 * 読み込み
	 */
	load() {
		if (this.$stateParams.customerId === undefined) {
			this.$location.path('/customer/');
		}

		this.loadConst();
		this.loadHearing();
		this.loadCustomer();
		this.loadCustomerPhones();
		this.changeHearingAt();
	}

	/**
	 * 定数を読み込む
	 */
	loadConst() {
		let self = this;
		this.$http.get('api/v1/hearing/const.json')
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
	 * 利用者の読み込み
	 */
	loadHearing() {
		let self = this;
		this.$http.get('api/v1/hearing/find_one.json', {
			params: {
				customer_id: this.$stateParams.customerId,
				hearing_at: this.$stateParams.hearingAt
			}
		})
			.success(function (result) {
				if (result === null || result.result === 'ng') {
					return;
				}

				var hearing = self.systemService.dateStringToISO8601(result.hearing.hearing_content);
				angular.forEach(self.hearingDefault, function (value, key) {
					if (hearing[key] === undefined) {
						hearing[key] = value;
					}
				});
				self.hearing = hearing;
			})
			.error(function () {
			});
	}

	/**
	 * 利用者の読み込み
	 */
	loadCustomer() {
		let self = this;
		this.$http.get('api/v1/customer/find_one.json', {
			params: {
				id: this.$stateParams.customerId
			}
		})
			.success(function (result) {
				if (result === null || result.result === 'ng') {
					self.$location.path('/customer/');
					return;
				}

				self.customer = result.customer;
				// 身体情報
				self.hearing.body.height = self.hearing.body.height === self.hearingDefault.body.height ? parseFloat(result.customer.height) : self.hearing.body.height;
				self.hearing.body.weight = self.hearing.body.weight === self.hearingDefault.body.weight ? parseFloat(result.customer.weight) : self.hearing.body.weight;
				self.hearing.body.temperature = self.hearing.body.temperature === self.hearingDefault.body.temperature ? parseFloat(result.customer.temperature) : self.hearing.body.temperature;
				self.hearing.body.blood.pressure.systolic = self.hearing.body.blood.pressure.systolic === self.hearingDefault.body.blood.pressure.systolic ? parseFloat(result.customer.blood_pressure_systolic) : self.hearing.body.blood.pressure.systolic;
				self.hearing.body.blood.pressure.diastolic = self.hearing.body.blood.pressure.diastolic === self.hearingDefault.body.blood.pressure.diastolic ? parseFloat(result.customer.blood_pressure_diastolic) : self.hearing.body.blood.pressure.diastolic;
			})
			.error(function () {
			});
	}

	/**
	 * 利用者の電話番号読み込み
	 */
	loadCustomerPhones() {
		let self = this;
		this.$http.get('api/v1/customer/phones.json', {
			params: {
				id: this.$stateParams.customerId
			}
		})
			.success(function (result) {
				if (result === null || result.result === 'ng') {
					return;
				}
				self.phones = result.phones;
			})
			.error(function () {
			});
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
				enableEdit: false,
			},
			clickOutsideToClose: true
		})
			.then(function () {
			}, function () {
			});
		return false;
	}

	/**
	 * 料理を検索
	 */
	findMeals(keyword) {
		var deferred = this.$q.defer();

		let self = this;
		this.$http.get('api/v1/meal/search.json', {
			params: {
				keyword: keyword
			}
		})
			.success(function (result) {
				if (result === null || result.result === 'ng') {
					self.error = result.error;
					return;
				}

				if (result.meals) {
					deferred.resolve(result.meals);
				}
			})
			.error(function () {
			});

		return deferred.promise;
	}

	/**
	 * 料理を追加
	 */
	addMeal(mealTiming) {
		console.log('addMeal');
	}

	/**
	 * 保存
	 */
	save() {
		let self = this;

		// 備考欄が空白の場合は「特に無し」として保存
		let hearing = this.hearing;
		if (hearing.question_note_01 === '') {
			hearing.question_note_01 = '特に無し';
		}
		if (hearing.question_note_02 === '') {
			hearing.question_note_02 = '特に無し';
		}
		if (hearing.question_note_03 === '') {
			hearing.question_note_03 = '特に無し';
		}
		if (hearing.question_note_04 === '') {
			hearing.question_note_04 = '特に無し';
		}
		if (hearing.question_note_05 === '') {
			hearing.question_note_05 = '特に無し';
		}
		if (hearing.impression_note_01 === '') {
			hearing.impression_note_01 = '特に無し';
		}

		let data = {
			hearing: {
				customer_id: this.$stateParams.customerId,
				hearing_at: this.$stateParams.hearingAt,
				hearing_content: JSON.stringify(hearing)
			}
		};
		this.$http.post('api/v1/hearing/save.json', data)
			.success(function (result) {
				if (result === null || result.result === 'ng') {
					self.error = result.error;
					return;
				}

				var message = result.sent_mail ? '保存しました メールを送信しました' : '保存しました';
				self.$mdToast.show(
					self.$mdToast.simple()
						.textContent(message)
						.position('bottom right')
						.hideDelay(1000)
				);
			})
			.error(function () {
			});
	}

	/**
	 * 聞き取り日を変更
	 */
	changeHearingAt() {
		let self = this;
		this.$scope.$watch('ctrl.search.hearingAt', function (newValue) {
			if (newValue === undefined) {
				return;
			}

			self.changeLocationPath(newValue);
		});
	}

	/**
	 * 指定聞き取り日に画面繊維する
	 *
	 * @param date
	 */
	changeLocationPath(date) {
		var path = [];
		path.push('customer');
		path.push('hearing');
		path.push(this.$stateParams.customerId);
		path.push((date === undefined) ? this.moment().format('YYYY-MM-DD') : this.moment(date).format('YYYY-MM-DD'));
		this.$location.path('/' + path.join('/'));
	}
}
