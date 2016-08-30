Auth.$inject = ['$http', '$location', '$cookies', '$mdDialog'];

export default function Auth($http, $location, $cookies, $mdDialog) {
	/**
	 * JSON文字列のURL情報をJSONに復元する
	 */
	var getAcceptUrls = function (callback) {
		var jsonUrls = $cookies.get('CALLWELL_SHARE_ACCEPT_URLS');
		if (jsonUrls !== undefined) {
			return JSON.parse(jsonUrls);
		}
		return undefined;
	};

	/**
	 * CookieにURL情報を保持する
	 * @param staff
	 * @param group
	 */
	var putHashStatus = function (hash) {
		var urls = getAcceptUrls();
		if (urls === undefined) {
			urls = {};
		}
		urls[hash] = true;

		// クッキーの有効期限を1ヵ月後に設定
		var expire = new Date();
		expire.setMonth(expire.getMonth() + 1);

		$cookies.put('CALLWELL_SHARE_ACCEPT_URLS', JSON.stringify(urls), {
			expires: expire,
			path: '/'
		});
	};

	/**
	 * CookieのURL情報を削除する
	 */
	var removeUrl = function (hash) {
		var urls = getAcceptUrls();
		if (urls !== undefined && urls[hash]) {
			delete urls[hash];
		}

		// クッキーの有効期限を1ヵ月後に設定
		var expire = new Date();
		expire.setMonth(expire.getMonth() + 1);

		$cookies.put('CALLWELL_SHARE_ACCEPT_URLS', JSON.stringify(urls), {
			expires: expire
		});
	};

	return {
		/**
		 * URL受け入れ
		 */
		accept: function (hash, password) {
			$http.post('api/v1/share/accept.json', {
				hash: hash,
				password: password
			})
				.success(function (result) {
					if (result.result === 'ok') {
						putHashStatus(hash);

						// 希望URLがあり、同ハッシュなら遷移
						//  ※ハッシュが異なる場合、遷移先ハッシュの認証が通っていないとpassword画面へ戻されてしまうため
						var hopeUrl = $cookies.get('CALLWELL_SHARE_HOPE_URL');
						$cookies.remove('CALLWELL_SHARE_HOPE_URL', {path: '/'});
						if (hopeUrl && hopeUrl.indexOf(hash) !== -1) {
							$location.path(hopeUrl);
						} else {
							$location.path('/customer/hearing/' + hash + '/');
						}
					} else {
						$mdDialog.show(
							$mdDialog.alert()
								.textContent('URLまたはパスワードが間違っています。')
								.ok('閉じる')
						);
					}
				})
				.error(function (result) {
				});
		},

		/**
		 * 確認
		 */
		isAccept: function (hash) {
			// 元URLの取得
			$cookies.put('CALLWELL_SHARE_HOPE_URL', $location.url());

			var urls = getAcceptUrls();
			if (urls == null || urls[hash] !== true) {
				$location.path('/customer/password/' + hash);
				return false;
			}

			return $http.post('api/v1/share/is_accept.json', {
				hash: hash
			})
				.success(function (result) {
					if (result.result === 'ok') {
						return true;
					} else {
						removeUrl(hash);
						$location.path('/customer/password/' + hash);
						return false;
					}
				})
				.error(function (result) {
					removeUrl(hash);
					$location.path('/customer/password/' + hash);
					return false;
				});
		},
		getAcceptUrls: getAcceptUrls
	};
}
