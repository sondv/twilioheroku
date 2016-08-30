/**
 * 聞き取り現状
 */
export default class CustomerConditionCtrl {
	static $inject = ['$http', '$location', '$stateParams', '$scope', 'constant', 'moment', 'systemService', 'chartService'];

	/**
	 * コンストラクタ
	 *
	 * @param $http
	 * @param $location
	 * @param $stateParams
	 * @param $scope
	 * @param constant
	 * @param moment
	 * @param systemService
	 * @param chartService
	 */
	constructor($http, $location, $stateParams, $scope, constant, moment, systemService, chartService) {
		this.$http = $http;
		this.$location = $location;
		this.$stateParams = $stateParams;
		this.$scope = $scope;
		this.constant = constant;
		this.moment = moment;
		this.systemService = systemService;
		this.chartService = chartService;

		this.customer = null;
		this.chart = {
			fromDate: this.moment(this.moment().format('YYYY-MM-01')).toDate(),
			toDate: this.moment(this.moment().format('YYYY-MM-01')).toDate(),
			period: '1month'
		};
		this.phones = [];

		this.load();
	}

	/**
	 * 読み込み
	 */
	load() {
		if (this.$stateParams.customerId === undefined) {
			this.$location.path('/customer/');
			return;
		}

		this.loadCustomer();
		this.loadCustomerPhones();
		this.recentlyCondition();
		this.updateChart();
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
	 * 直近の状況
	 */
	recentlyCondition() {
		let self = this;
		this.$http.get('api/v1/hearing/recently_condition.json', {
			params: {
				customer_id: self.$stateParams.customerId
			}
		})
			.success(function (result) {
				if (result === null || result.result === 'ng') {
					self.error = result.error;
					return;
				}

				self.recently = result.recently;
			})
			.error(function () {
			});
	}

	/**
	 * グラフを更新
	 */
	updateChart() {
		this.drawChart('chart-1', 'meal');
		this.drawChart('chart-2', 'activity');
		this.drawChart('chart-3', 'feeling');
		this.drawChart('chart-4', 'fever');
	}

	/**
	 * グラフの描画
	 *
	 * @param elementId
	 * @param targetId
	 */
	drawChart(elementId, targetId) {
		let self = this;
		this.$http.get('api/v1/hearing/condition.json', {
			params: {
				customer_id: self.$stateParams.customerId,
				target: targetId,
				period: self.chart.period,
				date: self.moment(self.chart.toDate).format('YYYY-MM-DD')
			}
		})
			.success(function (result) {
				if (result === null || result.result === 'ng') {
					self.error = result.error;
					return;
				}

				self[targetId] = result.data;
				if (targetId === 'fever') {
					self.chartService.drawFeverChart(elementId, result.data);
				} else {
					self.chartService.drawComboBarLine(elementId, result.data);
				}
			})
			.error(function () {
			});
	}

	/**
	 * 前の期間へ
	 */
	prevPeriod() {
		var prev = -1;
		// switch (this.chart.period) {
		// 	case '1month':
		// 		prev = -1;
		// 		break;
		// 	case '3months':
		// 		prev = -3;
		// 		break;
		// 	case '12months':
		// 		prev = -12;
		// 		break;
		// }

		this.chart.fromDate = this.moment(this.chart.fromDate).add(prev, 'M');
		this.chart.toDate = this.moment(this.chart.toDate).add(prev, 'M');
		this.updateChart();
	}

	/**
	 * 次の期間へ
	 */
	nextPeriod() {
		var next = +1;
		// switch (this.chart.period) {
		// 	case '1month':
		// 		next = +1;
		// 		break;
		// 	case '3months':
		// 		next = +3;
		// 		break;
		// 	case '12months':
		// 		next = +12;
		// 		break;
		// }

		this.chart.fromDate = this.moment(this.chart.fromDate).add(next, 'M');
		this.chart.toDate = this.moment(this.chart.toDate).add(next, 'M');
		this.updateChart();
	}

	/**
	 * 期間の変更
	 */
	changePeriod() {
		var from = -0;
		switch (this.chart.period) {
			case '1month':
				from = -0;
				break;
			case '3months':
				from = -2;
				break;
			case '12months':
				from = -11;
				break;
		}

		this.chart.fromDate = this.moment(this.chart.toDate).add(from, 'M');
		this.updateChart();
	}
}
