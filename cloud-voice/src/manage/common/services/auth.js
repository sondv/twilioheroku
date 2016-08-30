Auth.$inject = ['$http', '$location', '$cookies', '$mdDialog', 'manageServicesTwilio'];

export default function Auth($http, $location, $cookies, $mdDialog, manageServicesTwilio) {
    /**
     * Cookieにユーザ情報を保持する
     * @param staff
     * @param group
     */
    var putStatus = function(staff) {
        // クッキーの有効期限を1ヵ月後に設定
        var expire = new Date();
        expire.setMonth(expire.getMonth() + 1);

        $cookies.put('CALLWELL_LOGIN_STAFF_JSON', JSON.stringify(staff), {
            expires: expire,
            path: '/'
        });
    };
    /**
     * Cookieのユーザ情報を削除する
     */
    var removeStatus = function() {
        $cookies.remove('CALLWELL_LOGIN_STAFF_JSON', {path: '/'});
    };
    return {
        /**
         * ログイン
         */
        login: function(login_id, password) {
            $http.post('api/v1/auth/login.json', {
                    login_id: login_id,
                    password: password
                })
                .success(function(result) {
                    if (result.result === 'ok') {
                        putStatus(result.staff);
                        manageServicesTwilio.refreshToken();
                        $location.path('/customer/');
                    } else {
                        console.log('not login');
                        console.log($mdDialog);
                        alert('ログイン出来ませんでした。');
                        $mdDialog.show(
                            $mdDialog.alert()
                            .textContent('ログイン出来ませんでした。')
                            .ok('閉じる')
                        );
                    }
                })
                .error(function(result) {
                    console.log(result);
                    console.log('error');
                });
        },
        /**
         * ログイン認証確認
         */
        isLogged: function() {
            var staff = this.getLoginStaff();
            if (staff === undefined) {
                $location.path('/auth/login/');
                return false;
            }

            $http.post('api/v1/auth/is_logged.json')
                .success(function(result) {
                    if (result.result === 'ok') {
                        return true;
                    } else {
                        removeStatus();
                        $location.path('/auth/login/');
                        return false;
                    }
                })
                .error(function(result) {
                    console.log(result);
                    console.log('error');
                    removeStatus();
                    $location.path('/auth/login/');
                    return false;
                });
        },
        /**
         * JSON文字列のスタッフをJSONに復元する
         */
        getLoginStaff: function(callback) {
            var jsonStaff = $cookies.get('CALLWELL_LOGIN_STAFF_JSON');
            if (jsonStaff !== undefined) {
                return JSON.parse(jsonStaff);
            }
            return undefined;
        },
        /**
         * ログアウト
         */
        logout: function() {
            $http.post('api/v1/auth/logout.json')
                .success(function(result) {})
                .error(function(result) {
                    console.log(result);
                    console.log('error');
                });
            removeStatus();
            $location.path('/auth/login/');
        }
    };
}
