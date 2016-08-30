/**
 * ツールバー
 */
export default class ToolbarCtrl {
	static $inject = ['$http', '$timeout', '$interval', '$mdSidenav', 'manageServicesTwilio', 'manageServicesAuth'];

	/**
	 * コンストラクタ
	 *
	 * @param $http
	 * @param $timeout
	 * @param $interval
	 * @param $mdSidenav
	 * @param manageServicesTwilio
	 * @param manageServicesAuth
	 */
	constructor($http, $timeout, $interval, $mdSidenav, manageServicesTwilio, manageServicesAuth) {
		this.$http = $http;
		this.$timeout = $timeout;
		this.$interval = $interval;
		this.$mdSidenav = $mdSidenav;
		this.manageServicesTwilio = manageServicesTwilio;
		this.staff = manageServicesAuth.getLoginStaff();

		this.recordingStatusList = {
			none: '',
			loading: 'loading',
			recording: 'recording',
			finish: 'finish',
			canceled: 'canceled',
			fail: 'fail'
		};
		this.recordingStatusGetOnce = true;
		this.recordingInterval = null;

		this.defaultRecordingStatus();
	}

	/**
	 * サイドナビの切り替え
	 */
	toggleSidenav() {
		this.$mdSidenav('left').toggle();
	}

	/**
	 * Twilioの切断
	 */
	disconnectTwilio() {
		// 切断
		this.manageServicesTwilio.disconnectAll();

		let self = this;

		this.defaultRecordingStatus();
		this.recordingStatus = this.recordingStatusList.loading;
		this.recordingInterval = this.$interval(function () {
			self.$http.get('api/v1/twilio/find_parent_call_sid?' + $.param({
					sid: self.manageServicesTwilio.recentCallSid
				}))
				.success(function(result) {
					if (result == null || result.result === 'ng' || result.hearing == null) {
						// 取得1回目なら、再取得
						if (self.recordingStatusGetOnce === true) {
							self.recordingStatusGetOnce = false;
						} else {
							self.recordingStatus = self.recordingStatusList.fail;
							self.$timeout(function() {
								self.recordingStatus = self.recordingStatusList.none;
								self.recordingStatusGetOnce = true;
							}, 5000);
							self.$interval.cancel(self.recordingInterval);
						}
						return;
					}

					// 通話拒否など、通話自体がなかった
					if (result.hearing.status !== 'completed') {
						self.recordingStatus = self.recordingStatusList.canceled;
						self.$timeout(function() {
							self.recordingStatus = self.recordingStatusList.none;
							self.recordingStatusGetOnce = true;
						}, 5000);
						self.$interval.cancel(self.recordingInterval);
					// 録音ファイルパスが生成されていない
					} else if (result.hearing.record_file_path == null || result.hearing.record_file_path == '') {
						self.recordingStatus = self.recordingStatusList.recording;
					// 録音ファイルパスが生成された
					} else if (result.hearing.record_file_path) {
						self.recordingStatus = self.recordingStatusList.finish;
						self.$timeout(function() {
							self.recordingStatus = self.recordingStatusList.none;
							self.recordingStatusGetOnce = true;
						}, 5000);
						self.$interval.cancel(self.recordingInterval);
					// それ以外
					} else {
						self.recordingStatus = self.recordingStatusList.fail;
						self.$timeout(function() {
							self.recordingStatus = self.recordingStatusList.none;
							self.recordingStatusGetOnce = true;
						}, 5000);
						self.$interval.cancel(self.recordingInterval);
					}
					return;
				})
				.error(function() {});
		}, 1000);
	}

	/**
	 * 録音ステータスの初期化
	 */
	defaultRecordingStatus() {
		this.recordingStatus = this.recordingStatusList.none;
		this.recordingStatusGetOnce = true;
	}

}
