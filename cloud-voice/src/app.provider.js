routing.$inject = ['$urlRouterProvider', '$httpProvider', '$mdDateLocaleProvider', '$mdThemingProvider', 'moment'];

export default function routing($urlRouterProvider, $httpProvider, $mdDateLocaleProvider, $mdThemingProvider, moment) {

	/**
	 * ルーター
	 */
	$urlRouterProvider.otherwise('/');

	/**
	 * HTTP
	 */
	$httpProvider.defaults.headers.post['Content-Type'] = 'application/x-www-form-urlencoded; charset=UTF-8';
	$httpProvider.defaults.headers.common['App'] = 'app-' + new Date().getTime();
	$httpProvider.defaults.xsrfCookieName = 'APP-TOKEN';
	$httpProvider.defaults.xsrfHeaderName = 'X-CSRF-TOKEN';

	/**
	 * カレンダー
	 */
	$mdDateLocaleProvider.months = ['1月', '2月', '3月', '4月', '5月', '6月', '7月', '8月', '9月', '10月', '11月', '12月'];
	$mdDateLocaleProvider.shortMonths = ['1月', '2月', '3月', '4月', '5月', '6月', '7月', '8月', '9月', '10月', '11月', '12月'];
	$mdDateLocaleProvider.days = ['日曜日', '月曜日', '火曜日', '水曜日', '木曜日', '金曜日', '土曜日'];
	$mdDateLocaleProvider.shortDays = ['日', '月', '火', '水', '木', '金', '土'];
	$mdDateLocaleProvider.firstDayOfWeek = 0;
	$mdDateLocaleProvider.parseDate = function(dateString) {
		var m = moment(dateString, 'YYYY/MM/DD', true);
		return m.isValid() ? m.toDate() : new Date(NaN);
	};
	$mdDateLocaleProvider.formatDate = function(date) {
		if (date === undefined) {
			return null;
		}
		return moment(date).format('YYYY/MM/DD');
	};
	$mdDateLocaleProvider.monthHeaderFormatter = function(date) {
		return date.getFullYear() + '年 ' + (date.getMonth() + 1) + '月';
	};

	/**
	 * テーマ
	 */
	let customBlueMap = $mdThemingProvider.extendPalette('light-blue', {
		'contrastDefaultColor': 'light',
		'contrastDarkColors': ['50'],
		'50': 'ffffff'
	});
	$mdThemingProvider.definePalette('customBlue', customBlueMap);
	$mdThemingProvider.theme('default')
		.primaryPalette('customBlue', {
			'default': '500',
			'hue-1': '50'
		})
		.accentPalette('pink');
	$mdThemingProvider.theme('input', 'default')
		.primaryPalette('grey')
}
