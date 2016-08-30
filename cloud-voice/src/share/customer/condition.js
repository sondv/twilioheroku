/**
 * 状態
 */
export default class CustomerConditionCtrl {
	static $inject = ['$http', '$stateParams', '$scope', '$location', 'constant', 'moment', 'systemService', 'chartService', 'shareServicesAuth'];

	/**
	 * コンストラクタ
	 *
	 * @param $http
	 * @param $stateParams
	 * @param $scope
	 * @param $location
	 * @param constant
	 * @param moment
	 * @param systemService
	 * @param chartService
	 * @param shareServicesAuth
	 */
	constructor($http, $stateParams, $scope, $location, constant, moment, systemService, chartService, shareServicesAuth) {
		this.$http = $http;
		this.$stateParams = $stateParams;
		this.$scope = $scope;
		this.$location = $location;
		this.constant = constant;
		this.moment = moment;
		this.systemService = systemService;
		this.chartService = chartService;
		this.shareServicesAuth = shareServicesAuth;

		this.customer = null;
		self.hearing = null;
		this.chart = {
			fromDate: this.moment(this.moment().format('YYYY-MM-01')).toDate(),
			toDate: this.moment(this.moment().format('YYYY-MM-01')).toDate(),
			period: '1month'
		};

		// URL受け入れ確認
		if (this.shareServicesAuth.isAccept(this.$stateParams.hash)) {
			// 聞き取り日が未設定の場合は、本日とする
			if (this.$stateParams.date === '') {
				this.changeLocationPath();
			} else {
				this.load();
			}
		}
	}

	/**
	 * 読み込み
	 */
	load() {
		if (this.$stateParams.hash === undefined) {
			return;
		}

		this.loadConst();
		this.loadCustomer();
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
	loadCustomer() {
		let self = this;
		this.$http.get('api/v1/share/customer.json', {
			params: {
				hash: this.$stateParams.hash,
				date: this.$stateParams.date
			}
		})
			.success(function (result) {
				if (result === null || result.result === 'ng') {
					self.$location.path('/customer/password/' + self.$stateParams.hash);
					return;
				}

				self.customer = result.customer;
				if (result.hearing) {
					self.hearing = self.systemService.dateStringToISO8601(result.hearing.hearing_content);
				}

				// 読み込んだ利用者情報から、その他情報の読み込み
				self.recentlyCondition();
				self.updateChart();
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
				customer_id: this.customer.id
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
		// this.drawChart('chart-4', 'fever');
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
				customer_id: this.customer.id,
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

		this.chart.fromDate = this.moment(this.chart.fromDate).add(prev, 'M');
		this.chart.toDate = this.moment(this.chart.toDate).add(prev, 'M');
		this.updateChart();
	}

	/**
	 * 次の期間へ
	 */
	nextPeriod() {
		var next = +1;

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

	/**
	 * 指定聞き取り日に画面繊維する
	 *
	 * @param date
	 */
	changeLocationPath(date) {
		var path = [];
		path.push('share');
		path.push('customer');
		path.push('condition');
		path.push(this.$stateParams.hash);
		path.push((date === undefined) ? this.moment().format('YYYY-MM-DD') : this.moment(date).format('YYYY-MM-DD'));
		this.$location.path('/' + path.join('/'));
	}
}
