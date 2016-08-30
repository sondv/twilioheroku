/**
 * 聞き取り
 */
export default class CustomerHearingCtrl {
	static $inject = ['$http', '$stateParams', '$scope', '$location', '$document', 'constant', 'moment', 'systemService', 'shareServicesAuth', 'ngAudio'];

	/**
	 * コンストラクタ
	 *
	 * @param $http
	 * @param $stateParams
	 * @param $scope
	 * @param $location
	 * @param $document
	 * @param constant
	 * @param moment
	 * @param systemService
	 * @param shareServicesAuth
	 * @param ngAudio
	 */
	constructor($http, $stateParams, $scope, $location, $document, constant, moment, systemService, shareServicesAuth, ngAudio) {
		this.$http = $http;
		this.$stateParams = $stateParams;
		this.$scope = $scope;
		this.$location = $location;
		this.$document = $document;
		this.constant = constant;
		this.moment = moment;
		this.systemService = systemService;
		this.shareServicesAuth = shareServicesAuth;
		this.ngAudio = ngAudio;

		this.customer = null;
		this.hearing = null;
		this.twilio_histories = [];
		this.audio = null;
		this.audios = [];
		this.playRecordFilePath = '';

		// URL受け入れ確認
		if (this.shareServicesAuth.isAccept(this.$stateParams.hash)) {
			// 聞き取り日が未設定の場合は、本日とする
			if (this.$stateParams.date === '') {
				this.changeLocationPath();
			} else {
				this.load();
			}
		}

		// ngAudioの破棄（画面遷移後に継続して再生させない）
		let self = this;
		this.$scope.$on('$destroy', function handler() {
			if (self.audio != null) {
				self.ngAudio = null;
				self.audio.stop();
				self.audio.destroy();
			}
		});
	}

	/**
	 * 読み込み
	 */
	load() {
		if (this.$stateParams.hash === undefined || this.$stateParams.date === undefined) {
			return;
		}

		this.loadConst();
		this.loadCustomer();
		this.loadHearing();
		this.loadTwilioHistories();
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
				hash: this.$stateParams.hash
			}
		})
			.success(function (result) {
				if (result == null || result.result === 'ng') {
					self.$location.path('/customer/password/' + self.$stateParams.hash);
					return;
				}

				self.customer = result.customer;
			})
			.error(function () {
			});
	}

	/**
     * 聞き取り情報の取得
     */
    loadHearing() {
        let self = this;
		this.$http.get('api/v1/share/hearing.json', {
				params: {
					hash: this.$stateParams.hash,
					date: this.$stateParams.date
				}
			})
			.success(function(result) {
				if (result == null || result.result === 'ng') {
					self.$location.path('/customer/top/' + self.$stateParams.hash);
					return;
				}

				self.hearing = self.systemService.dateStringToISO8601(result.hearing.hearing_content);
			})
			.error(function() {});
    }

	/**
	 * Twilio履歴の読み込み
	 */
	loadTwilioHistories() {
		let self = this;
		this.$http.get('api/v1/share/twilio_histories.json', {
			params: {
				hash: this.$stateParams.hash,
				date: this.$stateParams.date
			}
		})
			.success(function (result) {
				if (result == null || result.result === 'ng') {
					return;
				}

				self.twilio_histories = result.twilio_histories;
			})
			.error(function () {
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
		path.push(this.$stateParams.hash);
		path.push((date === undefined) ? this.moment().format('YYYY-MM-DD') : this.moment(date).format('YYYY-MM-DD'));
		this.$location.path('/' + path.join('/'));
	}

	/**
	 * 録音ファイルを再生
	 *
	 * @param path
	 */
	playRecordFile(path) {
		if (this.playRecordFilePath !== path) {
			this.playRecordFilePath = path;
			if (this.audio != null) {
				this.audio.destroy();
			}

			this.audio = this.ngAudio.load(this.constant.resourceBasePath + 'record/' + path);
			this.audio.play();
		} else {
			this.audio.paused ? this.audio.play() : this.audio.pause();
		}
	}

}
