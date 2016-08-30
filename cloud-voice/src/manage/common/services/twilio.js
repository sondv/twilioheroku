/**
 * Twilioサービス
 */
export default class ManageTwilioService {
	static $inject = ['$http', '$location', '$mdToast', '$mdDialog'];

	/**
	 * コンストラクタ
	 *
	 * @param $http
	 * @param $location
	 * @param $mdToast
	 * @param $mdDialog
	 */
	constructor($http, $location, $mdToast, $mdDialog) {
		this.$http = $http;
		this.$location = $location;
		this.$mdToast = $mdToast;
		this.$mdDialog = $mdDialog;
	}

	/**
	 * Twilio.Deviceを初期化済みか
	 *
	 * @type Boolean
	 */
	hasSetupClient = false;

	/**
	 * 通話中か
	 *
	 * @type Boolean
	 */
	inConnected = false;

	/**
	 * 呼び出し中か
	 *
	 * @type Boolean
	 */
	inCalling = false;

	/**
	 * トークン
	 *
	 * @type string
	 */
	token = null;

	/**
	 * 受電電話番号
	 *
	 * @type {null}
	 */
	incomingPhoneNumber = null;

	/**
	 * 接続中電話番号
	 *
	 * @type {null}
	 */
	connectingPhoneNumber = null;

	/**
	 * 直近の通話SID
	 *
	 * @type int
	 */
	recentCallSid = '';

	/**
	 * 切断中
	 *
	 * @type {boolean}
	 */
	disconnecting = false;

	/**
	 * Twilioクライアントを設定
	 *   Twilio.Device；https://jp.twilio.com/docs/api/client/device
	 */
	setupClient() {
		console.log('TwilioService.setupClient');
		let self = this;
		this.hasSetupClient = true;
		this.enable();
		this.refreshToken();

		/**
		 * Twilio.Device.offline
		 */
		Twilio.Device.offline(function (device) {
			console.log('Twilio.Device.offline');
			//self.refreshToken();
		});

		/**
		 * Twilio.Device.error
		 */
		Twilio.Device.error(function (error) {
			console.log('Twilio.Device.error');
			if (error === undefined) {
				self.$mdToast.show(
					self.$mdToast.simple()
						.textContent('不明なエラーが発生しました。')
						.position('bottom right')
						.hideDelay(5000)
				);
				return;
			}

			if (error.code === 31205) {
				// JWT token expired. なので tokenの再取得をうながす。
				self.refreshToken();
				return;
			}

			self.$mdToast.show(
				self.$mdToast.simple()
					.textContent('エラーが発生しました。' + error.code + ':' + error.message)
					.position('bottom right')
					.hideDelay(5000)
			);
		});

		/**
		 * Twilio.Device.incoming
		 */
		Twilio.Device.incoming(function (conn) {
			console.log('Twilio.Device.incoming');

			var incomingPhoneNumber = self.removeInternationalCallingCode(conn.parameters.From);
			self.incomingPhoneNumber = incomingPhoneNumber;
			self.connectingPhoneNumber = incomingPhoneNumber;
			console.log("Incoming connection from " + self.incomingPhoneNumber);

			self.recentCallSid = conn.parameters.CallSid;

			self.$mdDialog.show({
				template: require('../controllers/templates/incoming.html'),
				controller: 'IncomingFormCtrl',
				controllerAs: 'ctrl',
				parent: angular.element(document.body),
				clickOutsideToClose: true,
				locals: {incomingPhoneNumber: self.incomingPhoneNumber}
			})
				.then(function (result) {
					console.log('connect', self.incomingPhoneNumber);
					if (result === 'cancel') {
						return;
					}
					self.setIncomingPhoneNumber(self.incomingPhoneNumber);
					self.acceptIncoming();
					self.talking();
				}, function () {
					console.log('not connected');
					self.disconnect();
				});
		});

		/**
		 * Twilio.Device.ready
		 */
		Twilio.Device.ready(function (device) {
			console.log('Twilio.Device.ready');
		});

		/**
		 * Twilio.Device.connect
		 */
		Twilio.Device.connect(function (conn) {
			console.log('Twilio.Device.connect');
			self.recentCallSid = conn.parameters.CallSid;
		});

		/**
		 * Twilio.Device.disconnect
		 */
		Twilio.Device.disconnect(function (conn) {
			console.log('Twilio.Device.disconnect');

			if (self.disconnecting === false) {
				angular.element(document.querySelector('#twilio-talking')).triggerHandler('click');
			}
			self.disconnecting = false;
			self.connectingPhoneNumber = null;
		});

		/**
		 * Twilio.Device.cancel
		 */
		Twilio.Device.cancel(function (conn) {
			console.log('Twilio.Device.cancel');
			self.$mdDialog.hide('cancel');
			self.connectingPhoneNumber = null;
		});
	}

	/**
	 * ボタンを通話可能にする
	 */
	enable() {
		console.log('button.enable');
		this.inConnected = false;
		this.inCalling = false;
	}

	/**
	 * ボタンを呼び出し中にする
	 */
	calling() {
		console.log('button.calling');
		this.inConnected = false;
		this.inCalling = true;
	}

	/**
	 * ボタンを通話中にする
	 */
	talking() {
		console.log('button.talking');
		this.inConnected = true;
		this.inCalling = false;
	}

	/**
	 * トークンを再取得する
	 */
	refreshToken() {
		console.log('TwilioService.refreshToken');
		let self = this;
		this.$http.get('api/v1/twilio/token.json', {})
			.success(function (result) {
				self.token = result.token;
				if (self.token === undefined || self.token === null || self.token === '') {
					self.showTokenError();
					return;
				}

				console.log('Successfule get twilio token', self.token);
				Twilio.Device.setup(self.token, {
					debug: true,
					closeProtection: '通話中もしくは着信中です。このままウィンドウを閉じると通話を終了します。'
				});
			})
			.error(function () {
				self.showTokenError();
			});
	}

	/**
	 * トークン取得エラーを表示
	 */
	showTokenError() {
		if (['/auth/login/'].indexOf(this.$location.path()) > -1) {
			return;
		}

		this.$mdToast.show(
			this.$mdToast.simple()
				.textContent('電話機能の初期化に失敗しました。ページを再読み込みしてください。それでも解決しない場合は管理者へご連絡ください。')
				.position('bottom right')
				.hideDelay(5000)
		);
	}

	/**
	 * 接続
	 *
	 * @param customerId
	 * @param phoneNumber
	 * @param isWakeUp
	 */
	connect(customerId, phoneNumber, isWakeUp = true) {
		console.log('TwilioService.connect');
		console.log(customerId, phoneNumber);

		let outgoingPhoneNumber = this.resetInternationalCallingCode(phoneNumber);
		this.connectingPhoneNumber = this.removeInternationalCallingCode(phoneNumber);
		console.log('resetInternationalCallingCode', outgoingPhoneNumber);


		if (isWakeUp) {
			let self = this;
			this.$http.get('api/v1/gcm/is_wake_up.json', {
				params: {
					customer_id: customerId
				}
			})
				.success(function (result) {
					console.log('connect', outgoingPhoneNumber);
					self.talking();
					this.setConnectingPhoneNumber(customerId, outgoingPhoneNumber);
					Twilio.Device.connect({PhoneNumber: outgoingPhoneNumber});
				})
				.error(function () {
					console.log('not connected');
					setTimeout(function () {
						if (self.inConnected) {
							self.connect(customerId, outgoingPhoneNumber);
						}
					}, 1000);
				});
		} else {
			this.talking();
			this.setConnectingPhoneNumber(customerId, outgoingPhoneNumber);
			Twilio.Device.connect({PhoneNumber: outgoingPhoneNumber});
		}
	}

	/**
	 * 接続キャンセル
	 */
	connectCancel() {
		console.log('TwilioService.connectCancel');
		this.enable();
	}

	/**
	 * 切断
	 */
	disconnect() {
		console.log('TwilioService.disconnect');
		this.enable();
		let connection = Twilio.Device.activeConnection();
		if (connection === undefined) {
			return;
		}

		console.log(connection.status());
		if (connection.status() === 'pending') {
			connection.reject();
		} else if (connection.status() === 'connecting' || connection.status() === 'open') {
			connection.disconnect();
		}
	}

	/**
	 * すべて切断
	 */
	disconnectAll() {
		console.log('TwilioService.disconnectAll');
		this.enable();
		this.disconnecting = true;
		Twilio.Device.disconnectAll();
	}

	/**
	 * 電話を掛ける
	 *
	 * @param $event
	 * @param customerId
	 * @param customerPhone
	 * @param isWakeUp
	 */
	call($event, customerId, customerPhones = [], isWakeUp = true) {
		console.log('TwilioService.call');
		let self = this;
		let phoneNumber = this.getInOrderPhoneNumber(customerPhones);
		this.calling();

		if (isWakeUp) {
			this.$http.get('api/v1/gcm/wake_on_push.json', {
				params: {
					customer_id: customerId
				}
			})
				.success(function (result) {
					self.connect(customerId, phoneNumber, true);
				})
				.error(function () {
					self.enable();

					self.$mdToast.show(
						self.$mdToast.simple()
							.textContent('利用者の端末状態を確認できませんでした。端末の電源が入っていないか、電場の届かない場所にいるため通話できません。' + response.message)
							.position('bottom right')
							.hideDelay(5000)
					);
					return;
				});
		} else {
			this.connect(customerId, phoneNumber, false);
		}
	}

	/**
	 * 受電
	 */
	acceptIncoming() {
		console.log('TwilioService.acceptIncoming');
		let connection = Twilio.Device.activeConnection();
		if (connection === undefined) {
			return;
		}

		console.log(connection.status());
		if (connection.status() === 'pending') {
			connection.accept();
		}
	}

	/**
	 * サーバーに通話関連情報を送信
	 *
	 * @param int customerId
	 * @param string phoneNumber
	 * @returns {*}
	 */
	setConnectingPhoneNumber(customerId, phoneNumber) {
		var data = {
			customer_id: customerId,
			phone_number: phoneNumber
		};

		this.$http.post('api/v1/twilio/connecting_phone_number.json', data)
			.success(function (result) {
			})
			.error(function () {
			});
	}

	/**
	 * サーバーに通話関連情報を送信
	 *
	 * @param string phoneNumber
	 * @returns {*}
	 */
	setIncomingPhoneNumber(phoneNumber) {
		var data = {
			phone_number: phoneNumber
		};

		this.$http.post('api/v1/twilio/incoming_phone_number.json', data)
			.success(function (result) {
			})
			.error(function () {
			});
	}

	/**
	 * 国際電話番号を除去する
	 *
	 * @param number
	 * @returns {*}
	 */
	removeInternationalCallingCode(number) {
		if (number === undefined || number === null) {
			return '';
		}
		if (typeof number === 'number') {
			return String(number);
		}
		if (typeof number === 'string') {
			// @todo 国際電話場号一覧から除去する
			if (number.substr(0, 3) === '+81') {
				return '0' + number.substr(3);
			}
		}
		return number;
	}

	/**
	 * 国際電話番号を付与する
	 *
	 * @param number
	 * @param countory
	 * @returns {string}
	 */
	resetInternationalCallingCode(number, countory = 'jp') {
		// 市外局番の先頭に0があれば除去
		if (number.substr(0, 1) === '0') {
			number = number.substr(1);
		}

		// 改めて国際番号を付与する
		// @todo 国コードで国際電話番号を付与する
		return '+81' + number;
	}

	/**
	 * 優先順に電話番号を返す
	 *
	 * @param phones
	 * @returns {*}
	 */
	getInOrderPhoneNumber(phones = []) {
		var phoneNumber = null;
		angular.forEach(phones, function (phone) {
			if (phoneNumber === null &&
				Object.prototype.toString.call(phone).toString() === '[object String]' &&
				phone.length > 0) {
				phoneNumber = phone;
			}
		});
		return phoneNumber;
	}

}
