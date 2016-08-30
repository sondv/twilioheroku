running.$inject = ['$rootScope', '$templateCache', '$location', '$state', 'manageServicesTwilio', 'manageServicesAuth'];

export default function running($rootScope, $templateCache, $location, $state, manageServicesTwilio, manageServicesAuth) {
    $rootScope.$on('$stateChangeStart', function(event, next, current) {
        // テンプレートキャッシュクリア
        $templateCache.remove(current.templateUrl);

        // ログイン判定
        if (next.url.indexOf('/') !== -1 && next.controller !== 'LoginFormCtrl') {
            manageServicesAuth.isLogged();

            if (manageServicesTwilio.hasSetupClient === false) {
                manageServicesTwilio.setupClient();
            } else {
                manageServicesTwilio.refreshToken();
            }
        }
        // if (next.url.indexOf('/share/') !== -1 && next.controller !== 'urlPasswordCtrl') {
        //     // manageServicesAuth.isAccept();
        // }
    });
    $rootScope.$on('$stateChangeSuccess', function() {
        $templateCache.removeAll();
    });
    $rootScope.$on('$viewContentLoaded', function() {
        $templateCache.removeAll();
    });
}
