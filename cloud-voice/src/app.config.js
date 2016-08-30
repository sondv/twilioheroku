routing.$inject = ['$urlRouterProvider', '$httpProvider', '$mdDateLocaleProvider', '$mdThemingProvider'];

export default function routing($urlRouterProvider, $httpProvider, $mdDateLocaleProvider, $mdThemingProvider) {
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

    //$mdDateLocaleProvider.formatDate = function(date) {
    //	return moment(date).format('YYYY/MM/DD');
    //};

    // // 最初は otherwise な LoginCtrl のルートしかない
    // $urlRouterProvider.otherwise({
    //     // templateUrl: 'view/login.html',
    //     // controller: 'LoginCtrl',
    //     resolve: {
    //         login: function($route, manageLoginService) {
    //             if (manageLoginService.isLogged()) {
    //                 // ログインに成功したらルートを追加＆上書きする
    //                 $urlRouterProvider.when('/', {
    //                     template: '<h1>home</h1>',
    //                     controller: function() {}
    //                 });
    //                 $urlRouterProvider.when('/page1', {
    //                     template: '<h1>page1</h1>',
    //                     controller: function() {}
    //                 });
    //                 $urlRouterProvider.when('/page2', {
    //                     template: '<h1>page2</h1>',
    //                     controller: function() {}
    //                 });
    //                 $urlRouterProvider.otherwise({
    //                     redirectTo: '/'
    //                 });
    //                 $route.reload();
    //             }
    //         }
    //     }
    // });

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
